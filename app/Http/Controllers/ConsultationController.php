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
    /**
 * Store new consultation
 */
public function store(Request $request)
{
    $validated = $request->validate([

        'user_id' => 'required|exists:users,id',
        'issue_title' => 'required|string|max:255',
        'crop_type' => 'required|string|max:100',
        'crop_age' => 'required|string|max:100',
        'issue_duration' => 'required|string|max:100',
        'description' => 'required|string',
        'status' => 'nullable|string|max:50',
        'assigned_expert_id' => 'nullable|exists:users,id',

    ]);

    $consultation = Consultation::create($validated);

    return response()->json([
        'message' => 'Consultation created successfully',
        'data' => $consultation
    ], 201);
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
    /**
 * Update consultation
 */
public function update(Request $request, $id)
{
    $consultation = Consultation::findOrFail($id);

    $validated = $request->validate([

        'user_id' => 'sometimes|exists:users,id',

        'issue_title' => 'sometimes|string|max:255',

        'crop_type' => 'sometimes|string|max:100',

        'crop_age' => 'sometimes|string|max:100',

        'issue_duration' => 'sometimes|string|max:100',

        'description' => 'sometimes|string',

        'status' => 'nullable|string|max:50',

        'assigned_expert_id' => 'nullable|exists:users,id',

    ]);

    $consultation->update($validated);

    return response()->json([

        'message' => 'Consultation updated successfully',

        'data' => $consultation

    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    /**
 * Delete consultation
 */
public function destroy($id)
{
    $consultation = Consultation::findOrFail($id);

    $consultation->delete();

    return response()->json([

        'message' => 'Consultation deleted successfully'

    ]);
}
}
