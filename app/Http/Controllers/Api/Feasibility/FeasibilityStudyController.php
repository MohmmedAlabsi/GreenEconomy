<?php

namespace App\Http\Controllers\Feasibility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feasibility\StoreFeasibilityStudyRequest;
use App\Http\Requests\Feasibility\UpdateFeasibilityStudyRequest;
use App\Http\Resources\Feasibility\FeasibilityStudyResource;
use App\Models\FeasibilityStudy;
use App\Services\Feasibility\FeasibilityStudyService;
use Illuminate\Http\Request;

class FeasibilityStudyController extends Controller
{
    public function __construct(private readonly FeasibilityStudyService $studies)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', FeasibilityStudy::class);
        $user = $request->user();
        $query = FeasibilityStudy::with(['category', 'region', 'user']);

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

        return FeasibilityStudyResource::collection($query->latest()->paginate(50));
    }

    public function show(string $id)
    {
        $study = FeasibilityStudy::with(['category', 'region', 'user'])->findOrFail($id);
        $this->authorize('view', $study);
        return new FeasibilityStudyResource($study);
    }

    public function create()
    {
        return response()->json(['message' => 'Create feasibility study']);
    }

    public function store(StoreFeasibilityStudyRequest $request)
    {
        $this->authorize('create', FeasibilityStudy::class);
        $study = $this->studies->create(
            $request->validated(),
            $request->file('file') ?? $request->file('pdf_file'),
            $request->file('image') ?? $request->file('cover_image'),
            $request->user()->id,
        );

        return response()->json(['message' => 'Feasibility study created successfully', 'data' => new FeasibilityStudyResource($study)], 201);
    }

    public function edit(string $id)
    {
        return response()->json(['message' => 'Edit feasibility study', 'id' => $id]);
    }

    public function update(UpdateFeasibilityStudyRequest $request, string $id)
    {
        $study = FeasibilityStudy::findOrFail($id);
        $this->authorize('update', $study);
        $study = $this->studies->update(
            $study,
            $request->validated(),
            $request->file('file') ?? $request->file('pdf_file'),
            $request->file('image') ?? $request->file('cover_image'),
        );

        return response()->json(['message' => 'Feasibility study updated successfully', 'data' => new FeasibilityStudyResource($study)]);
    }

    public function destroy(string $id)
    {
        $study = FeasibilityStudy::findOrFail($id);
        $this->authorize('delete', $study);
        $this->studies->delete($study);

        return response()->json(['message' => 'تم حذف دراسة الجدوى وكافة ملفاتها من السيرفر بنجاح']);
    }
}
