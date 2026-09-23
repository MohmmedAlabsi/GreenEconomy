<?php

namespace App\Http\Controllers\Feasibility;
use Illuminate\Http\Request;
use App\Models\FeasibilityStudy;
use App\Http\Requests\Feasibility\StoreFeasibilityStudyRequest;
use App\Http\Requests\Feasibility\UpdateFeasibilityStudyRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FeasibilityStudyController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', FeasibilityStudy::class);

        $user = $request->user();
        $query = FeasibilityStudy::with(['category', 'region']);

        if (!$user->hasPermission('studies.manage')) {
            $query->where(function ($scopedQuery) use ($user) {
                $scopedQuery->where('user_id', $user->id);
                if ($user->hasPermission('studies.view-approved')) {
                    $scopedQuery->orWhere('status', 'approved');
                }
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->latest()->paginate(50));
    }

    public function show(Request $request, $id)
    {
        $study = FeasibilityStudy::with(['category', 'region', 'user'])->findOrFail($id);
        $this->authorize('view', $study);
        return response()->json($study);
    }

    public function create()
    {
        return response()->json(['message' => 'Create feasibility study']);
    }

    public function store(StoreFeasibilityStudyRequest $request)
    {
        $this->authorize('create', FeasibilityStudy::class);
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

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
        $this->authorize('update', $study);
        $data = $request->validated();
        unset($data['user_id']);

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
        $this->authorize('delete', $study);

        $supabaseUrl = config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL') ?? '';
        $baseUrl = rtrim($supabaseUrl, '/');

        // 1. حذف ملف الـ PDF من Supabase Storage
        if (!empty($study->pdf_file)) {
            try {
                $pdfPath = str_replace($baseUrl . '/', '', $study->pdf_file);
                // إزالة أي بادئة slash متبقية
                $cleanPdfPath = ltrim($pdfPath, '/');
                Storage::disk('supabase')->delete($cleanPdfPath);
            } catch (\Throwable $e) {
                Log::warning("فشل حذف ملف الـ PDF لدراسة الجدوى رقم {$study->id}: " . $e->getMessage());
            }
        }

        // 2. حذف صورة الغلاف من Supabase Storage
        if (!empty($study->cover_image)) {
            try {
                $imagePath = str_replace($baseUrl . '/', '', $study->cover_image);
                // إزالة أي بادئة slash متبقية
                $cleanImagePath = ltrim($imagePath, '/');
                Storage::disk('supabase')->delete($cleanImagePath);
            } catch (\Throwable $e) {
                Log::warning("فشل حذف صورة الغلاف لدراسة الجدوى رقم {$study->id}: " . $e->getMessage());
            }
        }

        // 3. حذف سجل دراسة الجدوى من قاعدة البيانات
        $study->delete();

        return response()->json(['message' => 'تم حذف دراسة الجدوى وكافة ملفاتها من السيرفر بنجاح']);
    }
}
