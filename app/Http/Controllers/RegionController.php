<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;

class RegionController extends Controller
{
    /**
     * Display a listing of the regions.
     */
    public function index()
    {
        $regions = Region::all();

        return response()->json($regions);
    }


    /**
     * Display a specific region with related data.
     */
    public function show($id)
    {
        $region = Region::with([
            'feasibilityStudies',
            'feasibilityRequests'
        ])->findOrFail($id);

        return response()->json($region);
    }

    public function create()
    {
        return response()->json([
            'message' => 'Create region'
        ]);
    }
    /**
     * Store a newly created region.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
        ]);


        $region = Region::create($validated);


        return response()->json([
            'message' => 'Region created successfully',
            'data' => $region
        ], 201);
    }
    public function edit($id)
    {
        return response()->json([
            'message' => 'Edit region',
            'id' => $id
        ]);
    }

    /**
     * Update an existing region.
     */
    public function update(Request $request, $id)
    {
        $region = Region::findOrFail($id);


        $validated = $request->validate([
            'name' => 'required|string|max:150',
        ]);


        $region->update($validated);


        return response()->json([
            'message' => 'Region updated successfully',
            'data' => $region
        ]);
    }


    /**
     * Delete a region.
     */
    public function destroy($id)
    {
        $region = Region::findOrFail($id);

        $region->delete();


        return response()->json([
            'message' => 'Region deleted successfully'
        ]);
    }
}