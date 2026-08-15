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
        $rules = [
            'name'              => 'required|string|max:255',
            'scientific_name'   => 'nullable|string|max:255',
            'plant_type'        => 'nullable|string|max:255',
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
            // تخزين المسار النسبى الخام داخل القرص العام
            $path = $request->file('image')->store('plant_diseases', 'public');
            $validated['image_url'] = $path;
        }

        unset($validated['image']);

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

        $rules = [
            'name'              => 'sometimes|string|max:255',
            'scientific_name'   => 'nullable|string|max:255',
            'plant_type'        => 'nullable|string|max:255',
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
            // جلب القيمة الخام للمسار من قاعدة البيانات مباشرة بدلاً من الرابط الكامل
            $rawImagePath = $disease->getRawOriginal('image_url');

            if ($rawImagePath) {
                Storage::disk('public')->delete($rawImagePath);
            }

            $path = $request->file('image')->store('plant_diseases', 'public');
            $validated['image_url'] = $path;
        }

        unset($validated['image']);

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

        $rawImagePath = $disease->getRawOriginal('image_url');

        if ($rawImagePath) {
            Storage::disk('public')->delete($rawImagePath);
        }

        $disease->delete();

        return response()->json([
            'message' => 'Disease deleted successfully'
        ]);
    }
}