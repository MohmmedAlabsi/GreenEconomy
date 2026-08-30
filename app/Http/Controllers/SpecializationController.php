<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Http\Requests\StoreSpecializationRequest;
use App\Http\Requests\UpdateSpecializationRequest;

class SpecializationController extends Controller
{
    public function index()
    {
        $specializations = Specialization::with('role')->get(); //[cite: 30]
        return response()->json($specializations);
    }

    public function show($id)
    {
        $specialization = Specialization::with([
            'role',
            'users'
        ])->findOrFail($id); //[cite: 30]

        return response()->json($specialization);
    }

    public function create()
    {
        return response()->json([
            'message' => 'Create specialization'
        ]); //[cite: 30]
    }

    public function store(StoreSpecializationRequest $request)
    {
        $specialization = Specialization::create($request->validated()); //[cite: 30]

        return response()->json([
            'message' => 'Specialization created successfully',
            'data' => $specialization
        ], 201); //[cite: 30]
    }

    public function edit($id)
    {
        return response()->json([
            'message' => 'Edit specialization',
            'id' => $id
        ]); //[cite: 30]
    }

    public function update(UpdateSpecializationRequest $request, $id)
    {
        $specialization = Specialization::findOrFail($id); //[cite: 30]
        $specialization->update($request->validated()); //[cite: 30]

        return response()->json([
            'message'=>'Specialization updated successfully',
            'data'=>$specialization
        ]); //[cite: 30]
    }

    public function destroy($id)
    {
        $specialization = Specialization::findOrFail($id); //[cite: 30]
        $specialization->delete();

        return response()->json([
            'message'=>'Specialization deleted successfully'
        ]); //[cite: 30]
    }
}