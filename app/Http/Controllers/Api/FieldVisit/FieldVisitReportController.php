<?php
namespace App\Http\Controllers\Api\FieldVisit;
use App\Http\Controllers\Controller;
use App\Http\Requests\FieldVisit\StoreFieldVisitReportRequest;
use App\Http\Resources\FieldVisit\FieldVisitReportResource;
use App\Services\FieldVisit\FieldVisitReportService;
class FieldVisitReportController extends Controller
{
 public function __construct(private readonly FieldVisitReportService $service) {}
 public function index() { return FieldVisitReportResource::collection($this->service->query()->latest()->paginate(50)); }
 public function store(StoreFieldVisitReportRequest $request,?string $id=null) { return response()->json(['success'=>true,'message'=>'Report saved successfully','data'=>new FieldVisitReportResource($this->service->save($request->validated(),$request->user()->id,$id))],201); }
}
