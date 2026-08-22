<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FieldVisit;
use App\Models\FieldVisitReport;
use Illuminate\Routing\Controller;
use App\Models\Notification;

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

        $visits = $query->paginate(10);
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'             => 'required|exists:users,id',
            'contact_name'        => 'required|string|max:255',
            'contact_phone'       => 'required|string|max:20',
            'governorate'         => 'required|string|max:100',
            'district'            => 'required|string|max:100',
            'village_or_area'     => 'required|string|max:255',
            'nearest_landmark'    => 'nullable|string|max:255',
            'crop_type'           => 'required|string|max:100',
            'area_size'           => 'required|numeric',
            'infestation_type'    => 'required|string|max:150',
            'priority_level'      => 'required|string|max:50',
            'problem_description' => 'required|string',
            'status'              => 'nullable|string|max:50',
            'scheduled_at'        => 'nullable|date',
            'estimated_cost'      => 'nullable|numeric',
        ]);

        $validated['current_step'] = 1;
        $validated['status'] = $validated['status'] ?? 'submitted';

        $visit = FieldVisit::create($validated);
        
        $admin = \App\Models\User::whereHas('role', function($q) {
            $q->where('name', 'Admin');
        })->first();

        // إشعار للأدمن بطلب جديد
        if ($admin) {
            \App\Models\Notification::create([
                'audience' => 'specific',
                'user_id'  => $admin->id,
                'title'    => 'طلب نزول ميداني جديد',
                'body'     => 'قام المزارع ' . $visit->contact_name . ' بطلب نزول ميداني جديد رقم #' . $visit->id,
                'priority' => 'normal',
            ]);
        }

        // إشعار للمزارع بتأكيد الاستلام
        if ($visit->user_id) {
            \App\Models\Notification::create([
                'audience' => 'specific',
                'user_id'  => $visit->user_id,
                'title'    => 'تم استلام طلبك بنجاح',
                'body'     => 'تم استلام طلب النزول الميداني رقم #' . $visit->id . ' وجاري مراجعته من قبل الإدارة.',
                'priority' => 'normal',
            ]);
        }

        return response()->json([
            'message' => 'Field visit created successfully',
            'data'    => $visit->load(['user', 'attachments'])
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $visit = FieldVisit::findOrFail($id);

        $validated = $request->validate([
            'user_id'             => 'sometimes|exists:users,id',
            'engineer_id'         => 'nullable|exists:users,id',
            'contact_name'        => 'sometimes|string|max:255',
            'contact_phone'       => 'sometimes|string|max:20',
            'governorate'         => 'sometimes|string|max:100',
            'district'            => 'sometimes|string|max:100',
            'village_or_area'     => 'sometimes|string|max:255',
            'nearest_landmark'    => 'nullable|string|max:255',
            'crop_type'           => 'sometimes|string|max:100',
            'area_size'           => 'sometimes|numeric',
            'infestation_type'    => 'sometimes|string|max:150',
            'priority_level'      => 'sometimes|string|max:50',
            'problem_description' => 'sometimes|string',
            'status'              => 'nullable|string|max:50',
            'current_step'        => 'nullable|integer|min:1|max:9',
            'scheduled_at'        => 'nullable|date',
            'estimated_cost'      => 'nullable|numeric',
            'rating'              => 'nullable|integer|min:1|max:5',
            'rating_comment'      => 'nullable|string',
        ]);

        $visit->update($validated);

        return response()->json([
            'message' => 'Field visit updated successfully',
            'data'    => $visit->load(['user', 'engineer', 'report', 'attachments'])
        ]);
    }

    public function destroy(string $id)
    {
        $visit = FieldVisit::findOrFail($id);
        $visit->delete();

        return response()->json([
            'message' => 'Field visit deleted successfully'
        ]);
    }

    /**
     * تعيين مهندس زراعي للزيارة (المدير) -> المرحلة 3
     */
    public function assignEngineer(Request $request, $id)
    {
        $validated = $request->validate([
            'engineer_id' => 'required|exists:users,id',
        ]);

        $visit = FieldVisit::findOrFail($id);
        $engineerId = $validated['engineer_id'];

        $visit->update([
            'engineer_id'  => $engineerId,
            'current_step' => 3,
            'status'       => 'assigned',
        ]);

        // إشعار للمهندس
        \App\Models\Notification::create([
            'audience' => 'specific',
            'user_id'  => $engineerId,
            'title'    => 'اسناد مهمة نزول ميداني جديدة',
            'body'     => 'تم إسناد طلب النزول الميداني رقم #' . $visit->id . ' إليك لتنفيذه.',
            'priority' => 'high',
        ]);

        // إشعار للمزارع
        if ($visit->user_id) {
            \App\Models\Notification::create([
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
    public function submitEstimate(Request $request, $id)
    {
        $validated = $request->validate([
            'estimated_cost' => 'required|numeric|min:0',
            'scheduled_at'   => 'required|date',
        ]);

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
            \App\Models\Notification::create([
                'audience' => 'specific',
                'user_id'  => $admin->id,
                'title'    => 'تقديم تسعيرة وموعد نزول',
                'body'     => 'قام المهندس بتقديم التكلفة والوقت المقترح للطلب #' . $visit->id,
                'priority' => 'normal',
            ]);
        }

        if ($visit->user_id) {
            \App\Models\Notification::create([
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
    public function submitReport(Request $request, $id)
    {
        $visit = FieldVisit::findOrFail($id);
        $admin = \App\Models\User::whereHas('role', function($q) {
            $q->where('name', 'Admin');
        })->first();

        $validated = $request->validate([
            'diagnosis'         => 'required|string',
            'recommendations'   => 'required|string',
            'prescribed_inputs' => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        FieldVisitReport::updateOrCreate(
            ['field_visit_id' => $visit->id],
            [
                'engineer_id'       => $visit->engineer_id ?? $request->user()->id,
                'diagnosis'         => $validated['diagnosis'],
                'recommendations'   => $validated['recommendations'],
                'prescribed_inputs' => $validated['prescribed_inputs'] ?? null,
                'notes'             => $validated['notes'] ?? null,
            ]
        );

        $visit->update([
            'current_step' => 8,
            'status'       => 'completed',
        ]);

        if ($admin) {
            \App\Models\Notification::create([
                'audience' => 'specific',
                'user_id'  => $admin->id,
                'title'    => 'تم إرفاق وإتمام تقرير النزول',
                'body'     => 'أتم المهندس رفع التقرير الميداني للطلب رقم #' . $visit->id . ' بنجاح.',
                'priority' => 'normal',
            ]);
        }

        return response()->json([
            'message' => 'Field visit report submitted successfully',
            'data'    => $visit->load(['user', 'engineer', 'report'])
        ]);
    }

    /**
     * تقييم الخدمة (المزارع) -> المرحلة 9
     */
    public function submitRating(Request $request, $id)
    {
        $validated = $request->validate([
            'rating'         => 'required|integer|min:1|max:5',
            'rating_comment' => 'nullable|string',
        ]);

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
            \App\Models\Notification::create([
                'audience' => 'specific',
                'user_id'  => $admin->id,
                'title'    => 'تقييم خدمة ومهندس جديد',
                'body'     => 'تم تقييم الخدمة والمهندس بـ ' . $validated['rating'] . ' نجوم للطلب #' . $visit->id,
                'priority' => 'normal',
            ]);
        }

        if ($engineer) {
            \App\Models\Notification::create([
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