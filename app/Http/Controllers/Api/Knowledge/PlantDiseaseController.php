<?php

namespace App\Http\Controllers\Knowledge;

use App\Http\Controllers\Controller;
use App\Http\Requests\Knowledge\StorePlantDiseaseRequest;
use App\Http\Requests\Knowledge\UpdatePlantDiseaseRequest;
use App\Http\Resources\Knowledge\PlantDiseaseResource;
use App\Models\PlantDisease;
use App\Services\Knowledge\PlantDiseaseService;

class PlantDiseaseController extends Controller
{
    public function __construct(private readonly PlantDiseaseService $service) {}

    public function index() { return PlantDiseaseResource::collection($this->service->query()->latest()->get()); }
    public function show(string $id) { return new PlantDiseaseResource($this->service->find($id)); }
    public function store(StorePlantDiseaseRequest $request) { return response()->json(['message' => 'Disease created successfully', 'data' => new PlantDiseaseResource($this->service->create($request->validated(), $request->file('image')))], 201); }
    public function update(UpdatePlantDiseaseRequest $request, string $id) { return response()->json(['message' => 'Disease updated successfully', 'data' => new PlantDiseaseResource($this->service->update($this->service->find($id), $request->validated(), $request->file('image')))]); }
    public function destroy(string $id) { $this->service->destroy($this->service->find($id)); return response()->json(['message' => 'Disease deleted successfully']); }
}
