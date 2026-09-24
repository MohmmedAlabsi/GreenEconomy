<?php

namespace App\Services\Engineer;

use App\Models\EngineerProfile;
use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class EngineerProfileService
{
    public function __construct(private readonly SupabaseStorageService $storage) {}

    public function query() { return EngineerProfile::query()->with(['user.region', 'specialization']); }
    public function find(string|int $id): EngineerProfile { return $this->query()->findOrFail($id); }
    public function findForUserOrFail(string|int $id): EngineerProfile { return $this->query()->where('user_id', $id)->firstOrFail(); }
    public function save(int $userId, array $data, ?UploadedFile $cv = null, ?UploadedFile $avatar = null): EngineerProfile { $profile = EngineerProfile::firstOrNew(['user_id' => $userId]); return $this->update($profile, $data, $cv, null); }
    public function delete(EngineerProfile $profile): bool { return DB::transaction(fn () => (bool) $profile->delete()); }

    public function queryForUser(int $userId)
    {
        return EngineerProfile::query()->where('user_id', $userId);
    }

    public function update(EngineerProfile $profile, array $data, ?UploadedFile $cv = null, ?UploadedFile $certificate = null): EngineerProfile
    {
        return DB::transaction(function () use ($profile, $data, $cv, $certificate) {
            unset($data['user_id']);
            foreach (['cv' => $cv, 'certificate' => $certificate] as $field => $file) {
                if (!$file?->isValid()) continue;
                $old = $profile->{$field.'_url'} ?? $profile->{$field};
                if ($old) $this->storage->delete($old);
                $path = $file->store('engineers/'.$profile->user_id, 'supabase');
                $data[$field.'_url'] = $this->storage->url($path);
                unset($data[$field]);
            }
            $profile->update($data);
            return $profile->fresh(['user', 'specialization']);
        });
    }

    public function approve(EngineerProfile $profile, bool $approved): EngineerProfile
    {
        return DB::transaction(function () use ($profile, $approved) {
            $profile->update(['is_verified' => $approved]);
            return $profile->fresh(['user', 'specialization']);
        });
    }
}
