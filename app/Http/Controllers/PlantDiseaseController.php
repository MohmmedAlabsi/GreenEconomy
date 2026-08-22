<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantDisease;
use Illuminate\Support\Facades\Storage;

class PlantDiseaseController extends Controller
{
    /**
     * Display all diseases
     */
    public function index()
    {
        $diseases = PlantDisease::with('plants')->latest()->get();

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
     * Store disease
     */
    public function store(Request $request)
    {
        $rules = [
            'name'              => 'required|string|max:255',
            'scientific_name'   => 'nullable|string|max:255',
            'plant_type'        => 'nullable|string|max:255',
            'plant_ids'         => 'nullable|array',               // مصفوفة معرفات النباتات المرتبطة
            'plant_ids.*'       => 'integer|exists:plants,id',
            'type'              => 'required|string|max:100',
            'severity_level'    => 'nullable|string|max:50',
            'spread_rate'       => 'nullable|string|max:50',
            'farmer_visibility' => 'nullable|string|max:100',
            'symptoms'          => 'required|string',
            'cause_description' => 'nullable|string',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,webp|max:4096';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('plant_diseases', 'public');
            $validated['image_url'] = $path;
        }

        unset($validated['image']);

        $disease = PlantDisease::create($validated);

        // ربط النباتات عبر الجدول الوسيط في حال تم إرسال plant_ids
        if ($request->has('plant_ids')) {
            $disease->plants()->sync($request->input('plant_ids', []));
        }

        return response()->json([
            'message' => 'Disease created successfully',
            'data'    => $disease->load('plants')
        ], 201);
    }

    /**
     * Update disease
     */
    public function update(Request $request, $id)
    {
        $disease = PlantDisease::findOrFail($id);

        $rules = [
            'name'              => 'sometimes|string|max:255',
            'scientific_name'   => 'nullable|string|max:255',
            'plant_type'        => 'nullable|string|max:255',
            'plant_ids'         => 'nullable|array',               // مصفوفة معرفات النباتات
            'plant_ids.*'       => 'integer|exists:plants,id',
            'type'              => 'sometimes|string|max:100',
            'severity_level'    => 'nullable|string|max:50',
            'spread_rate'       => 'nullable|string|max:50',
            'farmer_visibility' => 'nullable|string|max:100',
            'symptoms'          => 'sometimes|string',
            'cause_description' => 'nullable|string',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,webp|max:4096';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('image')) {
            $rawImagePath = $disease->getRawOriginal('image_url');

            if ($rawImagePath) {
                Storage::disk('public')->delete($rawImagePath);
            }

            $path = $request->file('image')->store('plant_diseases', 'public');
            $validated['image_url'] = $path;
        }

        unset($validated['image']);

        $disease->update($validated);

        // تحديث مزامنة النباتات المرتبطة بالجدول الوسيط
        if ($request->has('plant_ids')) {
            $disease->plants()->sync($request->input('plant_ids', []));
        }

        return response()->json([
            'message' => 'Disease updated successfully',
            'data'    => $disease->load('plants')
        ]);
    }

    /**
     * Delete disease
     */
    public function destroy($id)
    {
        $disease = PlantDisease::findOrFail($id);

    // 1. فك ارتباط المرض بالنباتات من الجدول الوسيط
    if (method_exists($disease, 'plants')) {
        $disease->plants()->detach();
    }

    // 2. حذف العلاجات المرتبطة بهذا المرض
    if (method_exists($disease, 'treatments')) {
        $disease->treatments()->delete();
    }

    // 3. حذف المرض نفسه
    $disease->delete();

    return response()->json([
        'message' => 'تم حذف المرض وجميع البيانات المرتبطة به بنجاح'
    ], 200);
    }
}