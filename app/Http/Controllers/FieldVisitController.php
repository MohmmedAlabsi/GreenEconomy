<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FieldVisit;
use Illuminate\Routing\Controller;

class FieldVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visits = FieldVisit::with(['user', 'attachments'])->latest()->paginate(10);
        return response()->json($visits);
    }

    // عرض تفاصيل زيارة ميدانية معينة
    public function show($id)
    {
        $FieldVisit_id = FieldVisit::with(['user', 'attachments'])->findOrFail($id);
        return response()->json($FieldVisit_id);
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
