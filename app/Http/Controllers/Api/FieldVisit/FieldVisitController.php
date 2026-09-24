<?php
namespace App\Http\Controllers\Api\FieldVisit;
use App\Http\Controllers\Controller;
use App\Http\Requests\FieldVisit\StoreFieldVisitRequest;
use App\Http\Requests\FieldVisit\UpdateFieldVisitRequest;
use App\Http\Resources\FieldVisit\FieldVisitResource;
use App\Services\FieldVisit\FieldVisitService;
use Illuminate\Http\Request;
class FieldVisitController extends Controller
{
 public function __construct(private readonly FieldVisitService $service) {}
 public function index(Request $request) { $this->authorize('viewAny',\App\Models\FieldVisit::class); return FieldVisitResource::collection($this->service->index($request->user()->id,$request->all())); }
 public function show(string $id) { $visit=$this->service->find($id); $this->authorize('view',$visit); return new FieldVisitResource($visit); }
 public function store(StoreFieldVisitRequest $request) { $this->authorize('create',\App\Models\FieldVisit::class); return response()->json(['message'=>'Field visit created successfully','data'=>new FieldVisitResource($this->service->createVisit($request->validated(),$request->user()->id))],201); }
 public function update(UpdateFieldVisitRequest $request,string $id) { $visit=$this->service->find($id); $this->authorize('update',$visit); return new FieldVisitResource($this->service->update($visit,$request->validated(),$request->user()->id)); }
 public function destroy(string $id) { $visit=$this->service->find($id); $this->authorize('delete',$visit); $this->service->destroy($visit); return response()->json(['message'=>'Field visit deleted successfully']); }
}
