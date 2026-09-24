<?php

namespace App\Services\FieldVisit;

use App\Models\Attachment;
use App\Models\FieldVisit;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class FieldVisitService
{
    public function __construct(private readonly SupabaseStorageService $storage) {}

    public function index(int $userId, array $filters = [])
    {
        $query = FieldVisit::query()->with(['user', 'engineer', 'attachments']);
        if (!isset($filters['all'])) $query->where('user_id', $userId);
        return $query->latest()->get();
    }

    public function find(string|int $id): FieldVisit
    {
        return FieldVisit::with(['user', 'engineer', 'report', 'attachments'])->findOrFail($id);
    }

    public function update(FieldVisit $visit, array $data, int $userId): FieldVisit
    {
        $files = $data['attachments'] ?? [];
        unset($data['attachments']);
        return DB::transaction(function () use ($visit, $data, $files) {
            $visit->update($data);
            foreach ((array) $files as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $path = $file->store('field_visits', 'supabase');
                    Attachment::create(['attachable_type' => FieldVisit::class, 'attachable_id' => $visit->id, 'user_id' => $visit->user_id, 'file_name' => $file->getClientOriginalName(), 'file_path' => $path, 'file_type' => $file->getClientMimeType(), 'file_size' => $file->getSize(), 'url' => $this->storage->url($path)]);
                }
            }
            return $visit->fresh(['user', 'engineer', 'report', 'attachments']);
        });
    }

    public function assignEngineer(FieldVisit $visit, int $engineerId): FieldVisit { $visit->update(['engineer_id' => $engineerId]); return $visit->fresh(['engineer']); }
    public function submitEstimate(FieldVisit $visit, array $data): FieldVisit { $visit->update($data + ['current_step' => 4]); return $visit->fresh(); }
    public function submitReport(FieldVisit $visit, array $data): FieldVisit { return $this->updateJourneyStep($visit, 8, $data['notes'] ?? null, $data['attachment'] ?? null); }
    public function submitRating(FieldVisit $visit, array $data): FieldVisit { $visit->update($data); return $visit->fresh(); }

    public function createVisit(array $data, int $userId): FieldVisit
    {
        $files = $data['attachments'] ?? [];
        unset($data['attachments'], $data['images'], $data['id_card_image']);
        $visit = DB::transaction(fn () => FieldVisit::create(array_merge($data, [
            'user_id' => $userId,
            'current_step' => 1,
            'status' => $data['status'] ?? 'submitted',
        ])));

        foreach ((array) $files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $path = $file->storeAs('field_visits', time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension(), 'supabase');
                Attachment::create([
                    'attachable_type' => FieldVisit::class,
                    'attachable_id' => $visit->id,
                    'user_id' => $userId,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'url' => $this->storage->url($path),
                ]);
            }
        }

        $farmer = User::find($userId);
        $admins = User::admins()->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new GeneralNotification([
                'title' => 'طلب نزول ميداني جديد',
                'body' => 'تم إنشاء طلب نزول ميداني رقم #'.$visit->id,
                'type' => 'field_visit',
                'sender_id' => $userId,
                'action_url' => '/admin/field-visits/'.$visit->id,
            ]));
        }
        if ($farmer) {
            $farmer->notify(new GeneralNotification([
                'title' => 'تم استلام طلبك بنجاح',
                'body' => 'تم استلام طلب النزول الميداني رقم #'.$visit->id,
                'type' => 'field_visit',
                'action_url' => '/farmer/my-requests/'.$visit->id,
            ]));
        }

        return $visit->load(['user', 'attachments']);
    }

    public function updateJourneyStep(FieldVisit $visit, int $step, ?string $reportText = null, ?UploadedFile $attachment = null): FieldVisit
    {
        abort_if($step < 1 || $step > 9, 422, 'Invalid journey step.');
        return DB::transaction(function () use ($visit, $step, $reportText, $attachment) {
            $visit->update(['current_step' => $step] + ($step >= 8 ? ['status' => 'completed'] : []));
            if ($reportText !== null || $attachment) {
                $path = $attachment?->store('visit_reports', 'supabase');
                $visit->report()->updateOrCreate([], [
                    'engineer_id' => $visit->engineer_id,
                    'notes' => $reportText,
                    'attachment' => $path ? $this->storage->url($path) : null,
                ]);
            }
            return $visit->load(['user', 'engineer', 'report', 'attachments']);
        });
    }

    public function destroy(FieldVisit $visit): bool
    {
        return DB::transaction(function () use ($visit) {
            foreach ($visit->attachments as $attachment) {
                if ($attachment->file_path) $this->storage->delete($attachment->file_path);
                $attachment->delete();
            }
            if ($visit->report?->attachment) $this->storage->delete($visit->report->attachment);
            return (bool) $visit->delete();
        });
    }
}
