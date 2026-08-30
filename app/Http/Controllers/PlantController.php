<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Http\Requests\StorePlantRequest;
use App\Http\Requests\UpdatePlantRequest;

class PlantController extends Controller
{
    public function index()
    {
        $plants = Plant::with('category')->get();
        return response()->json($plants);
    }

    public function show($id)
    {
        $plant = Plant::with(['category', 'diseases'])->findOrFail($id);
        return response()->json($plant);
    }

    public function create()
    {
        return response()->json(['message'=>'Create plant']);
    }

    public function store(StorePlantRequest $request)
    {
        $plant = Plant::create($request->validated());

        return response()->json([
            'message' => 'Plant created successfully',
            'data'    => $plant
        ], 201);
    }

    public function edit($id)
    {
        $plant = Plant::findOrFail($id);
        return response()->json($plant);
    }

    public function update(UpdatePlantRequest $request, $id)
    {
        $plant = Plant::findOrFail($id);
        $plant->update($request->validated());

        return response()->json([
            'message' => 'Plant updated successfully',
            'data'    => $plant
        ]);
    }

    public function destroy($id)
    {
        $plant = Plant::findOrFail($id);
        $plant->delete();

        return response()->json(['message' => 'Plant deleted successfully']);
    }
}