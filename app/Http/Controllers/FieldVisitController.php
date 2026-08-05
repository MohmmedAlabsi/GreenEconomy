<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FieldVisit;
use Illuminate\Routing\Controller;

class FieldVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visits = FieldVisit::with(['user', 'attachments'])->latest()->paginate(10);
        return response()->json($visits);
    }

    // عرض تفاصيل زيارة ميدانية معينة
    public function show($id)
    {
        $FieldVisit_id = FieldVisit::with(['user', 'attachments'])->findOrFail($id);
        return response()->json($FieldVisit_id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return response()->json([
        'message' => 'Create field visit'
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    /**
 * Store a newly created resource.
 */
public function store(Request $request)
{
    $validated = $request->validate([

        'user_id' => 'required|exists:users,id',

        'contact_name' => 'required|string|max:255',

        'contact_phone' => 'required|string|max:20',

        'governorate' => 'required|string|max:100',

        'district' => 'required|string|max:100',

        'village_or_area' => 'required|string|max:255',

        'nearest_landmark' => 'nullable|string|max:255',

        'crop_type' => 'required|string|max:100',

        'area_size' => 'required|numeric',

        'infestation_type' => 'required|string|max:150',

        'priority_level' => 'required|string|max:50',

        'problem_description' => 'required|string',

        'status' => 'nullable|string|max:50',

        'scheduled_at' => 'nullable|date',

        'estimated_cost' => 'nullable|numeric',

    ]);

    $visit = FieldVisit::create($validated);

    return response()->json([

        'message' => 'Field visit created successfully',

        'data' => $visit

    ], 201);
}

    /**
     * Show the form for editing the specified resource.
     */
        public function edit($id)
{
    return response()->json([
        'message' => 'Edit field visit',
        'id' => $id
    ]);
}

    /**
     * Update the specified resource in storage.
     */
    /**
 * Update the specified resource.
 */
public function update(Request $request, string $id)
{
    $visit = FieldVisit::findOrFail($id);

    $validated = $request->validate([

        'user_id' => 'sometimes|exists:users,id',

        'contact_name' => 'sometimes|string|max:255',

        'contact_phone' => 'sometimes|string|max:20',

        'governorate' => 'sometimes|string|max:100',

        'district' => 'sometimes|string|max:100',

        'village_or_area' => 'sometimes|string|max:255',

        'nearest_landmark' => 'nullable|string|max:255',

        'crop_type' => 'sometimes|string|max:100',

        'area_size' => 'sometimes|numeric',

        'infestation_type' => 'sometimes|string|max:150',

        'priority_level' => 'sometimes|string|max:50',

        'problem_description' => 'sometimes|string',

        'status' => 'nullable|string|max:50',

        'scheduled_at' => 'nullable|date',

        'estimated_cost' => 'nullable|numeric',

    ]);

    $visit->update($validated);

    return response()->json([

        'message' => 'Field visit updated successfully',

        'data' => $visit

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
    $visit = FieldVisit::findOrFail($id);

    $visit->delete();

    return response()->json([

        'message' => 'Field visit deleted successfully'

    ]);
}
}
