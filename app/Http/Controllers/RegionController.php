<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::all();
        return response()->json($regions); //[cite: 28]
    }

    public function show($id)
    {
        $region = Region::with([
            'feasibilityStudies',
            'feasibilityRequests'
        ])->findOrFail($id); //[cite: 28]

        return response()->json($region);
    }

    public function create()
    {
        return response()->json([
            'message' => 'Create region'
        ]); //[cite: 28]
    }

    public function store(StoreRegionRequest $request)
    {
        $region = Region::create($request->validated()); //[cite: 28]

        return response()->json([
            'message' => 'Region created successfully',
            'data' => $region
        ], 201); //[cite: 28]
    }

    public function edit($id)
    {
        return response()->json([
            'message' => 'Edit region',
            'id' => $id
        ]); //[cite: 28]
    }

    public function update(UpdateRegionRequest $request, $id)
    {
        $region = Region::findOrFail($id); //[cite: 28]
        $region->update($request->validated()); //[cite: 28]

        return response()->json([
            'message' => 'Region updated successfully',
            'data' => $region
        ]); //[cite: 28]
    }

    public function destroy($id)
    {
        $region = Region::findOrFail($id);
        $region->delete(); //[cite: 28]

        return response()->json([
            'message' => 'Region deleted successfully'
        ]); //[cite: 28]
    }
}