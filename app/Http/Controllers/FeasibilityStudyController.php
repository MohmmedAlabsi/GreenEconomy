<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeasibilityStudy;
use App\Http\Requests\StoreFeasibilityStudyRequest;
use App\Http\Requests\UpdateFeasibilityStudyRequest;

class FeasibilityStudyController extends Controller
{
    public function index(Request $request)
    {
        $query = FeasibilityStudy::with(['category', 'region']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function show($id)
    {
        $study = FeasibilityStudy::with(['category', 'region', 'user'])->findOrFail($id);
        return response()->json($study);
    }

    public function create()
    {
        return response()->json(['message' => 'Create feasibility study']);
    }

    public function store(StoreFeasibilityStudyRequest $request)
    {
        $study = FeasibilityStudy::create($request->validated());

        return response()->json([
            'message' => 'Feasibility study created successfully',
            'data' => $study
        ], 201);
    }

    public function edit($id)
    {
        return response()->json(['message' => 'Edit feasibility study', 'id' => $id]);
    }

    public function update(UpdateFeasibilityStudyRequest $request, string $id)
    {
        $study = FeasibilityStudy::findOrFail($id);
        $study->update($request->validated());

        return response()->json([
            'message' => 'Feasibility study updated successfully',
            'data' => $study
        ]);
    }

    public function destroy(string $id)
    {
        $study = FeasibilityStudy::findOrFail($id);
        $study->delete();

        return response()->json(['message' => 'Feasibility study deleted successfully']);
    }
}