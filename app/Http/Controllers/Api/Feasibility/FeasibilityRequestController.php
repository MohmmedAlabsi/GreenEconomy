<?php
namespace App\Http\Controllers\Api\Feasibility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Feasibility\StoreFeasibilityRequest;
use App\Http\Requests\Feasibility\UpdateFeasibilityRequest;
use App\Http\Resources\Feasibility\FeasibilityRequestResource;
use App\Services\Feasibility\FeasibilityRequestService;
class FeasibilityRequestController extends Controller
{
 public function __construct(private readonly FeasibilityRequestService $service) {}
 public function index() { return FeasibilityRequestResource::collection($this->service->query()->latest()->paginate(50)); }
 public function show(string $id) { return new FeasibilityRequestResource($this->service->find($id)); }
 public function store(StoreFeasibilityRequest $request) { return response()->json(['message'=>'Feasibility request created successfully','data'=>new FeasibilityRequestResource($this->service->create($request->validated(),$request->user()->id))],201); }
 public function update(UpdateFeasibilityRequest $request,string $id) { return new FeasibilityRequestResource($this->service->update($this->service->find($id),$request->validated())); }
 public function destroy(string $id) { $this->service->delete($this->service->find($id)); return response()->json(['message'=>'Feasibility request deleted successfully']); }
}
