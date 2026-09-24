<?php

namespace App\Services\Engineer;

use App\Models\Consultation;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ConsultationService
{
    public function queryForUser(int $userId)
    {
        return Consultation::query()->where(function ($query) use ($userId) {
            $query->where('farmer_id', $userId)->orWhere('engineer_id', $userId)->orWhere('user_id', $userId);
        });
    }

    public function create(array $data, int $userId): Consultation
    {
        return DB::transaction(function () use ($data, $userId) {
            $data['user_id'] ??= $userId;
            $consultation = Consultation::create($data);
            $recipientId = $data['engineer_id'] ?? $data['farmer_id'] ?? null;
            if ($recipientId && ($recipient = User::find($recipientId))) {
                $recipient->notify(new GeneralNotification([
                    'title' => 'استشارة جديدة',
                    'body' => 'تم إنشاء استشارة جديدة رقم #'.$consultation->id,
                    'type' => 'consultation',
                    'sender_id' => $userId,
                    'action_url' => '/consultations/'.$consultation->id,
                ]));
            }
            return $consultation->fresh(['farmer', 'engineer']);
        });
    }

    public function update(Consultation $consultation, array $data, int $actorId): Consultation
    {
        return DB::transaction(function () use ($consultation, $data, $actorId) {
            unset($data['user_id'], $data['farmer_id'], $data['engineer_id']);
            $consultation->update($data);
            $recipientId = $consultation->farmer_id === $actorId ? $consultation->engineer_id : $consultation->farmer_id;
            if ($recipientId && ($recipient = User::find($recipientId))) {
                Notification::send($recipient, new GeneralNotification([
                    'title' => 'تحديث الاستشارة',
                    'body' => 'تم تحديث حالة الاستشارة رقم #'.$consultation->id,
                    'type' => 'consultation',
                    'sender_id' => $actorId,
                    'action_url' => '/consultations/'.$consultation->id,
                ]));
            }
            return $consultation->fresh(['farmer', 'engineer']);
        });
    }
}
