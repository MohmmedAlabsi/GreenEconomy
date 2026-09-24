<?php
namespace App\Http\Controllers\Api\Feasibility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Feasibility\StoreFeasibilityStudyRequest;
use App\Http\Requests\Feasibility\UpdateFeasibilityStudyRequest;
use App\Http\Resources\Feasibility\FeasibilityStudyResource;
use App\Services\Feasibility\FeasibilityStudyService;
use Illuminate\Http\Request;
class FeasibilityStudyController extends Controller
{
 public function __construct(private readonly FeasibilityStudyService $service) {}
 public function index(Request $request) { $this->authorize('viewAny', \App\Models\FeasibilityStudy::class); return FeasibilityStudyResource::collection($this->service->index($request->user()->id, $request->all())); }
 public function show(Request $request,string $id) { $study=$this->service->find($id); $this->authorize('view',$study); return new FeasibilityStudyResource($study); }
 public function store(StoreFeasibilityStudyRequest $request) { $this->authorize('create', \App\Models\FeasibilityStudy::class); return response()->json(['message'=>'Feasibility study created successfully','data'=>new FeasibilityStudyResource($this->service->create($request->validated(),$request->user()->id))],201); }
 public function update(UpdateFeasibilityStudyRequest $request,string $id) { $study=$this->service->find($id); $this->authorize('update',$study); return new FeasibilityStudyResource($this->service->update($study,$request->validated())); }
 public function destroy(string $id) { $study=$this->service->find($id); $this->authorize('delete',$study); $this->service->delete($study); return response()->json(['message'=>'Feasibility study deleted successfully']); }
}
