<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantDisease;

class PlantDiseaseController extends Controller
{

    /**
     * Display all diseases
     */
    public function index()
    {
        $diseases = PlantDisease::all();

        return response()->json($diseases);
    }



    /**
     * Display specific disease
     */
    public function show($id)
    {
        $plantDisease = PlantDisease::with([
            'plants',
            'treatments'
        ])->findOrFail($id);


        return response()->json($plantDisease);
    }




    /**
     * Create
     */
    public function create()
    {
        return response()->json([
            'message'=>'Create plant disease'
        ]);
    }





    /**
     * Store disease
     */
    public function store(Request $request)
    {

        $validated = $request->validate([

            'name'=>'required|string|max:255',

            'scientific_name'=>'nullable|string|max:255',

            'type'=>'required|string|max:50',

            'symptoms'=>'required|string',

            'cause_description'=>'nullable|string',

            'image_url'=>'nullable|string|max:255',

        ]);



        $disease = PlantDisease::create($validated);



        return response()->json([

            'message'=>'Disease created successfully',

            'data'=>$disease

        ],201);

    }





    /**
     * Edit
     */
    public function edit($id)
    {

        $disease = PlantDisease::findOrFail($id);


        return response()->json($disease);

    }





    /**
     * Update disease
     */
    public function update(Request $request,$id)
    {

        $disease = PlantDisease::findOrFail($id);



        $validated = $request->validate([

            'name'=>'sometimes|string|max:255',

            'scientific_name'=>'nullable|string|max:255',

            'type'=>'sometimes|string|max:50',

            'symptoms'=>'sometimes|string',

            'cause_description'=>'nullable|string',

            'image_url'=>'nullable|string|max:255',

        ]);



        $disease->update($validated);



        return response()->json([

            'message'=>'Disease updated successfully',

            'data'=>$disease

        ]);

    }





    /**
     * Delete disease
     */
    public function destroy($id)
    {

        $disease = PlantDisease::findOrFail($id);


        $disease->delete();



        return response()->json([

            'message'=>'Disease deleted successfully'

        ]);

    }

}