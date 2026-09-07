<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FieldVisit;
use App\Models\FieldVisitReport;
use Illuminate\Routing\Controller;
use App\Models\Notification;
use App\Http\Requests\StoreFieldVisitRequest;
use App\Http\Requests\UpdateFieldVisitRequest;
use App\Http\Requests\AssignEngineerFieldVisitRequest;
use App\Http\Requests\SubmitEstimateFieldVisitRequest;
use App\Http\Requests\SubmitReportFieldVisitRequest;
use App\Http\Requests\SubmitRatingFieldVisitRequest;
use Illuminate\Support\Facades\Storage;

class FieldVisitController extends Controller
{
    public function index(Request $request)
    {
        $query = FieldVisit::with(['user', 'engineer', 'report', 'attachments'])->latest();

        if ($request->has('engineer_id')) {
            $query->where('engineer_id', $request->engineer_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $visits = $query->paginate(50);
        return response()->json($visits);
    }

    public function show($id)
    {
        $visit = FieldVisit::with(['user', 'engineer', 'report', 'attachments'])->findOrFail($id);
        return response()->json($visit);
    }

    /**
     * إنشاء طلب زيارة ميدانية جديد (المزارع)
     */
public function store(StoreFieldVisitRequest $request)
    {
        $validated = $request->validated();
        
        // تعيين القيم الافتراضية
        $validated['current_step'] = 1;
        $validated['status'] = $validated['status'] ?? 'submitted';

        // 1. إنشاء طلب النزول الميداني
        $visit = FieldVisit::create($validated);
        
        // 2. معالجة ورفع المرفقات (الصور وصورة الهوية) إلى Supabase وتخزينها في جدول attachments
        if ($request->hasFile('images') || $request->hasFile('id_card_image')) {
            $files = [];
            
            if ($request->hasFile('id_card_image')) {
                $files[] = $request->file('id_card_image');
            }
            
            if ($request->hasFile('images')) {
                $files = array_merge($files, $request->file('images'));
            }

            $baseUrl = rtrim(config('filesystems.disks.supabase.url'), '/');

            foreach ($files as $file) {
                if ($file->isValid()) {
                    $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
                    
                    // رفع الملف إلى حاوية Supabase (ضمن مجلد attachments أو field_visits)
                    $filePath = $file->storeAs('field_visits', $fileName, 'supabase');

                    // حفظ بيانات المرفق في جدول attachments باستخدام الهيكل المعتمد
                    \App\Models\Attachment::create([
                        'attachable_type' => FieldVisit::class,
                        'attachable_id'   => $visit->id,
                        'user_id'         => $visit->user_id,
                        'file_name'       => $file->getClientOriginalName(),
                        'file_path'       => $filePath,
                        'file_type'       => $file->getClientMimeType(),
                        'file_size'       => $file->getSize(),
                        'url'             => $baseUrl . '/' . $filePath,
                    ]);
                }
            }
        }

        // إرسال الإشعارات للإدمن والمزارع
        $admin = \App\Models\User::whereHas('role', function($q) {
            $q->where('name', 'Admin');
        })->first();

        if ($admin) {
            Notification::create([
                'audience' => 'specific',
                'user_id'  => $admin->id,
                'title'    => 'طلب نزول ميداني جديد',
                'body'     => 'قام المزارع ' . $visit->contact_name . ' بطلب نزول ميداني جديد رقم #' . $visit->id,
                'priority' => 'normal',
            ]);
        }

        if ($visit->user_id) {
            Notification::create([
                'audience' => 'specific',
                'user_id'  => $visit->user_id,
                'title'    => 'تم استلام طلبك بنجاح',
                'body'     => 'تم استلام طلب النزول الميداني رقم #' . $visit->id . ' وجاري مراجعته من قبل الإدارة.',
                'priority' => 'normal',
            ]);
        }

        return response()->json([
            'message' => 'Field visit created successfully with attachments',
            'data'    => $visit->load(['user', 'attachments'])
        ], 201);
    }

    public function update(UpdateFieldVisitRequest $request, string $id)
    {
        $visit = FieldVisit::findOrFail($id);
        $data = $request->validated();

        // عند اعتذار المهندس: تفريغ المهندس، إعادة الخطوة، وإشعار الإدارة تلقائياً
        if (isset($data['status']) && $data['status'] === 'rejected') {
            $data['engineer_id'] = null;
            $data['current_step'] = 2;

            $admin = \App\Models\User::whereHas('role', function($q) {
                $q->where('name', 'Admin');
            })->first();

            if ($admin) {
                Notification::create([
                    'audience' => 'specific',
                    'user_id'  => $admin->id,
                    'title'    => 'اعتذار مهندس عن مهمة نزول',
                    'body'     => 'اعتذر المهندس عن تنفيذ طلب النزول رقم #' . $visit->id . '، ويتطلب الطلب إعادة إسناد.',
                    'priority' => 'high',
                ]);
            }
        }

        $visit->update($data);

        return response()->json([
            'message' => 'Field visit updated successfully',
            'data'    => $visit->load(['user', 'engineer', 'report', 'attachments'])
        ]);
    }

    public function destroy(string $id)
    {
        // جلب الزيارة مع المرفقات والمستخدم
        $visit = FieldVisit::with(['user', 'attachments'])->findOrFail($id);

        // 1. حذف المرفقات المربوطة بالزيارة من Supabase Storage
        if ($visit->attachments && $visit->attachments->isNotEmpty()) {
            foreach ($visit->attachments as $attachment) {
                $path = $attachment->file_path ?? $attachment->path;
                if ($path) {
                    // إذا كان المسار رابطاً كاملاً، يتم استخراج المسار النسبي داخل الـ Bucket
                    $cleanPath = parse_url($path, PHP_URL_PATH);
                    $cleanPath = ltrim(preg_replace('#^/storage/v1/object/public/[^/]+/#', '', $cleanPath), '/');

                    Storage::disk('supabase')->delete($cleanPath ?: $path);
                }
                $attachment->delete();
            }
        }

        // 2. إذا كانت الصور مخزنة كحقل JSON أو مصفوفة روابط مباشرة داخل السجل (مثل images أو id_card_image)
        $directImages = array_filter([
            $visit->id_card_image ?? null,
            ...(is_array($visit->images ?? null) ? $visit->images : [])
        ]);

        foreach ($directImages as $imgUrl) {
            $cleanPath = parse_url($imgUrl, PHP_URL_PATH);
            $cleanPath = ltrim(preg_replace('#^/storage/v1/object/public/[^/]+/#', '', $cleanPath), '/');
            Storage::disk('supabase')->delete($cleanPath ?: $imgUrl);
        }

        // 3. البحث عن المدير لإرسال إشعار الإلغاء إليه
        $admin = \App\Models\User::whereHas('role', function($q) {
            $q->where('name', 'Admin');
        })->first();

        if ($admin) {
            $farmerName = $visit->contact_name ?? $visit->user?->name ?? 'المزارع';
            Notification::create([
                'audience' => 'specific',
                'user_id'  => $admin->id,
                'title'    => 'إلغاء طلب نزول ميداني',
                'body'     => 'قام المزارع ' . $farmerName . ' بإلغاء طلب النزول الميداني رقم #' . $visit->id,
                'priority' => 'high',
            ]);
        }

        // 4. حذف سجل الزيارة من قاعدة البيانات
        $visit->delete();

        return response()->json([
            'message' => 'Field visit and associated storage files deleted successfully'
        ]);
    }

    /**
     * تعيين مهندس زراعي للزيارة (المدير) -> المرحلة 3
     */
    public function assignEngineer(AssignEngineerFieldVisitRequest $request, $id)
    {
        $validated = $request->validated();
        $visit = FieldVisit::findOrFail($id);
        $engineerId = $validated['engineer_id'];

        $visit->update([
            'engineer_id'  => $engineerId,
            'current_step' => 3,
            'status'       => 'assigned',
        ]);

        // إشعار للمهندس
        Notification::create([
            'audience' => 'specific',
            'user_id'  => $engineerId,
            'title'    => 'اسناد مهمة نزول ميداني جديدة',
            'body'     => 'تم إسناد طلب النزول الميداني رقم #' . $visit->id . ' إليك لتنفيذه.',
            'priority' => 'high',
        ]);

        // إشعار للمزارع
        if ($visit->user_id) {
            Notification::create([
                'audience' => 'specific',
                'user_id'  => $visit->user_id,
                'title'    => 'تعيين مهندس لطلبك',
                'body'     => 'تم تعيين خبير ومستشار زراعي لمتابعة طلبك رقم #' . $visit->id,
                'priority' => 'normal',
            ]);
        }

        return response()->json([
            'message' => 'Engineer assigned successfully',
            'data'    => $visit->load(['user', 'engineer'])
        ]);
    }

    /**
     * تقديم التكلفة والموعد المقترح (المهندس) -> المرحلة 4
     */
    public function submitEstimate(SubmitEstimateFieldVisitRequest $request, $id)
    {
        $validated = $request->validated();
        $visit = FieldVisit::findOrFail($id);
        
        $admin = \App\Models\User::whereHas('role', function($q) {
            $q->where('name', 'Admin');
        })->first();

        $visit->update([
            'estimated_cost' => $validated['estimated_cost'],
            'scheduled_at'   => $validated['scheduled_at'],
            'current_step'   => 4,
            'status'         => 'estimated',
        ]);

        if ($admin) {
            Notification::create([
                'audience' => 'specific',
                'user_id'  => $admin->id,
                'title'    => 'تقديم تسعيرة وموعد نزول',
                'body'     => 'قام المهندس بتقديم التكلفة والوقت المقترح للطلب #' . $visit->id,
                'priority' => 'normal',
            ]);
        }

        if ($visit->user_id) {
            Notification::create([
                'audience' => 'specific',
                'user_id'  => $visit->user_id,
                'title'    => 'تم تحديد تكلفة وموعد الزيارة',
                'body'     => 'تم تحديد تكلفة النزول وموعد الزيارة لطلبك رقم #' . $visit->id . '، يرجى مراجعة التفاصيل والموافقة.',
                'priority' => 'high',
            ]);
        }

        return response()->json([
            'message' => 'Estimate submitted successfully',
            'data'    => $visit->load(['user', 'engineer'])
        ]);
    }

    /**
     * رفع التقرير الميداني وإغلاق الطلب (المهندس) -> المرحلة 8
     */
    public function submitReport(SubmitReportFieldVisitRequest $request, $id)
    {
        try {
            $visit = FieldVisit::findOrFail($id);
            $validated = $request->validated();
            
            $admin = \App\Models\User::whereHas('role', function($q) {
                $q->where('name', 'Admin');
            })->first();

            $fileUrl = null;

            // معالجة ورفع ملف التقرير إلى Supabase Storage
            if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
                
                // استخدام القرص العام 'public' أو 's3' أو 'supabase' بحسب إعداداتك الفعلية
                $filePath = $file->storeAs('visit_reports', $fileName, 'supabase');
                
                $baseUrl = rtrim(config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL'), '/');
                $fileUrl = $baseUrl . '/' . $filePath;
            }

            $engineerId = $visit->engineer_id ?? optional($request->user())->id ?? 1;

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

            if ($admin) {
                Notification::create([
                    'audience' => 'specific',
                    'user_id'  => $admin->id,
                    'title'    => 'تم إرفاق وإتمام تقرير النزول',
                    'body'     => 'أتم المهندس رفع التقرير الميداني للطلب رقم #' . $visit->id . ' بنجاح.',
                    'priority' => 'normal',
                ]);
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
    /**
     * تقييم الخدمة (المزارع) -> المرحلة 9
     */
    public function submitRating(SubmitRatingFieldVisitRequest $request, $id)
    {
        $validated = $request->validated();
        $visit = FieldVisit::findOrFail($id);
        
        $visit->update([
            'rating'         => $validated['rating'],
            'rating_comment' => $validated['rating_comment'] ?? null,
            'current_step'   => 9,
        ]);

        $admin = \App\Models\User::whereHas('role', function($q) {
            $q->where('name', 'Admin');
        })->first();
        $engineer = \App\Models\User::find($visit->engineer_id);

        if ($admin) {
            Notification::create([
                'audience' => 'specific',
                'user_id'  => $admin->id,
                'title'    => 'تقييم خدمة ومهندس جديد',
                'body'     => 'تم تقييم الخدمة والمهندس بـ ' . $validated['rating'] . ' نجوم للطلب #' . $visit->id,
                'priority' => 'normal',
            ]);
        }

        if ($engineer) {
            Notification::create([
                'audience' => 'specific',
                'user_id'  => $engineer->id,
                'title'    => 'تلقيت تقييماً جديداً',
                'body'     => 'حصلت على تقييم بـ ' . $validated['rating'] . ' نجوم في الطلب #' . $visit->id,
                'priority' => 'normal',
            ]);
        }

        return response()->json([
            'message' => 'Rating submitted successfully',
            'data'    => $visit
        ]);
    }
}