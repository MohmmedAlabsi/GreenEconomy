<?php
namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\User\UserResource;
use App\Services\User\UserService;
use Illuminate\Http\Request;
class UserController extends Controller
{
 public function __construct(private readonly UserService $service) {}
 public function index(Request $request) { return UserResource::collection($this->service->search($request->only(['search','status','role_id','region_id']))); }
 public function show(string $id) { return new UserResource($this->service->find($id)); }
 public function store(StoreUserRequest $request) { return response()->json(['message'=>'User created successfully','data'=>new UserResource($this->service->create($request->validated()))],201); }
 public function update(UpdateUserRequest $request,string $id) { return response()->json(['message'=>'User updated successfully','data'=>new UserResource($this->service->update($this->service->find($id),$request->validated(),$request->file('avatar')))]); }
 public function destroy(Request $request,string $id) { abort_if((int)$request->user()->id===(int)$id,422,'Cannot delete the current account.'); $this->service->delete($this->service->find($id)); return response()->json(['message'=>'User deleted successfully']); }
 public function updatePassword(UpdatePasswordRequest $request) { $this->service->updatePassword($request->user(),$request->validated()['password']); return response()->json(['message'=>'Password updated successfully']); }
}
