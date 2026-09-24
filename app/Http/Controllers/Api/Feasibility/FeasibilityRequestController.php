<?php

namespace App\Http\Controllers\Feasibility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feasibility\StoreFeasibilityRequest;
use App\Http\Requests\Feasibility\UpdateFeasibilityRequest;
use App\Http\Resources\Feasibility\FeasibilityRequestResource;
use App\Models\FeasibilityRequest;
use App\Services\Feasibility\FeasibilityRequestService;

class FeasibilityRequestController extends Controller
{
    public function __construct(private readonly FeasibilityRequestService $requests)
    {
    }

    public function index()
    {
        return FeasibilityRequestResource::collection(
            FeasibilityRequest::with(['user', 'category', 'region'])->latest()->paginate(50)
        );
    }

    public function show(string $id)
    {
        $request = FeasibilityRequest::with(['user', 'category', 'region'])->findOrFail($id);
        return new FeasibilityRequestResource($request);
    }

    public function create()
    {
        return response()->json(['message' => 'Create feasibility request']);
    }

    public function store(StoreFeasibilityRequest $request)
    {
        $requestData = $this->requests->createRequest($request->validated(), $request->user()->id);

        return response()->json([
            'message' => 'Feasibility request created successfully',
            'data' => new FeasibilityRequestResource($requestData->load(['user', 'category', 'region'])),
        ], 201);
    }

    public function edit(string $id)
    {
        return response()->json(['message' => 'Edit feasibility request', 'id' => $id]);
    }

    public function update(UpdateFeasibilityRequest $request, string $id)
    {
        $requestData = FeasibilityRequest::findOrFail($id);
        $requestData->update($request->validated());

        return response()->json(['message' => 'Feasibility request updated successfully', 'data' => new FeasibilityRequestResource($requestData->load(['user', 'category', 'region']))]);
    }

    public function destroy(string $id)
    {
        $requestData = FeasibilityRequest::findOrFail($id);
        $requestData->delete();

        return response()->json(['message' => 'Feasibility request deleted successfully']);
    }
}
