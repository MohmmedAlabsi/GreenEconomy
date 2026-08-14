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
public function index(Request $request)
{
    $query = FeasibilityStudy::with(['category', 'region']);

    // تطبيق فلتر الحالة إن وجد
    if ($request->filled('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }

    // تطبيق البحث بالاسم إن وجد
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    // إرجاع الترقيم القياسي (10 عناصر لكل صفحة)
    $studies = $query->latest()->paginate(10);

    return response()->json($studies);
}

    // عرض تفاصيل دراسة جدوى محددة
    public function show($id)
    {
        $feasibilityStudy_id = FeasibilityStudy::with(['category', 'region', 'user'])->findOrFail($id);
        return response()->json($feasibilityStudy_id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return response()->json([
        'message' => 'Create feasibility study'
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    /**
 * Store a newly created resource.
 */
public function store(Request $request)
{
    $validated = $request->validate([

        'title' => 'required|string|max:255',

        'description' => 'nullable|string',

        'category_id' => 'nullable|exists:categories,id',

        'region_id' => 'nullable|exists:regions,id',

        'cover_image' => 'nullable|string|max:255',

        'capital_required' => 'nullable|numeric',

        'expected_roi' => 'required|numeric',

        'payback_period' => 'nullable|integer',

        'risk_level' => 'nullable|string|max:50',

        'status' => 'nullable|string|max:50',

        'pdf_file' => 'nullable|string|max:255',

        'user_id' => 'required|exists:users,id',

    ]);

    $study = FeasibilityStudy::create($validated);

    return response()->json([

        'message' => 'Feasibility study created successfully',

        'data' => $study

    ], 201);
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    return response()->json([
        'message' => 'Edit feasibility study',
        'id' => $id
    ]);
}

    /**
     * Update the specified resource in storage.
     */
    /**
 * Update the specified resource.
 */
public function update(Request $request, string $id)
{
    $study = FeasibilityStudy::findOrFail($id);

    $validated = $request->validate([

        'title' => 'sometimes|string|max:255',

        'description' => 'nullable|string',

        'category_id' => 'nullable|exists:categories,id',

        'region_id' => 'nullable|exists:regions,id',

        'cover_image' => 'nullable|string|max:255',

        'capital_required' => 'nullable|numeric',

        'expected_roi' => 'sometimes|numeric',

        'payback_period' => 'nullable|integer',

        'risk_level' => 'nullable|string|max:50',

        'status' => 'nullable|string|max:50',

        'pdf_file' => 'nullable|string|max:255',

        'user_id' => 'sometimes|exists:users,id',

    ]);

    $study->update($validated);

    return response()->json([

        'message' => 'Feasibility study updated successfully',

        'data' => $study

    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    /**
 * Remove the specified resource.
 */
public function destroy(string $id)
{
    $study = FeasibilityStudy::findOrFail($id);

    $study->delete();

    return response()->json([

        'message' => 'Feasibility study deleted successfully'

    ]);
}
}
