<?php

namespace App\Http\Controllers;

use App\Models\FieldVisitReport;
use App\Models\FieldVisit;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreFieldVisitReportRequest;

class FieldVisitReportController extends Controller
{
    public function index()
    {
        try {
            $reports = FieldVisitReport::with(['fieldVisit', 'engineer'])->latest()->get();
            return response()->json(['success' => true, 'data' => $reports], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'تعذر جلب التقارير',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreFieldVisitReportRequest $request, $id = null)
    {
        try {
            $validatedData = $request->validated();
            $visit = FieldVisit::find($validatedData['field_visit_id']);

            if (!$visit) {
                return response()->json([
                    'success' => false,
                    'message' => 'المهمة الميدانية المطلوبة غير موجودة'
                ], 404);
            }

            $engineerId = $visit->getAttribute('engineer_id') ?? auth()->id;

            $fileUrl = null;
            if ($request->hasFile('attachment')) {
                // الرفع إلى Supabase
                $filePath = $request->file('attachment')->store('visit_reports', 'supabase');
                $fileUrl = rtrim(config('filesystems.disks.supabase.url'), '/') . '/' . $filePath;
            }

            $report = FieldVisitReport::create([
                'field_visit_id'    => $visit->getKey(),
                'engineer_id'       => $engineerId,
                'diagnosis'         => $validatedData['diagnosis'],
                'recommendations'   => $validatedData['recommendations'],
                'prescribed_inputs' => $validatedData['prescribed_inputs'] ?? null,
                'notes'             => $validatedData['notes'] ?? null,
                // حفظ الرابط المباشر للمرفق
                'attachment'        => $fileUrl,
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