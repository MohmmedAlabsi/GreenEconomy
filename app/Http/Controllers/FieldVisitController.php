<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FieldVisit;
use App\Models\FieldVisitReport;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Routing\Controller;
use App\Http\Requests\StoreFieldVisitRequest;
use App\Http\Requests\UpdateFieldVisitRequest;
use App\Http\Requests\AssignEngineerFieldVisitRequest;
use App\Http\Requests\SubmitEstimateFieldVisitRequest;
use App\Http\Requests\SubmitReportFieldVisitRequest;
use App\Http\Requests\SubmitRatingFieldVisitRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FieldVisitController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', FieldVisit::class);
        $user = $request->user();
        $query = FieldVisit::with(['user', 'engineer', 'report', 'attachments'])->latest();

        if ($user->hasPermission('visits.manage')) {
            if ($request->filled('engineer_id')) {
                $query->where('engineer_id', $request->engineer_id);
            }
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        } elseif ($user->hasPermission('visits.view-assigned')) {
            $query->where('engineer_id', $user->id);
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $visits = $query->paginate(50);
        return response()->json($visits);
    }

    public function show(Request $request, $id)
    {
        $visit = FieldVisit::with(['user', 'engineer', 'report', 'attachments'])->findOrFail($id);
        $this->authorize('view', $visit);
        return response()->json($visit);
    }

    public function store(StoreFieldVisitRequest $request)
    {
        $this->authorize('create', FieldVisit::class);
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;
        
        $validated['current_step'] = 1;
        $validated['status'] = $validated['status'] ?? 'submitted';

        $visit = FieldVisit::create($validated);
        
        if ($request->hasFile('images') || $request->hasFile('id_card_image')) {
            $files = [];
            
            if ($request->hasFile('id_card_image')) {
                $files[] = $request->file('id_card_image');
            }
            
            if ($request->hasFile('images')) {
                $files = array_merge($files, $request->file('images'));
            }

            $baseUrl = rtrim(config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL') ?? '', '/');

            foreach ($files as $file) {
                if ($file->isValid()) {
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('field_visits', $fileName, 'supabase');

                    \App\Models\Attachment::create([
                        'attachable_type' => FieldVisit::class,
                        'attachable_id'   => $visit->id,
                        'user_id'         => $visit->user_id,
                        'file_name'       => $file->getClientOriginalName(),
                        'file_path'       => $filePath,
                        'file_type'       => $file->getClientMimeType(),
                        'file_size'       => $file->getSize(),
                        'url'             => $baseUrl ? ($baseUrl . '/' . $filePath) : $filePath,
                    ]);
                }
            }
        }

        $farmer = User::find($visit->user_id);
        $farmerName = $visit->contact_name ?: ($farmer?->name ?? 'المزارع');

            $admins = User::admins()->get();       
            if ($admins->isNotEmpty()) {
            Notification::send($admins, new GeneralNotification([
                'title'       => 'طلب نزول ميداني جديد',
                'body'        => 'قام المزارع ' . $farmerName . ' بطلب نزول ميداني جديد رقم #' . $visit->id,
                'priority'    => 'normal',
                'type'        => 'field_visit',
                'sender_id'   => $visit->user_id,
                'sender_name' => $farmerName,
                'sender_role' => 'farmer',
                'action_url'  => '/admin/field-visits/' . $visit->id,
            ]));
        }

        if ($farmer) {
            $farmer->notify(new GeneralNotification([
                'title'      => 'تم استلام طلبك بنجاح',
                'body'       => 'تم استلام طلب النزول الميداني رقم #' . $visit->id . ' وجاري مراجعته من قبل الإدارة.',
                'priority'   => 'normal',
                'type'       => 'field_visit',
                'action_url' => '/farmer/my-requests/' . $visit->id,
            ]));
        }

        return response()->json([
            'message' => 'Field visit created successfully with attachments',
            'data'    => $visit->load(['user', 'attachments'])
        ], 201);
    }

    public function update(UpdateFieldVisitRequest $request, string $id)
    {
        $visit = FieldVisit::findOrFail($id);
        $this->authorize('update', $visit);
        $data = $request->validated();
        unset($data['user_id']);

        if (isset($data['status']) && $data['status'] === 'rejected') {
            $currentUser = $request->user();
            $engineer = User::find($visit->engineer_id) ?? $currentUser;
            $engName = $engineer?->name ?? 'المهندس';

            $data['engineer_id'] = null;
            $data['current_step'] = 2;

            $admins = User::admins()->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new GeneralNotification([
                    'title'       => 'اعتذار مهندس عن مهمة نزول',
                    'body'        => 'قام المهندس ' . $engName . ' بالاعتذار عن تنفيذ طلب النزول رقم #' . $visit->id,
                    'priority'    => 'high',
                    'type'        => 'field_visit',
                    'sender_id'   => $engineer?->id,
                    'sender_name' => $engName,
                    'sender_role' => 'engineer',
                    'action_url'  => '/admin/field-visits/' . $visit->id,
                ]));
            }
        }

        $visit->update($data);

        return response()->json([
            'message' => 'Field visit updated successfully',
            'data'    => $visit->load(['user', 'engineer', 'report', 'attachments'])
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $visit = FieldVisit::with(['user', 'attachments'])->findOrFail($id);
        $this->authorize('delete', $visit);
        $baseUrl = rtrim(config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL') ?? '', '/');

        if ($visit->attachments && $visit->attachments->isNotEmpty()) {
            foreach ($visit->attachments as $attachment) {
                $rawPath = $attachment->file_path ?? $attachment->url ?? $attachment->path;
                if ($rawPath) {
                    $cleanPath = str_replace($baseUrl . '/', '', $rawPath);
                    $cleanPath = ltrim($cleanPath, '/');
                    try {
                        Storage::disk('supabase')->delete($cleanPath);
                    } catch (\Throwable $e) {}
                }
                $attachment->delete();
            }
        }

        $directImages = array_filter([
            $visit->id_card_image ?? null,
            ...(is_array($visit->images ?? null) ? $visit->images : [])
        ]);

        foreach ($directImages as $imgUrl) {
            $cleanPath = str_replace($baseUrl . '/', '', $imgUrl);
            $cleanPath = ltrim($cleanPath, '/');
            try {
                Storage::disk('supabase')->delete($cleanPath);
            } catch (\Throwable $e) {}
        }

        $admins = User::admins()->get();
        if ($admins->isNotEmpty()) {
            $farmerName = $visit->contact_name ?? $visit->user?->name ?? 'المزارع';
            Notification::send($admins, new GeneralNotification([
                'title'       => 'إلغاء طلب نزول ميداني',
                'body'        => 'قام المزارع ' . $farmerName . ' بإلغ��ء طلب النزول الميداني رقم #' . $visit->id,
                'priority'    => 'high',
                'type'        => 'field_visit',
                'sender_id'   => $visit->user_id,
                'sender_name' => $farmerName,
                'sender_role' => 'farmer',
            ]));
        }

        $visit->delete();

        return response()->json([
            'message' => 'Field visit and associated storage files deleted successfully'
        ]);
    }

    public function assignEngineer(AssignEngineerFieldVisitRequest $request, $id)
    {
        $validated = $request->validated();
        $visit = FieldVisit::findOrFail($id);
        $this->authorize('updateStep', $visit);
        $engineerId = $validated['engineer_id'];

        $visit->update([
            'engineer_id'  => $engineerId,
            'current_step' => 3,
            'status'       => 'assigned',
        ]);

        $adminUser = $request->user();
        $adminId = $adminUser?->id;
        $adminName = $adminUser?->name ?? 'الإدارة';

        $engineer = User::find($engineerId);
        if ($engineer) {
            $engineer->notify(new GeneralNotification([
                'title'            => 'اسناد مهمة نزول ميداني جديدة',
                'body'             => 'تم إسناد طلب النزول الميداني رقم #' . $visit->id . ' إليك لتنفيذه.',
                'priority'         => 'high',
                'audience'         => 'specific',
                'sender_id'        => $adminId,
                'sender_name'      => $adminName,
                'sender_role'      => 'admin',
                'target_user_name' => $engineer->name,
                'target_user_role' => 'engineer',
                'type'             => 'field_visit',
                'action_url'       => '/engineer/tasks',
            ]));
        }

        $farmer = User::find($visit->user_id);
        if ($farmer) {
            $farmer->notify(new GeneralNotification([
                'title'            => 'تعيين مهندس لطلبك',
                'body'             => 'تم تعيين خبير ومستشار زراعي لمتابعة طلبك رقم #' . $visit->id,
                'priority'         => 'normal',
                'audience'         => 'specific',
                'sender_id'        => $adminId,
                'sender_name'      => $adminName,
                'sender_role'      => 'admin',
                'target_user_name' => $farmer->name,
                'target_user_role' => 'farmer',
                'type'             => 'field_visit',
                'action_url'       => '/farmer/my-requests/' . $visit->id,
            ]));
        }

        return response()->json([
            'message' => 'Engineer assigned successfully',
            'data'    => $visit->load(['user', 'engineer'])
        ]);
    }

    public function submitEstimate(SubmitEstimateFieldVisitRequest $request, $id)
    {
        $validated = $request->validated();
        $visit = FieldVisit::findOrFail($id);
        $this->authorize('updateStep', $visit);

        $visit->update([
            'estimated_cost' => $validated['estimated_cost'],
            'scheduled_at'   => $validated['scheduled_at'],
            'current_step'   => 4,
            'status'         => 'estimated',
        ]);

        $currentUser = $request->user();
        $engineer = User::find($visit->engineer_id) ?? $currentUser;
        $engName = $engineer?->name ?? 'المهندس';
        $engId = $engineer?->id ?? $currentUser?->id;

        $admins = User::admins()->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new GeneralNotification([
                'title'       => 'تقديم تسعيرة وموعد نزول',
                'body'        => 'قام المهندس ' . $engName . ' بتقديم التكلفة والوقت المقترح للطلب #' . $visit->id,
                'priority'    => 'normal',
                'type'        => 'field_visit',
                'sender_id'   => $engId,
                'sender_name' => $engName,
                'sender_role' => 'engineer',
                'action_url'  => '/admin/field-visits/' . $visit->id,
            ]));
        }

        $farmer = User::find($visit->user_id);
        if ($farmer) {
            $farmer->notify(new GeneralNotification([
                'title'       => 'تم تحديد تكلفة وموعد الزيارة',
                'body'        => 'قام المهندس ' . $engName . ' بتحديد تكلفة النزول وموعد الزيارة لطلبك رقم #' . $visit->id,
                'priority'    => 'high',
                'type'        => 'field_visit',
                'sender_id'   => $engId,
                'sender_name' => $engName,
                'sender_role' => 'engineer',
                'action_url'  => '/farmer/my-requests/' . $visit->id,
            ]));
        }

        return response()->json([
            'message' => 'Estimate submitted successfully',
            'data'    => $visit->load(['user', 'engineer'])
        ]);
    }

    public function submitReport(SubmitReportFieldVisitRequest $request, $id)
    {
        try {
            $visit = FieldVisit::findOrFail($id);
            $this->authorize('uploadReport', $visit);
            $validated = $request->validated();

            $fileUrl = null;

            if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('visit_reports', $fileName, 'supabase');
                
                $baseUrl = rtrim(config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL') ?? '', '/');
                $fileUrl = $baseUrl ? ($baseUrl . '/' . $filePath) : $filePath;
            }

            $currentUser = $request->user();
            $engineerId = $visit->engineer_id ?? $currentUser?->id;
            $engineer = User::find($engineerId) ?? $currentUser;
            $engName = $engineer?->name ?? 'المهندس';

            FieldVisitReport::updateOrCreate(
                ['field_visit_id' => $visit->id],
                [
                    'engineer_id'       => $engineerId,
                    'diagnosis'         => $validated['diagnosis'],
                    'recommendations'   => $validated['recommendations'],
                    'prescribed_inputs' => $validated['prescribed_inputs'] ?? null,
                    'notes'             => $validated['notes'] ?? null,
                    'attachment'        => $fileUrl,
                ]
            );

            $visit->update([
                'current_step' => 8,
                'status'       => 'completed',
            ]);

            $admins = User::admins()->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new GeneralNotification([
                    'title'       => 'تم إرفاق وإتمام تقرير النزول',
                    'body'        => 'أتم المهندس ' . $engName . ' رفع التقرير الميداني للطلب رقم #' . $visit->id . ' بنجاح.',
                    'priority'    => 'normal',
                    'type'        => 'field_visit',
                    'sender_id'   => $engineerId,
                    'sender_name' => $engName,
                    'sender_role' => 'engineer',
                    'action_url'  => '/admin/field-visits/' . $visit->id,
                ]));
            }

            $farmer = User::find($visit->user_id);
            if ($farmer) {
                $farmer->notify(new GeneralNotification([
                    'title'       => 'تقرير الزيارة الميدانية جاهز',
                    'body'        => 'أتم المهندس ' . $engName . ' إعداد التقرير التشخيصي والتوصيات لطلبك رقم #' . $visit->id,
                    'priority'    => 'normal',
                    'type'        => 'field_visit',
                    'sender_id'   => $engineerId,
                    'sender_name' => $engName,
                    'sender_role' => 'engineer',
                    'action_url'  => '/farmer/my-requests/' . $visit->id,
                ]));
            }

            return response()->json([
                'success' => true,
                'message' => 'Field visit report submitted successfully',
                'data'    => $visit->load(['user', 'engineer', 'report'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ التقرير',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function submitRating(SubmitRatingFieldVisitRequest $request, $id)
    {
        $validated = $request->validated();
        $visit = FieldVisit::findOrFail($id);
        $this->authorize('rate', $visit);
        
        $visit->update([
            'rating'         => $validated['rating'],
            'rating_comment' => $validated['rating_comment'] ?? null,
            'current_step'   => 9,
        ]);

        $farmer = User::find($visit->user_id);
        $farmerName = $farmer?->name ?? 'المزارع';

        $admins = User::admins()->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new GeneralNotification([
                'title'       => 'تقييم خدمة ومهندس جديد',
                'body'        => 'قام المزارع ' . $farmerName . ' بتقييم الخدمة بـ ' . $validated['rating'] . ' نجوم للطلب #' . $visit->id,
                'priority'    => 'normal',
                'type'        => 'field_visit',
                'sender_id'   => $visit->user_id,
                'sender_name' => $farmerName,
                'sender_role' => 'farmer',
            ]));
        }

        $engineer = User::find($visit->engineer_id);
        if ($engineer) {
            $engineer->notify(new GeneralNotification([
                'title'       => 'تلقيت تقييماً جديداً',
                'body'        => 'حصلت على تقييم بـ ' . $validated['rating'] . ' نجوم من المزارع ' . $farmerName . ' في الطلب #' . $visit->id,
                'priority'    => 'normal',
                'type'        => 'field_visit',
                'sender_id'   => $visit->user_id,
                'sender_name' => $farmerName,
                'sender_role' => 'farmer',
            ]));
        }

        return response()->json([
            'message' => 'Rating submitted successfully',
            'data'    => $visit
        ]);
    }
}
