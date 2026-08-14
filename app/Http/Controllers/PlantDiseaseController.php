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
        $diseases = PlantDisease::latest()->get();

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
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'scientific_name'   => 'nullable|string|max:255',
            'plant_type'        => 'nullable|string|max:255',
            'type'              => 'required|string|max:100',
            'severity_level'    => 'nullable|string|max:50',
            'spread_rate'       => 'nullable|string|max:50',
            'farmer_visibility' => 'nullable|string|max:100',
            'symptoms'          => 'required|string',
            'cause_description' => 'nullable|string',
            'image_url'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096', // ملف الصورة
        ]);

        // معالجة رفع الصورة وحفظ مسارها في image_url
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('plant_diseases', 'public');
            $validated['image_url'] = 'storage/' . $path;
        }

        $disease = PlantDisease::create($validated);

        return response()->json([
            'message' => 'Disease created successfully',
            'data'    => $disease
        ], 201);
    }

    /**
     * Update disease
     */
    public function update(Request $request, $id)
    {
        $disease = PlantDisease::findOrFail($id);

        $validated = $request->validate([
            'name'              => 'sometimes|string|max:255',
            'scientific_name'   => 'nullable|string|max:255',
            'plant_type'        => 'nullable|string|max:255',
            'type'              => 'sometimes|string|max:100',
            'severity_level'    => 'nullable|string|max:50',
            'spread_rate'       => 'nullable|string|max:50',
            'farmer_visibility' => 'nullable|string|max:100',
            'symptoms'          => 'sometimes|string',
            'cause_description' => 'nullable|string',
            'image_url'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        // عند رفع صورة جديدة: إزالة الصورة القديمة ورفع الجديدة
        if ($request->hasFile('image')) {
            if ($disease->image_url) {
                $oldPath = str_replace('storage/', '', parse_url($disease->image_url, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('plant_diseases', 'public');
            $validated['image_url'] = 'storage/' . $path;
        }

        $disease->update($validated);

        return response()->json([
            'message' => 'Disease updated successfully',
            'data'    => $disease
        ]);
    }

    /**
     * Delete disease
     */
    public function destroy($id)
    {
        $disease = PlantDisease::findOrFail($id);

        // حذف الصورة الفيزيائية من التخزين عند حذف السجل
        if ($disease->image_url) {
            $path = str_replace('storage/', '', parse_url($disease->image_url, PHP_URL_PATH));
            Storage::disk('public')->delete($path);
        }

        $disease->delete();

        return response()->json([
            'message' => 'Disease deleted successfully'
        ]);
    }
}