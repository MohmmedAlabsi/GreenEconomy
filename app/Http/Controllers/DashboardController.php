<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FieldVisit;
use App\Models\FieldVisitReport;
use App\Models\Region;
use App\Models\Specialization;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function engineerData(Request $request)
    {
        $user = \App\Models\User::with('engineerProfile')->find(Auth::id());
        if (!$user) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }
        $userId = $user->id;

        $tasks = FieldVisit::where('engineer_id', $userId)
            ->with(['user:id,name,phone', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->get();

        $reports = FieldVisitReport::where('engineer_id', $userId)
            ->with(['fieldVisit:id,contact_name,crop_type'])
            ->orderBy('created_at', 'desc')
            ->get();

        // ==========================================================
        // الإشعارات: الوارد والصادر مع تجميع الصادر لمنع التكرار
        // ==========================================================
        $notificationsRaw = DB::table('notifications')->latest('created_at')->get();
        $notifications = collect();
        $outboxSeen = [];

        foreach ($notificationsRaw as $item) {
            $data = $this->decodeNotificationData($item->data);
            $notifiableId = (string) $item->notifiable_id;
            $senderId = $data['sender_id'] ?? null;

            $isInbox = $notifiableId === (string) $userId;
            $isOutboxFromThisEngineer = !$isInbox
                && $senderId !== null
                && (string) $senderId === (string) $userId;

            if (!$isInbox && !$isOutboxFromThisEngineer) {
                continue;
            }

            // تجميع الصادر الموجه لعدة مسؤولين لمنع تكرار الصفوف
            if ($isOutboxFromThisEngineer) {
                $title = $data['title'] ?? '';
                $body = $data['body'] ?? '';
                $actionUrl = $data['action_url'] ?? '';
                $groupKey = md5($title . '|' . $body . '|' . $actionUrl . '|' . substr((string) $item->created_at, 0, 16));

                if (isset($outboxSeen[$groupKey])) {
                    continue;
                }
                $outboxSeen[$groupKey] = true;
            }

            $notifications->push([
                'id'         => (string) $item->id,
                'title'      => $data['title'] ?? 'إشعار جديد',
                'body'       => $data['body'] ?? '',
                'priority'   => $data['priority'] ?? 'normal',
                'direction'  => $isInbox ? 'inbox' : 'outbox',
                'data'       => $data,
                'is_read'    => $isInbox ? ($item->read_at !== null) : true,
                'read_at'    => $item->read_at,
                'created_at' => $item->created_at,
            ]);
        }

        $notifications = $notifications->sortByDesc('created_at')->values();

        $regions = cache()->remember('regions_list', 3600, fn() => Region::all());
        $specializations = cache()->remember('specs_list', 3600, fn() => Specialization::all());

        // جلب إعدادات المنصة بما فيها حالة وضع الصيانة
        $settings = PlatformSetting::first();

        return response()->json([
            'user'            => $user,
            'tasks'           => $tasks,
            'reports'         => $reports,
            'notifications'   => $notifications,
            'regions'         => $regions,
            'specializations' => $specializations,
            'settings'        => $settings,
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