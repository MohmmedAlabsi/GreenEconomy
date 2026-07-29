<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialization;

class SpecializationController extends Controller
{

    /**
     * Display all specializations.
     */
    public function index()
    {
        $specializations = Specialization::with('role')->get();

        return response()->json($specializations);
    }


    /**
     * Display a specific specialization.
     */
    public function show($id)
    {
        $specialization = Specialization::with([
            'role',
            'users'
        ])->findOrFail($id);

        return response()->json($specialization);
    }
    public function create()
    {
        return response()->json([
            'message' => 'Create specialization'
        ]);
    }

    /**
     * Store a new specialization.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:150',
        ]);


        $specialization = Specialization::create($validated);


        return response()->json([
            'message' => 'Specialization created successfully',
            'data' => $specialization
        ],201);
    }


    public function edit($id)
    {
        return response()->json([
            'message' => 'Edit specialization',
            'id' => $id
        ]);
    }
    /**
     * Update specialization.
     */
    public function update(Request $request, $id)
    {

        $specialization = Specialization::findOrFail($id);


        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:150',
        ]);


        $specialization->update($validated);


        return response()->json([
            'message'=>'Specialization updated successfully',
            'data'=>$specialization
        ]);

    }



    /**
     * Delete specialization.
     */
    public function destroy($id)
    {

        $specialization = Specialization::findOrFail($id);


        $specialization->delete();


        return response()->json([
            'message'=>'Specialization deleted successfully'
        ]);

    }

}