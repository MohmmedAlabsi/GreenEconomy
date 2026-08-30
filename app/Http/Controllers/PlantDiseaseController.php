<?php

namespace App\Http\Controllers;

use App\Models\PlantDisease;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StorePlantDiseaseRequest;
use App\Http\Requests\UpdatePlantDiseaseRequest;

class PlantDiseaseController extends Controller
{
    public function index()
    {
        $diseases = PlantDisease::with('plants')->latest()->get();
        return response()->json($diseases);
    }

    public function show($id)
    {
        $plantDisease = PlantDisease::with(['plants', 'treatments'])->findOrFail($id);
        return response()->json($plantDisease);
    }

    public function store(StorePlantDiseaseRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('plant_diseases', 'public');
            $validated['image_url'] = $path;
        }

        unset($validated['image']);
        $disease = PlantDisease::create($validated);

        if ($request->has('plant_ids')) {
            $disease->plants()->sync($request->input('plant_ids', []));
        }

        return response()->json([
            'message' => 'Disease created successfully',
            'data'    => $disease->load('plants')
        ], 201);
    }

    public function update(UpdatePlantDiseaseRequest $request, $id)
    {
        $disease = PlantDisease::findOrFail($id);
        $validated = $request->validated();

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

        if ($request->has('plant_ids')) {
            $disease->plants()->sync($request->input('plant_ids', []));
        }

        return response()->json([
            'message' => 'Disease updated successfully',
            'data'    => $disease->load('plants')
        ]);
    }

    public function destroy($id)
    {
        $disease = PlantDisease::findOrFail($id);

        if (method_exists($disease, 'plants')) {
            $disease->plants()->detach();
        }

        if (method_exists($disease, 'treatments')) {
            $disease->treatments()->delete();
        }

        $disease->delete();

        return response()->json([
            'message' => 'تم حذف المرض وجميع البيانات المرتبطة به بنجاح'
        ], 200);
    }
}