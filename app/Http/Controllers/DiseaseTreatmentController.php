<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiseaseTreatment;
class DiseaseTreatmentController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $treatments = DiseaseTreatment::with('disease')->get();

        return response()->json($treatments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([

        'disease_id' => 'required|exists:plant_diseases,id',

        'treatment_type' => 'required|string|max:50',

        'title' => 'required|string|max:255',

        'instructions' => 'required|string',

    ]);

    $treatment = DiseaseTreatment::create($validated);

    return response()->json([

        'message' => 'Treatment created successfully',

        'data' => $treatment

    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $treatment = DiseaseTreatment::with('disease')->findOrFail($id);
        return response()->json($treatment);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, string $id)
{
    $treatment = DiseaseTreatment::findOrFail($id);

    $validated = $request->validate([

        'disease_id' => 'sometimes|exists:plant_diseases,id',

        'treatment_type' => 'sometimes|string|max:50',

        'title' => 'sometimes|string|max:255',

        'instructions' => 'sometimes|string',

    ]);

    $treatment->update($validated);

    return response()->json([

        'message' => 'Treatment updated successfully',

        'data' => $treatment

    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $treatment = DiseaseTreatment::findOrFail($id);

    $treatment->delete();

    return response()->json([

        'message' => 'Treatment deleted successfully'

    ]);
}
}
