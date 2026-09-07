<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeasibilityStudy;
use App\Http\Requests\StoreFeasibilityStudyRequest;
use App\Http\Requests\UpdateFeasibilityStudyRequest;
use Illuminate\Support\Facades\Storage;

class FeasibilityStudyController extends Controller
{
    public function index(Request $request)
    {
        $query = FeasibilityStudy::with(['category', 'region']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->latest()->paginate(50));
    }

    public function show($id)
    {
        $study = FeasibilityStudy::with(['category', 'region', 'user'])->findOrFail($id);
        return response()->json($study);
    }

    public function create()
    {
        return response()->json(['message' => 'Create feasibility study']);
    }

    public function store(StoreFeasibilityStudyRequest $request)
    {
        $data = $request->validated();

        $supabaseUrl = config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL') ?? '';
        $baseUrl = rtrim($supabaseUrl, '/');

        // معالجة ورفع ملف الـ PDF إلى Supabase Storage
        $pdfFile = $request->file('file') ?? $request->file('pdf_file');
        if ($pdfFile && $pdfFile->isValid()) {
            $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $pdfFile->getClientOriginalExtension();
            $filePath = $pdfFile->storeAs('feasibility_pdfs', $fileName, 'supabase');
            $data['pdf_file'] = $baseUrl ? ($baseUrl . '/' . $filePath) : $filePath;
        }

        // معالجة ورفع صورة المشروع/الغلاف إلى Supabase Storage
        $imageFile = $request->file('image') ?? $request->file('cover_image');
        if ($imageFile && $imageFile->isValid()) {
            $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
            $filePath = $imageFile->storeAs('feasibility_images', $fileName, 'supabase');
            $data['cover_image'] = $baseUrl ? ($baseUrl . '/' . $filePath) : $filePath;
        }

        $study = FeasibilityStudy::create($data);

        return response()->json([
            'message' => 'Feasibility study created successfully',
            'data' => $study
        ], 201);
    }

    public function edit($id)
    {
        return response()->json(['message' => 'Edit feasibility study', 'id' => $id]);
    }

    public function update(UpdateFeasibilityStudyRequest $request, string $id)
    {
        set_time_limit(300);

        $study = FeasibilityStudy::findOrFail($id);
        $data = $request->validated();

        $supabaseUrl = config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL') ?? '';
        $baseUrl = rtrim($supabaseUrl, '/');

        // 1. تحديث ملف الـ PDF وحذف القديم إن وجد
        $pdfFile = $request->file('file') ?? $request->file('pdf_file');
        if ($pdfFile && $pdfFile->isValid()) {
            // حذف الملف القديم من Supabase إذا كان موجوداً
            if ($study->pdf_file) {
                $oldPath = str_replace($baseUrl . '/', '', $study->pdf_file);
                \Illuminate\Support\Facades\Storage::disk('supabase')->delete($oldPath);
            }

            $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $pdfFile->getClientOriginalExtension();
            $filePath = $pdfFile->storeAs('feasibility_pdfs', $fileName, 'supabase');
            $data['pdf_file'] = $baseUrl ? ($baseUrl . '/' . $filePath) : $filePath;
        }

        // 2. تحديث صورة المشروع وحذف القديمة إن وجدته
        $imageFile = $request->file('image') ?? $request->file('cover_image');
        if ($imageFile && $imageFile->isValid()) {
            // حذف الصورة القديمة من Supabase إذا كانت موجودة
            if ($study->cover_image) {
                $oldPath = str_replace($baseUrl . '/', '', $study->cover_image);
                \Illuminate\Support\Facades\Storage::disk('supabase')->delete($oldPath);
            }

            $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
            $filePath = $imageFile->storeAs('feasibility_images', $fileName, 'supabase');
            $data['cover_image'] = $baseUrl ? ($baseUrl . '/' . $filePath) : $filePath;
        }

        $study->update($data);

        return response()->json([
            'message' => 'Feasibility study updated successfully',
            'data' => $study
        ]);
    }

    public function destroy(string $id)
    {
        $study = FeasibilityStudy::findOrFail($id);
        $study->delete();

        return response()->json(['message' => 'Feasibility study deleted successfully']);
    }
}