<?php
namespace App\Http\Controllers\Engineer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Engineer\StoreEngineerProfileRequest;
use App\Http\Requests\Engineer\UpdateEngineerProfileRequest;
use App\Http\Resources\Engineer\EngineerProfileResource;
use App\Models\EngineerProfile;
use App\Services\Engineer\EngineerProfileService;
use Illuminate\Http\Request;
class EngineerProfileController extends Controller
{
 public function __construct(private readonly EngineerProfileService $service) {}
 public function index() { return EngineerProfileResource::collection($this->service->query()->latest()->get()); }
 public function show(Request $request, ?string $id = null) { return new EngineerProfileResource($this->service->findForUserOrFail($id ?? $request->user()->id)); }
 public function store(StoreEngineerProfileRequest $request) { return new EngineerProfileResource($this->service->save($request->user()->id, $request->validated(), $request->file('cv_file'), $request->file('avatar'))); }
 public function update(UpdateEngineerProfileRequest $request, string $id) { $profile = $this->service->find($id); $this->authorize('update', $profile); return new EngineerProfileResource($this->service->update($profile, $request->validated(), $request->file('cv_file'), $request->file('certificate'))); }
 public function destroy(string $id) { $profile = $this->service->find($id); $this->authorize('delete', $profile); $this->service->delete($profile); return response()->json(['message' => 'Engineer profile deleted successfully']); }
}
