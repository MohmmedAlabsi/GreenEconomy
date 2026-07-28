<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultation;
use Illuminate\Routing\Controller;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $consultations = Consultation::with(['user', 'assignedExpert', 'attachments'])->latest()->paginate(10);
        return response()->json($consultations);
    }

    // عرض تفاصيل استشارة محددة مع المرفقات الخاصة بها (Morph)
    public function show($id)
    {
        $consultation_id = Consultation::with(['user', 'assignedExpert', 'attachments'])->findOrFail($id);
        return response()->json($consultation_id);
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
