<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

/**
 * Centralizes the creation of platform notifications.
 *
 * Previously every controller duplicated the same `Notification::create([...])`
 * block (and the "find the Admin user" query). This service is the single
 * source of truth for that behaviour so the payload shape stays consistent.
 */
class NotificationService
{
    /**
     * Create a notification.
     *
     * Mirrors the exact payload the controllers used to build inline.
     */
    public function notify(
        ?int $userId,
        string $title,
        string $body,
        string $priority = 'normal',
        string $audience = 'specific'
    ): Notification {
        return Notification::create([
            'audience' => $audience,
            'user_id'  => $userId,
            'title'    => $title,
            'body'     => $body,
            'priority' => $priority,
        ]);
    }

    /**
     * Notify the Admin user (if one exists).
     *
     * Replaces the repeated `User::whereHas('role', ...)->first()` + create block.
     * Returns null when no Admin user is found, matching the previous guarded behaviour.
     */
    public function notifyAdmin(string $title, string $body, string $priority = 'normal'): ?Notification
    {
        $admin = User::admins()->first();

        if (! $admin) {
            return null;
        }

        return $this->notify($admin->id, $title, $body, $priority);
    }
}
