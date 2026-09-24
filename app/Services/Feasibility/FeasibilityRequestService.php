<?php

namespace App\Services\Feasibility;

use App\Models\FeasibilityRequest;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class FeasibilityRequestService
{
    public function createRequest(array $data, int $userId): FeasibilityRequest
    {
        $data['user_id'] = $userId;
        $request = FeasibilityRequest::create($data);
        $farmer = User::find($userId);
        $farmerName = $farmer?->name ?? 'المزارع';

        $admins = User::admins()->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new GeneralNotification([
                'title' => 'طلب دراسة جدوى جديد',
                'body' => 'قام المزارع ' . $farmerName . ' بتقديم طلب دراسة جدوى لمشروع: "' . $request->project_title . '"',
                'priority' => 'normal',
                'type' => 'feasibility_request',
                'sender_id' => $userId,
                'sender_name' => $farmerName,
                'sender_role' => 'farmer',
                'action_url' => '/admin/feasibility-requests/' . $request->id,
            ]));
        }

        if ($farmer) {
            $farmer->notify(new GeneralNotification([
                'title' => 'تم استلام طلب دراسة الجدوى',
                'body' => 'تم حفظ طلب دراسة الجدوى الخاص بمشروع "' . $request->project_title . '" وسيتم معالجته قريباً.',
                'priority' => 'normal',
                'type' => 'feasibility_request',
                'action_url' => '/farmer/feasibility-requests/' . $request->id,
            ]));
        }

        return $request;
    }

    public function queryForUser(int $userId)
    {
        return FeasibilityRequest::query()->where('user_id', $userId);
    }
}
