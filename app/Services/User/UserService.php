<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    public function __construct(private readonly SupabaseStorageService $storage) {}

    public function queryForUser(int $userId) { return User::query()->whereKey($userId); }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $roleId = $data['role_id'] ?? null; unset($data['role_id']);
            if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            if ($roleId && ($role = Role::find($roleId))) $user->assignRole($role);
            return $user->load(['role', 'roles', 'region', 'engineerProfile.specialization']);
        });
    }

    public function update(User $user, array $data, ?UploadedFile $avatar = null): User
    {
        return DB::transaction(function () use ($user, $data, $avatar) {
            $roleId = $data['role_id'] ?? null; unset($data['role_id']);
            if ($avatar?->isValid()) {
                if ($user->avatar) $this->storage->delete($user->avatar);
                $path = $avatar->store('avatars/'.$user->id, 'supabase');
                $data['avatar'] = $this->storage->url($path);
            }
            if (!empty($data['password'])) $data['password'] = Hash::make($data['password']); else unset($data['password']);
            $user->update($data);
            if ($roleId !== null) $user->syncRoles(($role = Role::find($roleId)) ? [$role] : []);
            return $user->fresh(['role', 'roles', 'region', 'engineerProfile.specialization']);
        });
    }
}
