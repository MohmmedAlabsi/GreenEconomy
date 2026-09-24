<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    public function queryForUser(int $userId) { return User::query()->whereKey($userId); }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) { $roleId = $data['role_id'] ?? null; unset($data['role_id']); $data['password'] = Hash::make($data['password']); $user = User::create($data); if ($roleId && ($role = Role::find($roleId))) $user->assignRole($role); return $user->load(['role', 'roles', 'region', 'engineerProfile.specialization']); });
    }

    public function update(User $user, array $data): User
    {
        $hasRole = array_key_exists('role_id', $data);
        $roleId = $data['role_id'] ?? null;
        unset($data['role_id']);
        if (!empty($data['password'])) $data['password'] = Hash::make($data['password']); else unset($data['password']);
        return DB::transaction(function () use ($user, $data, $hasRole, $roleId) {
            $user->update($data);
            if ($hasRole) $user->syncRoles($roleId && ($role = Role::find($roleId)) ? [$role] : []);
            return $user->load(['role', 'roles', 'region', 'engineerProfile.specialization']);
        });
    }
}
