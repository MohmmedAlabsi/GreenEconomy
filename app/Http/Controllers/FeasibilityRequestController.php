<?php

namespace App\Http\Controllers;

use App\Models\FeasibilityRequest;
use App\Http\Requests\StoreFeasibilityRequest;
use App\Http\Requests\UpdateFeasibilityRequest;

class FeasibilityRequestController extends Controller
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
        
        $admin = \App\Models\User::whereHas('role', function($q) {
            $q->where('name', 'Admin');
        })->first();

        if ($admin) {
            \App\Models\Notification::create([
                'audience' => 'specific',
                'user_id'  => $admin->id,
                'title'    => 'طلب دراسة جدوى جديد',
                'body'     => 'تم تقديم طلب دراسة جدوى للمشروع: "' . $requestData->project_title . '" من قبل المستخدم ID: ' . $requestData->user_id,
                'priority' => 'normal',
            ]);
        }

        \App\Models\Notification::create([
            'audience' => 'specific',
            'user_id'  => $requestData->user_id,
            'title'    => 'تم استلام طلب دراسة الجدوى',
            'body'     => 'تم حفظ طلب دراسة الجدوى الخاص بمشروع "' . $requestData->project_title . '" وسيتم معالجته قريباً.',
            'priority' => 'normal',
        ]);

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