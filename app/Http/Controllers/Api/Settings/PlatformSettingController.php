<?php
namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePlatformSettingRequest;
use App\Http\Resources\Settings\PlatformSettingResource;
use App\Services\Settings\PlatformSettingService;
class PlatformSettingController extends Controller
{
 public function __construct(private readonly PlatformSettingService $service) {}
 public function index() { return new PlatformSettingResource($this->service->first()); }
 public function update(UpdatePlatformSettingRequest $request) { return new PlatformSettingResource($this->service->update($this->service->first(), $request->validated(), $request->file('asset'))); }
}
