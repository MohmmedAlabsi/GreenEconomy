<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plant;

class PlantController extends Controller
{

    /**
     * Display all plants
     */
    public function index()
    {
        $plants = Plant::with('category')->get();

        return response()->json($plants);
    }



    /**
     * Display single plant
     */
    public function show($id)
    {
        $plant = Plant::with([
            'category',
            'diseases'
        ])->findOrFail($id);


        return response()->json($plant);
    }




    /**
     * Create
     */
    public function create()
    {
        return response()->json([
            'message'=>'Create plant'
        ]);
    }




    /**
     * Store new plant
     */
    public function store(Request $request)
    {

        $validated = $request->validate([

            'common_name'=>'required|string|max:255',

            'scientific_name'=>'nullable|string|max:255',

            'description'=>'nullable|string',

            'climate_requirements'=>'nullable|string',

            'irrigation_schedule'=>'nullable|string',

            'planting_season'=>'nullable|string|max:100',

            'category_id'=>'required|exists:categories,id',

            'image_url'=>'nullable|string|max:255',

        ]);



        $plant = Plant::create($validated);



        return response()->json([

            'message'=>'Plant created successfully',

            'data'=>$plant

        ],201);

    }





    /**
     * Edit
     */
    public function edit($id)
    {

        $plant = Plant::findOrFail($id);

        return response()->json($plant);

    }





    /**
     * Update plant
     */
    public function update(Request $request,$id)
    {

        $plant = Plant::findOrFail($id);



        $validated = $request->validate([

            'common_name'=>'sometimes|string|max:255',

            'scientific_name'=>'nullable|string|max:255',

            'description'=>'nullable|string',

            'climate_requirements'=>'nullable|string',

            'irrigation_schedule'=>'nullable|string',

            'planting_season'=>'nullable|string|max:100',

            'category_id'=>'sometimes|exists:categories,id',

            'image_url'=>'nullable|string|max:255',

        ]);



        $plant->update($validated);



        return response()->json([

            'message'=>'Plant updated successfully',

            'data'=>$plant

        ]);

    }





    /**
     * Delete plant
     */
    public function destroy($id)
    {

        $plant = Plant::findOrFail($id);


        $plant->delete();


        return response()->json([

            'message'=>'Plant deleted successfully'

        ]);

    }

}