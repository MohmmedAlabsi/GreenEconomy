<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeasibilityStudy;
use Illuminate\Routing\Controller;

class FeasibilityStudyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $studies = FeasibilityStudy::with(['category', 'region', 'user'])->latest()->paginate(10);
        return response()->json($studies);
    }

    // عرض تفاصيل دراسة جدوى محددة
    public function show(FeasibilityStudy $feasibilityStudy)
    {
        return response()->json($feasibilityStudy->load(['category', 'region', 'user']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
