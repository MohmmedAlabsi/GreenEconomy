<?php

namespace App\Http\Controllers\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\FieldVisit;
use App\Models\Consultation;
use App\Models\FeasibilityRequest;
use App\Models\FeasibilityStudy;
use App\Models\Plant;
use App\Models\PlantDisease;
use App\Models\DiseaseTreatment;
use App\Models\Region;
use App\Models\Category;
use App\Models\Specialization;
use App\Models\Draft;
use App\Models\PlatformSetting;

class FarmerDashboardController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $farmer */
        $farmer = User::find(Auth::id());

        if (!$farmer) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }

        $farmerId = $farmer->id;

        // 1. بيانات الملف الشخصي
        $userProfile = User::select('id', 'name', 'email', 'phone', 'region_id', 'district', 'role_id', 'created_at')
            ->find($farmerId);

        // 2. طلبات النزول الميداني
        $fieldVisits = FieldVisit::where('user_id', $farmerId)
            ->with([
                'engineer:id,name,phone',
                'attachments',
            ])
            ->latest()
            ->get();

        // 3. الاستشارات الزراعية
        $consultations = class_exists(Consultation::class)
            ? Consultation::where('user_id', $farmerId)->latest()->get()
            : [];

        // 4. طلبات دراسات الجدوى
        $feasibilityRequests = FeasibilityRequest::where('user_id', $farmerId)
            ->with([
                'category:id,name',
                'region:id,name',
            ])
            ->latest()
            ->get();

        // 5. دراسات الجدوى
        $feasibilityStudies = FeasibilityStudy::where(function ($query) use ($farmerId) {
                $query->where('status', 'نشط')
                      ->orWhere('status', 'active')
                      ->orWhere('user_id', $farmerId);
            })
            ->with([
                'category:id,name',
                'region:id,name',
                'user:id,name',
            ])
            ->latest()
            ->get();

        // ==========================================================
        // 6. الإشعارات: الوارد والصادر مع منع التكرار
        // ==========================================================
        $notificationsRaw = DB::table('notifications')->latest('created_at')->get();
        $notifications = collect();
        $outboxSeen = [];

        foreach ($notificationsRaw as $item) {
            $data = $this->decodeNotificationData($item->data);
            $notifiableId = (string) $item->notifiable_id;
            $senderId = $data['sender_id'] ?? null;

            $isInbox = $notifiableId === (string) $farmerId;
            $isOutboxFromThisFarmer = !$isInbox
                && $senderId !== null
                && (string) $senderId === (string) $farmerId;

            if (!$isInbox && !$isOutboxFromThisFarmer) {
                continue;
            }

            // منع التكرار في الصادر
            if ($isOutboxFromThisFarmer) {
                $title = $data['title'] ?? '';
                $body = $data['body'] ?? '';
                $actionUrl = $data['action_url'] ?? '';
                $groupKey = md5($title . '|' . $body . '|' . $actionUrl . '|' . substr((string)$item->created_at, 0, 16));

                if (isset($outboxSeen[$groupKey])) {
                    continue;
                }
                $outboxSeen[$groupKey] = true;
            }

            $notifications->push([
                'id'         => (string) $item->id,
                'title'      => $data['title'] ?? '',
                'body'       => $data['body'] ?? '',
                'priority'   => $data['priority'] ?? 'normal',
                'type'       => $data['type'] ?? 'general',
                'direction'  => $isInbox ? 'inbox' : 'outbox',
                'data'       => $data,
                'is_read'    => $isInbox ? ($item->read_at !== null) : true,
                'read_at'    => $item->read_at,
                'created_at' => $item->created_at,
            ]);
        }

        $notifications = $notifications->sortByDesc('created_at')->values();

        // 7. المسودات
        $drafts = Draft::where('user_id', $farmerId)->get();

        // 8. البيانات المرجعية المخزنة في الكاش
        $metaData = Cache::remember('farmer_dashboard_static_meta_v5', 7200, function () {
            $diseases = PlantDisease::where('farmer_visibility', 'مرئي للمزارعين')
                ->with([
                    'plants:id,common_name',
                    'treatments:id,disease_id,treatment_type,title,instructions',
                ])
                ->latest()
                ->get();

            return [
                'regions' => class_exists(Region::class)
                    ? Region::select('id', 'name')->orderBy('name')->get()
                    : [],
                'categories' => Category::select('id', 'name', 'slug', 'type')->orderBy('name')->get(),
                'specializations' => class_exists(Specialization::class)
                    ? Specialization::select('id', 'name')->orderBy('name')->get()
                    : [],
                'plants' => Plant::select(
                    'id',
                    'common_name',
                    'scientific_name',
                    'description',
                    'climate_requirements',
                    'irrigation_schedule',
                    'planting_season',
                    'image_url'
                )->orderBy('common_name')->get(),
                'plant_diseases' => $diseases,
                'disease_treatments' => DiseaseTreatment::all(),
            ];
        });

        // 9. بطاقات الملخص
        $summary = [
            'total_visits' => $fieldVisits->count(),
            'completed_visits' => $fieldVisits->whereIn('status', ['completed', 'rated'])->count(),
            'pending_visits' => $fieldVisits->whereIn('status', ['submitted', 'assigned', 'estimated'])->count(),
            'total_requests' => $fieldVisits->count() + $feasibilityRequests->count(),
            'unread_notifications' => $farmer->unreadNotifications()->count(),
        ];

        // 10. إعدادات المنصة وحالة الصيانة
        $settings = PlatformSetting::first();

        return response()->json([
            'status' => 'success',
            'summary' => $summary,
            'user' => $userProfile,
            'field_visits' => $fieldVisits,
            'consultations' => $consultations,
            'feasibility_requests' => $feasibilityRequests,
            'feasibility_studies' => $feasibilityStudies,
            'notifications' => $notifications,
            'drafts' => $drafts,
            'regions' => $metaData['regions'],
            'categories' => $metaData['categories'],
            'specializations' => $metaData['specializations'],
            'plants' => $metaData['plants'],
            'plant_diseases' => $metaData['plant_diseases'],
            'disease_treatments' => $metaData['disease_treatments'],
            'settings' => $settings,
        ]);
    }

    private function decodeNotificationData($data): array
    {
        if (is_array($data)) return $data;
        if (is_object($data)) return (array) $data;
        $decoded = json_decode((string) $data, true);
        return is_array($decoded) ? $decoded : [];
    }
}