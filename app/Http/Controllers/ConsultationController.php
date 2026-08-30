<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Http\Requests\StoreConsultationRequest;
use App\Http\Requests\UpdateConsultationRequest;
use Illuminate\Routing\Controller;

class ConsultationController extends Controller
{
    public function index()
    {
        $consultations = Consultation::with(['user', 'assignedExpert', 'attachments'])->latest()->paginate(10);
        return response()->json($consultations);
    }

    public function show($id)
    {
        $consultation_id = Consultation::with(['user', 'assignedExpert', 'attachments'])->findOrFail($id);
        return response()->json($consultation_id);
    }

    public function create()
    {
        return response()->json([
            'message' => 'Create consultation'
        ]);
    }   

    public function store(StoreConsultationRequest $request)
    {
        $consultation = Consultation::create($request->validated());

        return response()->json([
            'message' => 'Consultation created successfully',
            'data' => $consultation
        ], 201);
    }

    public function edit($id)
    {
        return response()->json([
            'message' => 'Edit consultation',
            'id' => $id
        ]);
    }

    public function update(UpdateConsultationRequest $request, $id)
    {
        $consultation = Consultation::findOrFail($id);
        
        $consultation->update($request->validated());

        return response()->json([
            'message' => 'Consultation updated successfully',
            'data' => $consultation
        ]);
    }

    public function destroy($id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->delete();

        return response()->json([
            'message' => 'Consultation deleted successfully'
        ]);
    }
}