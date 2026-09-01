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
            // الرفع إلى Supabase
            $path = $request->file('image')->store('plant_diseases', 'supabase');
            // حفظ الرابط المباشر
            $validated['image_url'] = rtrim(config('filesystems.disks.supabase.url'), '/') . '/' . $path;
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
            $rawImageUrl = $disease->getRawOriginal('image_url');

            // إذا كان الرابط القديم من Supabase، يجب استخراج مسار الملف للحذف
            if ($rawImageUrl && str_contains($rawImageUrl, 'supabase.co')) {
                // استخراج المسار النسبي (مثال: plant_diseases/image.jpg) من الرابط الكامل
                $parsedUrl = parse_url($rawImageUrl, PHP_URL_PATH);
                // حذف الجزء الثابت من المسار (/storage/v1/object/public/bucket_name/)
                $pathToDelete = preg_replace('/^\/storage\/v1\/object\/public\/[^\/]+\//', '', $parsedUrl);
                
                if (Storage::disk('supabase')->exists($pathToDelete)) {
                    Storage::disk('supabase')->delete($pathToDelete);
                }
            }

            // رفع الصورة الجديدة
            $path = $request->file('image')->store('plant_diseases', 'supabase');
            $validated['image_url'] = rtrim(config('filesystems.disks.supabase.url'), '/') . '/' . $path;
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
        
        // استخراج وحذف الصورة من Supabase عند حذف المرض
        $rawImageUrl = $disease->getRawOriginal('image_url');
        if ($rawImageUrl && str_contains($rawImageUrl, 'supabase.co')) {
            $parsedUrl = parse_url($rawImageUrl, PHP_URL_PATH);
            $pathToDelete = preg_replace('/^\/storage\/v1\/object\/public\/[^\/]+\//', '', $parsedUrl);
            
            if (Storage::disk('supabase')->exists($pathToDelete)) {
                Storage::disk('supabase')->delete($pathToDelete);
            }
        }

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