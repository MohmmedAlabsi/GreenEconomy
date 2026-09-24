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
    public function search(array $filters) { $query = User::with(['region', 'role', 'engineerProfile.specialization']); foreach (['status', 'role_id', 'region_id'] as $field) { if (isset($filters[$field])) $query->where($field, $filters[$field]); } if (!empty($filters['search'])) $query->where(fn ($q) => $q->where('name', 'like', '%'.$filters['search'].'%')->orWhere('email', 'like', '%'.$filters['search'].'%')); return $query->latest()->get(); }
    public function find(string|int $id): User { return User::with(['role', 'roles', 'region', 'engineerProfile.specialization', 'consultations'])->findOrFail($id); }
    public function delete(User $user): bool { return DB::transaction(fn () => (bool) $user->delete()); }
    public function updatePassword(User $user, string $password): void { $user->update(['password' => Hash::make($password)]); }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $roleId = $data['role_id'] ?? null;
            unset($data['role_id']);
            if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            if ($roleId && ($role = Role::find($roleId))) $user->assignRole($role);
            return $user->load(['role', 'roles', 'region', 'engineerProfile.specialization']);
        });
    }

    public function update(User $user, array $data, ?UploadedFile $avatar = null): User
    {
        return DB::transaction(function () use ($user, $data, $avatar) {
            $roleId = $data['role_id'] ?? null;
            unset($data['role_id']);
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
