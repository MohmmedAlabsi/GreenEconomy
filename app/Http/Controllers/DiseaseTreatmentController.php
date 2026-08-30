<?php

namespace App\Http\Controllers;

use App\Models\DiseaseTreatment;
use App\Http\Requests\StoreDiseaseTreatmentRequest;
use App\Http\Requests\UpdateDiseaseTreatmentRequest;

class DiseaseTreatmentController extends Controller
{
    public function index()
    {
        $treatments = DiseaseTreatment::with('disease')->get();
        return response()->json($treatments);
    }

    public function create()
    {
        return response()->json(['message' => 'Create disease treatment']);
    }

    public function store(StoreDiseaseTreatmentRequest $request)
    {
        $treatment = DiseaseTreatment::create($request->validated());

        return response()->json([
            'message' => 'Treatment created successfully',
            'data' => $treatment
        ], 201);
    }

    public function show(string $id)
    {
        $treatment = DiseaseTreatment::with('disease')->findOrFail($id);
        return response()->json($treatment);
    }

    public function edit($id)
    {
        return response()->json(['message' => 'Edit disease treatment', 'id' => $id]);
    }

    public function update(UpdateDiseaseTreatmentRequest $request, string $id)
    {
        $treatment = DiseaseTreatment::findOrFail($id);
        $treatment->update($request->validated());

        return response()->json([
            'message' => 'Treatment updated successfully',
            'data' => $treatment
        ]);
    }

    public function destroy(string $id)
    {
        $treatment = DiseaseTreatment::findOrFail($id);
        $treatment->delete();

        return response()->json(['message' => 'Treatment deleted successfully']);
    }
}