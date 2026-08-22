<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FieldVisit;
use App\Models\FieldVisitReport;
use Illuminate\Routing\Controller;

class FieldVisitController extends Controller
{
    /**
     * عرض قائمة الزيارات الميدانية مع دعم التصفية
     */
    public function index(Request $request)
    {
        $query = FieldVisit::with(['user', 'engineer', 'report', 'attachments'])->latest();

        // تصفية بحسب المهندس المكلف
        if ($request->has('engineer_id')) {
            $query->where('engineer_id', $request->engineer_id);
        }

        // تصفية بحسب المزارع صاحب الطلب
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // تصفية بحسب حالة الطلب
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $visits = $query->paginate(10);
        return response()->json($visits);
    }

    /**
     * عرض تفاصيل زيارة ميدانية معينة
     */
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
            'nearest_landmark'   => 'nullable|string|max:255',
            'crop_type'           => 'required|string|max:100',
            'area_size'           => 'required|numeric',
            'infestation_type'    => 'required|string|max:150',
            'priority_level'      => 'required|string|max:50',
            'problem_description' => 'required|string',
            'status'              => 'nullable|string|max:50',
            'scheduled_at'        => 'nullable|date',
            'estimated_cost'      => 'nullable|numeric',
        ]);

        // تعيين المرحلة الأولى افتراضياً
        $validated['current_step'] = 1;
        $validated['status'] = $validated['status'] ?? 'submitted';

        $visit = FieldVisit::create($validated);

        return response()->json([
            'message' => 'Field visit created successfully',
            'data'    => $visit->load(['user', 'attachments'])
        ], 201);
    }

    /**
     * تحديث بيانات الزيارة الميدانية
     */
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
            'nearest_landmark'   => 'nullable|string|max:255',
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

    /**
     * حذف الزيارة الميدانية
     */
    public function destroy(string $id)
    {
        $visit = FieldVisit::findOrFail($id);
        $visit->delete();

        return response()->json([
            'message' => 'Field visit deleted successfully'
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /*                         دوال تتبع مراحل الخدمة                             */
    /* -------------------------------------------------------------------------- */

    /**
     * تعيين مهندس زراعي للزيارة (المدير) -> المرحلة 3
     */
    public function assignEngineer(Request $request, $id)
    {
        $validated = $request->validate([
            'engineer_id' => 'required|exists:users,id',
        ]);

        $visit = FieldVisit::findOrFail($id);
        $visit->update([
            'engineer_id'  => $validated['engineer_id'],
            'current_step' => 3,
            'status'       => 'assigned',
        ]);

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
        $visit->update([
            'estimated_cost' => $validated['estimated_cost'],
            'scheduled_at'   => $validated['scheduled_at'],
            'current_step'   => 4,
            'status'         => 'estimated',
        ]);

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

        $validated = $request->validate([
            'diagnosis'         => 'required|string',
            'recommendations'   => 'required|string',
            'prescribed_inputs' => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        $report = FieldVisitReport::updateOrCreate(
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

        return response()->json([
            'message' => 'Rating submitted successfully',
            'data'    => $visit
        ]);
    }
}