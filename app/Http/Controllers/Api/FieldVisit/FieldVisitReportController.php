<?php

namespace App\Http\Controllers\FieldVisit;
use Illuminate\Http\Request;
use App\Models\FieldVisitReport;
use App\Models\FieldVisit;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\FieldVisit\StoreFieldVisitReportRequest;

class FieldVisitReportController extends \App\Http\Controllers\Controller
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
            
            $visitId = $id ?? $validatedData['field_visit_id'] ?? null;
            $visit = FieldVisit::find($visitId);

            if (!$visit) {
                return response()->json([
                    'success' => false,
                    'message' => 'المهمة الميدانية المطلوبة غير موجودة'
                ], 404);
            }

            $currentUser = $request->user();
            $engineerId = $visit->engineer_id ?? $currentUser?->id;
            $engineer = User::find($engineerId) ?? $currentUser;
            $engName = $engineer?->name ?? 'المهندس';

            $fileUrl = null;
            if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                $filePath = $request->file('attachment')->store('visit_reports', 'supabase');
                $fileUrl = rtrim(config('filesystems.disks.supabase.url'), '/') . '/' . $filePath;
            }

            $report = FieldVisitReport::updateOrCreate(
                ['field_visit_id' => $visit->getKey()],
                [
                    'engineer_id'       => $engineerId,
                    'diagnosis'         => $validatedData['diagnosis'],
                    'recommendations'   => $validatedData['recommendations'],
                    'prescribed_inputs' => $validatedData['prescribed_inputs'] ?? null,
                    'notes'             => $validatedData['notes'] ?? null,
                    'attachment'        => $fileUrl,
                ]
            );

            $visit->update([
                'current_step' => 8,
                'status'       => 'completed',
            ]);

            $admins = User::where('role_id', 1)->orWhereHas('role', fn($q)=>$q->where('name', 'admin'))->get();
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