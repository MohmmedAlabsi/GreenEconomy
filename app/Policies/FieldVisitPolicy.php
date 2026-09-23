<?php

namespace App\Policies;

use App\Models\FieldVisit;
use App\Models\User;

class FieldVisitPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermission('visits.view-own') || $user->hasPermission('visits.view-assigned') || $user->hasPermission('visits.manage'); }
    public function view(User $user, FieldVisit $visit): bool
    {
        return $user->hasPermission('visits.manage') || ($visit->user_id === $user->id && $user->hasPermission('visits.view-own')) || ($visit->engineer_id === $user->id && $user->hasPermission('visits.view-assigned'));
    }
    public function create(User $user): bool { return $user->hasPermission('visits.create') || $user->hasPermission('visits.manage'); }
    public function update(User $user, FieldVisit $visit): bool { return $user->hasPermission('visits.manage') || ($visit->engineer_id === $user->id && $user->hasPermission('visits.update-step')); }
    public function delete(User $user, FieldVisit $visit): bool { return $user->hasPermission('visits.manage') || ($visit->user_id === $user->id && $user->hasPermission('visits.view-own')); }
    public function rate(User $user, FieldVisit $visit): bool { return $visit->user_id === $user->id && $user->hasPermission('visits.rate'); }
    public function updateStep(User $user, FieldVisit $visit): bool { return $user->hasPermission('visits.manage') || ($visit->engineer_id === $user->id && $user->hasPermission('visits.update-step')); }
    public function uploadReport(User $user, FieldVisit $visit): bool { return $user->hasPermission('visits.manage') || ($visit->engineer_id === $user->id && $user->hasPermission('visits.upload-report')); }
}
