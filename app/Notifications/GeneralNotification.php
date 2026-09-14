<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // الإرسال لقاعدة البيانات فقط
    }

    public function toDatabase(object $notifiable): array
    {
        // ==========================================================
        // مهم جداً: كل حقل هنا لازم يُحفظ كما هو داخل عمود data.
        // لوحة تحكم الأدمن (AdminDashboardController) تعتمد بشكل حصري على
        // وجود audience + sender_role='admin' هنا لتمييز الإشعار الصادر من
        // الإدارة عن إشعارات دورة العمل بين المهندس والمزارع. أي حقل يُسقط من
        // هنا يعني أنه سيختفي نهائياً من قاعدة البيانات ولن يظهر في أي تبويب.
        // ==========================================================
        return [
            'title'             => $this->data['title'] ?? '',
            'body'              => $this->data['body'] ?? '',
            'priority'          => $this->data['priority'] ?? 'normal',
            'audience'          => $this->data['audience'] ?? null,
            'sender_id'         => $this->data['sender_id'] ?? null,
            'sender_name'       => $this->data['sender_name'] ?? null,
            'sender_role'       => $this->data['sender_role'] ?? null,
            'target_user_name'  => $this->data['target_user_name'] ?? null,
            'target_user_role'  => $this->data['target_user_role'] ?? null,
            'type'              => $this->data['type'] ?? 'general',
            'action_url'        => $this->data['action_url'] ?? null,
            'extra'             => $this->data['extra'] ?? [],
        ];
    }
}