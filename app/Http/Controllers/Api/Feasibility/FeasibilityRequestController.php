<?php

namespace App\Http\Controllers\Feasibility;
use App\Models\FeasibilityRequest;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Feasibility\StoreFeasibilityRequest;
use App\Http\Requests\Feasibility\UpdateFeasibilityRequest;

class FeasibilityRequestController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $requests = FeasibilityRequest::with(['user', 'category', 'region'])->latest()->paginate(50);
        return response()->json($requests);
    }

    public function show($id)
    {
        $request_id = FeasibilityRequest::with(['user', 'category', 'region'])->findOrFail($id);
        return response()->json($request_id);
    }

    public function create()
    {
        return response()->json(['message' => 'Create feasibility request']);
    }

    public function store(StoreFeasibilityRequest $request)
    {
        $requestData = FeasibilityRequest::create($request->validated());
        
        $farmer = User::find($requestData->user_id);
        $farmerName = $farmer?->name ?? 'المزارع';

        // 1. إشعار مدراء النظام باسم المزارع الصريح وليس رقمه
        $admins = User::admins()->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new GeneralNotification([
                'title'       => 'طلب دراسة جدوى جديد',
                'body'        => 'قام المزارع ' . $farmerName . ' بتقديم طلب دراسة جدوى لمشروع: "' . $requestData->project_title . '"',
                'priority'    => 'normal',
                'type'        => 'feasibility_request',
                'sender_id'   => $requestData->user_id,
                'sender_name' => $farmerName,
                'sender_role' => 'farmer',
                'action_url'  => '/admin/feasibility-requests/' . $requestData->id,
            ]));
        }

        // 2. إشعار المزارع بتأكيد الاستلام
        if ($farmer) {
            $farmer->notify(new GeneralNotification([
                'title'      => 'تم استلام طلب دراسة الجدوى',
                'body'       => 'تم حفظ طلب دراسة الجدوى الخاص بمشروع "' . $requestData->project_title . '" وسيتم معالجته قريباً.',
                'priority'   => 'normal',
                'type'       => 'feasibility_request',
                'action_url' => '/farmer/feasibility-requests/' . $requestData->id,
            ]));
        }

        return response()->json([
            'message' => 'Feasibility request created successfully',
            'data' => $requestData
        ], 201);
    }

    public function edit($id)
    {
        return response()->json(['message' => 'Edit feasibility request', 'id' => $id]);
    }

    public function update(UpdateFeasibilityRequest $request, string $id)
    {
        $requestData = FeasibilityRequest::findOrFail($id);
        $requestData->update($request->validated());

        return response()->json([
            'message' => 'Feasibility request updated successfully',
            'data' => $requestData
        ]);
    }

    public function destroy(string $id)
    {
        $requestData = FeasibilityRequest::findOrFail($id);
        $requestData->delete();

        return response()->json(['message' => 'Feasibility request deleted successfully']);
    }
}