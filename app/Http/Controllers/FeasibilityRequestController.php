<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeasibilityRequest;
use Illuminate\Routing\Controller;

class FeasibilityRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requests = FeasibilityRequest::with(['user', 'category', 'region'])->latest()->paginate(10);
        return response()->json($requests);
    }

    // عرض تفاصيل طلب معين
    public function show($id)
    {
        $FeasibilityRequest_id = FeasibilityRequest::with(['user', 'category', 'region'])->findOrFail($id);
        return response()->json($FeasibilityRequest_id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
 * Store a newly created feasibility request.
 */
public function store(Request $request)
{
    $validated = $request->validate([

        'user_id' => 'required|exists:users,id',

        'project_title' => 'required|string|max:255',

        'category_id' => 'nullable|exists:categories,id',

        'region_id' => 'nullable|exists:regions,id',

        'estimated_budget' => 'nullable|numeric',

        'land_area' => 'nullable|numeric',

        'description' => 'nullable|string',

        'status' => 'nullable|string|max:50',

    ]);

    $requestData = FeasibilityRequest::create($validated);

    return response()->json([

        'message' => 'Feasibility request created successfully',

        'data' => $requestData

    ], 201);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /**
 * Update the specified resource.
 */
public function update(Request $request, string $id)
{
    $requestData = FeasibilityRequest::findOrFail($id);

    $validated = $request->validate([

        'user_id' => 'sometimes|exists:users,id',

        'project_title' => 'sometimes|string|max:255',

        'category_id' => 'nullable|exists:categories,id',

        'region_id' => 'nullable|exists:regions,id',

        'estimated_budget' => 'nullable|numeric',

        'land_area' => 'nullable|numeric',

        'description' => 'nullable|string',

        'status' => 'nullable|string|max:50',

    ]);

    $requestData->update($validated);

    return response()->json([

        'message' => 'Feasibility request updated successfully',

        'data' => $requestData

    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    /**
 * Remove the specified resource.
 */
public function destroy(string $id)
{
    $requestData = FeasibilityRequest::findOrFail($id);

    $requestData->delete();

    return response()->json([

        'message' => 'Feasibility request deleted successfully'

    ]);
}
}
