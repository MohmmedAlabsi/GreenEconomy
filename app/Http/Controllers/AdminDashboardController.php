<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FeasibilityStudy;
use App\Models\PlantDisease;
use App\Models\Plant;
use App\Models\DiseaseTreatment;
use App\Models\FieldVisit;
use App\Models\FieldVisitReport;
use App\Models\PlatformSetting;
use App\Models\Region;
use App\Models\Category;
use App\Models\Specialization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $users = User::with([
            'role',
            'roles',
            'region',
            'engineerProfile.specialization',
        ])->latest()->get();

        $feasibilityStudies = FeasibilityStudy::with([
            'category',
            'region',
            'user',
        ])->latest()->get();

        $feasibilityRequests = DB::getSchemaBuilder()->hasTable('feasibility_requests')
            ? DB::table('feasibility_requests')->get()
            : collect();

        $plantDiseases = PlantDisease::with(['plants'])->latest()->get();
        $plants = Plant::latest()->get();

        $treatments = DiseaseTreatment::select(
            'id',
            'disease_id',
            'treatment_type',
            'title',
            'instructions',
            'created_at',
            'updated_at'
        )->with(['disease'])->latest()->get();

        $fieldVisits = FieldVisit::with(['user', 'engineer'])->latest()->get();
        $fieldVisitReports = FieldVisitReport::with(['engineer', 'fieldVisit'])->latest()->get();

        $engineerJoinRequests = DB::getSchemaBuilder()->hasTable('engineer_join_requests')
            ? DB::table('engineer_join_requests')->latest()->get()
            : collect();

        $adminId = Auth::id();

        $usersById = $users->keyBy(fn ($user) => (string) $user->id);
        $fieldVisitsById = $fieldVisits->keyBy(fn ($visit) => (string) $visit->id);

        $notificationsRaw = DB::table('notifications')
            ->latest('created_at')
            ->get();

        $inboxNotifications = collect();
        $outboxRows = collect();

        foreach ($notificationsRaw as $item) {
            $payload = $this->decodeNotificationData($item->data);
            $title = trim((string) ($payload['title'] ?? 'إشعار'));
            $body = trim((string) ($payload['body'] ?? ''));
            $priority = $this->normalizePriority($payload['priority'] ?? 'normal');
            $notifiableId = (string) $item->notifiable_id;

            // ==========================================
            // 1. الوارد (INBOX): المستلم هو الأدمن الحالي
            //    (هذا الجزء يعمل بامتياز ولم يتم المساس بمنطقه إطلاقاً)
            // ==========================================
            if ($notifiableId === (string) $adminId) {
                $sender = $this->resolveInboxSender(
                    $payload,
                    $title,
                    $body,
                    $usersById,
                    $fieldVisitsById
                );

                $inboxNotifications->push([
                    'id'             => (string) $item->id,
                    'title'          => $title,
                    'body'           => $body,
                    'priority'       => $priority,
                    'direction'      => 'inbox',
                    'category'       => $sender['role'] === 'farmer' ? 'farmers' : 'engineers',
                    'badge_label'    => $sender['name'],
                    'badge_type'     => $sender['role'],
                    'role'           => $sender['role'],
                    'is_broadcast'   => false,
                    'is_read'        => $item->read_at !== null,
                    'recipient_read' => null,
                    'created_at'     => $item->created_at,
                ]);

                continue;
            }

            // ==========================================
            // 2. الصادر (OUTBOX): ما أرسلته الإدارة فعلياً عبر نموذج الإرسال
            // ==========================================
            // المعيار الوحيد المعتمد لاعتبار الإشعار "صادراً من الأدمن" هو أن يحمل
            // بصمة نموذج الإرسال الإداري (NotificationController@send) صراحة داخل
            // حمولة البيانات (data payload)، وليس تخمين محتوى النص.
            //
            // أي إشعار من دورات عمل النزول الميداني (تسعيرة، تقرير، استلام...) بين
            // المهندس والمزارع لا يحمل هذه البصمة أبداً، لذلك يُستبعد تلقائياً وبدقة
            // دون الحاجة لمطابقة كلمات مفتاحية هشة.
            if (! $this->isAdminSentNotification($payload)) {
                continue;
            }

            $audience = $this->normalizeAudience($payload['audience'] ?? null);

            $outboxRows->push([
                'notification' => $item,
                'payload'      => $payload,
                'title'        => $title,
                'body'         => $body,
                'priority'     => $priority,
                'audience'     => $audience,
            ]);
        }

        // ==========================================
        // تجميع الصادر الجماعي لمنع تكرار آلاف الصفوف لنفس الحملة
        // ==========================================
        $outboxNotifications = collect();
        $broadcastSeen = [];

        foreach ($outboxRows as $row) {
            $item = $row['notification'];
            $payload = $row['payload'];
            $title = $row['title'];
            $body = $row['body'];
            $priority = $row['priority'];
            $audience = $row['audience'];

            $isBroadcast = in_array($audience, ['all', 'farmers', 'engineers', 'admins'], true);

            if ($isBroadcast) {
                // مفتاح التجميع: نفس العنوان + نفس النص + نفس الفئة المستهدفة + نفس
                // دقيقة الإرسال (كل مستلمي نفس الحملة الجماعية يُنشأون بنفس اللحظة
                // تقريباً عبر notify() على مجموعة مستخدمين).
                $campaignKey = md5(
                    $title . '|' . $body . '|' . $audience . '|' . substr((string) $item->created_at, 0, 16)
                );

                if (isset($broadcastSeen[$campaignKey])) {
                    continue; // منع التكرار: هذا السطر تابع لحملة تم عرضها مسبقاً
                }
                $broadcastSeen[$campaignKey] = true;

                [$targetCategory, $label] = match ($audience) {
                    'farmers'   => ['farmers', 'كل المزارعين'],
                    'engineers' => ['engineers', 'كل المهندسين'],
                    'admins'    => ['all', 'كل الإداريين'],
                    default     => ['all', 'الجميع'],
                };

                $outboxNotifications->push([
                    'id'             => (string) $item->id,
                    'title'          => $title,
                    'body'           => $body,
                    'priority'       => $priority,
                    'direction'      => 'outbox',
                    'category'       => $targetCategory,
                    'badge_label'    => $label,
                    'badge_type'     => 'broadcast',
                    'role'           => null,
                    'is_broadcast'   => true,
                    'is_read'        => true,
                    'recipient_read' => null,
                    'created_at'     => $item->created_at,
                ]);

                continue;
            }

            // ==========================================
            // إشعار فردي صادر لمستخدم محدد (audience === 'specific')
            // ==========================================
            $recipientId = (string) $item->notifiable_id;
            $recipient = $usersById->get($recipientId);

            if ($recipient) {
                $role = $this->resolveUserRole($recipient);
                $name = (string) $recipient->name;
            } else {
                // fallback: المستخدم قد يكون محذوفاً، نعتمد على ما تم حفظه وقت الإرسال
                $role = ($payload['target_user_role'] ?? null) === 'engineer' ? 'engineer' : 'farmer';
                $name = (string) ($payload['target_user_name'] ?? 'مستخدم محدد');
            }

            $outboxNotifications->push([
                'id'             => (string) $item->id,
                'title'          => $title,
                'body'           => $body,
                'priority'       => $priority,
                'direction'      => 'outbox',
                'category'       => $role === 'engineer' ? 'engineers' : 'farmers',
                'badge_label'    => $name,
                'badge_type'     => $role,
                'role'           => $role,
                'is_broadcast'   => false,
                'is_read'        => true,
                'recipient_read' => $item->read_at !== null,
                'created_at'     => $item->created_at,
            ]);
        }

        $notifications = $inboxNotifications
            ->concat($outboxNotifications)
            ->sortByDesc('created_at')
            ->values();

        $settings = PlatformSetting::first();
        $regions = Region::all();
        $categories = Category::all();
        $specializations = Specialization::all();

        $stats = [
            'total_users' => User::count(),
            'total_engineers' => User::whereHas('role', fn ($q) => $q->where('name', 'engineer'))
                ->orWhereHas('roles', fn ($q) => $q->where('name', 'engineer'))->count(),
            'total_farmers' => User::whereHas('role', fn ($q) => $q->where('name', 'farmer'))
                ->orWhereHas('roles', fn ($q) => $q->where('name', 'farmer'))->count(),
            'total_studies' => FeasibilityStudy::count(),
            'total_diseases' => PlantDisease::count(),
            'total_reports' => FieldVisitReport::count(),
        ];

        return response()->json([
            'users' => $users,
            'feasibility_studies' => $feasibilityStudies,
            'feasibility_requests' => $feasibilityRequests,
            'plant_diseases' => $plantDiseases,
            'plants' => $plants,
            'disease_treatments' => $treatments,
            'field_visits' => $fieldVisits,
            'field_visit_reports' => $fieldVisitReports,
            'engineer_join_requests' => $engineerJoinRequests,
            'notifications' => $notifications,
            'settings' => $settings,
            'regions' => $regions,
            'categories' => $categories,
            'specializations' => $specializations,
            'stats' => $stats,
        ]);
    }

    private function decodeNotificationData($data): array
    {
        if (is_array($data)) return $data;
        if (is_object($data)) return (array) $data;
        $decoded = json_decode((string) $data, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function normalizePriority($priority): string
    {
        $priority = strtolower(trim((string) $priority));
        return in_array($priority, ['low', 'normal', 'high', 'urgent'], true) ? $priority : 'normal';
    }

    private function normalizeAudience($audience): string
    {
        $audience = strtolower(trim((string) $audience));
        return in_array($audience, ['all', 'farmers', 'engineers', 'admins', 'specific'], true) ? $audience : 'specific';
    }

    /**
     * المعيار الحصري لتحديد أن الإشعار "صادر فعلياً من الإدارة":
     * يجب أن يحمل بصمة نموذج الإرسال الإداري (NotificationController@send) وهي
     * وجود مفتاح audience صريح ضمن حمولة البيانات، إضافة إلى sender_role = admin.
     *
     * يعتمد هذا الشرط على أن GeneralNotification::toDatabase() يحفظ فعلياً كل
     * من audience و sender_role ضمن عمود data (تم إصلاح ذلك؛ كانت هذه الحقول
     * تُسقط بالكامل قبل الحفظ رغم أن NotificationController@send كان يمررها).
     */
    private function isAdminSentNotification(array $payload): bool
    {
        $hasAudienceKey = array_key_exists('audience', $payload)
            && in_array(
                strtolower(trim((string) $payload['audience'])),
                ['all', 'farmers', 'engineers', 'admins', 'specific'],
                true
            );

        $hasAdminSenderRole = strtolower(trim((string) ($payload['sender_role'] ?? ''))) === 'admin';

        return $hasAudienceKey && $hasAdminSenderRole;
    }

    private function resolveUserRole($user): string
    {
        if (!$user) return 'farmer';
        $roleName = strtolower((string) ($user->role?->name ?? ''));
        if (str_contains($roleName, 'engineer') || (string)$user->role_id === '3') {
            return 'engineer';
        }
        return 'farmer';
    }

    private function resolveInboxSender(array $payload, string $title, string $body, $usersById, $fieldVisitsById): array
    {
        $text = $title . ' ' . $body;

        // 1. فحص هل الإشعار خاص بمهندس
        $isEng = str_contains($title, 'تسعيرة') ||
                 str_contains($title, 'تقرير') ||
                 str_contains($title, 'اعتذار') ||
                 str_contains($body, 'المهندس');

        $role = $isEng ? 'engineer' : 'farmer';

        // 2. إذا وجد sender_id مباشر
        $senderId = $payload['sender_id'] ?? $payload['user_id'] ?? null;
        if ($senderId && $usersById->has((string) $senderId)) {
            return [
                'name' => (string) $usersById->get((string) $senderId)->name,
                'role' => $role
            ];
        }

        // 3. محاولة الربط برقم الزيارة الميدانية لطلبات المهندسين
        if (preg_match('/(?:طلب|للطلب|رقم)\s*#?(\d+)/u', $text, $matches)) {
            $visitId = $matches[1];
            if ($fieldVisitsById->has((string) $visitId)) {
                $visit = $fieldVisitsById->get((string) $visitId);
                if ($isEng && $visit->engineer_id && $usersById->has((string) $visit->engineer_id)) {
                    return [
                        'name' => (string) $usersById->get((string) $visit->engineer_id)->name,
                        'role' => 'engineer'
                    ];
                } elseif (!$isEng && $visit->user_id && $usersById->has((string) $visit->user_id)) {
                    return [
                        'name' => (string) $usersById->get((string) $visit->user_id)->name,
                        'role' => 'farmer'
                    ];
                }
            }
        }

        // 4. استخراج الاسم من النص
        if (preg_match('/قام\s+المزارع\s+([\p{L}\s]+?)(?:\s+بتقديم|\s+بطلب|ID|#|$)/u', $text, $matches)) {
            return ['name' => trim($matches[1]), 'role' => 'farmer'];
        }
        if (preg_match('/قام\s+المهندس\s+([\p{L}\s]+?)(?:\s+بتقديم|\s+بإرفاق|\s+بالاعتذار|ID|#|$)/u', $text, $matches)) {
            return ['name' => trim($matches[1]), 'role' => 'engineer'];
        }

        return [
            'name' => $isEng ? 'مهندس زراعي' : 'مزارع',
            'role' => $role
        ];
    }
}