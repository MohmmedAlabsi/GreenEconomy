<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FieldVisitReport;
use App\Models\FieldVisit;
use Illuminate\Support\Facades\Storage;

class FieldVisitReportController extends Controller
{
    /**
     * جلب قائمة كافة التقارير التشخيصية المسجلة
     */
    public function index(Request $request)
    {
        try {
            $reports = FieldVisitReport::with(['fieldVisit', 'engineer'])->latest()->get();

            return response()->json([
                'success' => true,
                'data' => $reports
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'تعذر جلب التقارير',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حفظ وتخزين تقرير زيارة ميدانية جديد مرتبط بالمهمة
     */
    public function store(Request $request, $id = null)
    {
        $visitId = $request->field_visit_id ?? $id;

        $request->merge(['field_visit_id' => $visitId]);

        $validatedData = $request->validate([
            'field_visit_id'    => 'required|exists:field_visits,id',
            'diagnosis'         => 'required|string',
            'recommendations'   => 'required|string',
            'prescribed_inputs' => 'nullable|string',
            'notes'             => 'nullable|string',
            'attachment'        => 'nullable|file|max:10240',
        ]);

        try {
            /** @var FieldVisit|null $visit */
            $visit = FieldVisit::find($visitId);

            if (!$visit) {
                return response()->json([
                    'success' => false,
                    'message' => 'المهمة الميدانية المطلوبة غير موجودة'
                ], 404);
            }

            // استخراج معرّف المهندس بطريقة تتجنب تحذيرات المحرر تماماً
            $engineerId = $visit->getAttribute('engineer_id') ?? auth()->id;

            $filePath = null;
            if ($request->hasFile('attachment')) {
                $filePath = $request->file('attachment')->store('visit_reports', 'public');
            }

            $report = FieldVisitReport::create([
                'field_visit_id'    => $visit->getKey(),
                'engineer_id'       => $engineerId,
                'diagnosis'         => $request->diagnosis,
                'recommendations'   => $request->recommendations,
                'prescribed_inputs' => $request->prescribed_inputs,
                'notes'             => $request->notes,
                'attachment'        => $filePath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ التقرير المرتبط بالمهمة بنجاح',
                'data'    => $report
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ التقرير في قاعدة البيانات',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}