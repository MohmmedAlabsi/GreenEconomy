<?php
namespace App\Http\Controllers\Api\Engineer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Engineer\StoreConsultationRequest;
use App\Http\Requests\Engineer\UpdateConsultationRequest;
use App\Http\Resources\Engineer\ConsultationResource;
use App\Services\Engineer\ConsultationService;
class ConsultationController extends Controller
{
 public function __construct(private readonly ConsultationService $service) {}
 public function index() { return ConsultationResource::collection($this->service->query()->latest()->paginate(10)); }
 public function show(string $id) { return new ConsultationResource($this->service->find($id)); }
 public function store(StoreConsultationRequest $request) { return response()->json(['message'=>'Consultation created successfully','data'=>new ConsultationResource($this->service->create($request->validated(),$request->user()->id))],201); }
 public function update(UpdateConsultationRequest $request,string $id) { return new ConsultationResource($this->service->update($this->service->find($id),$request->validated())); }
 public function destroy(string $id) { $this->service->delete($this->service->find($id)); return response()->json(['message'=>'Consultation deleted successfully']); }
}
