<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    public function queryForUser(int $userId) { return User::query()->whereKey($userId); }
    public function search(array $filters) { $query = User::with(['region', 'role', 'engineerProfile.specialization']); foreach (['status', 'role_id', 'region_id'] as $field) { if (isset($filters[$field])) $query->where($field, $filters[$field]); } if (!empty($filters['search'])) $query->where(fn ($q) => $q->where('name', 'like', '%'.$filters['search'].'%')->orWhere('email', 'like', '%'.$filters['search'].'%')); return $query->latest()->get(); }
    public function find(string|int $id): User { return User::with(['role', 'roles', 'region', 'engineerProfile.specialization', 'consultations'])->findOrFail($id); }
    public function delete(User $user): bool { return DB::transaction(fn () => (bool) $user->delete()); }
    public function updatePassword(User $user, string $password): void { $user->update(['password' => Hash::make($password)]); }

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
