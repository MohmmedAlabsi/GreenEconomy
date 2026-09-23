<?php

namespace App\Policies;

use App\Models\FeasibilityStudy;
use App\Models\User;

class FeasibilityStudyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('studies.view-approved') || $user->hasPermission('studies.manage');
    }

    public function view(User $user, FeasibilityStudy $study): bool
    {
        return $user->hasPermission('studies.manage')
            || ($study->status === 'approved' && $user->hasPermission('studies.view-approved'))
            || ($study->user_id === $user->id && $user->hasPermission('studies.view-own'));
    }

    public function create(User $user): bool { return $user->hasPermission('studies.request') || $user->hasPermission('studies.manage'); }
    public function update(User $user, FeasibilityStudy $study): bool { return $user->hasPermission('studies.manage') || ($study->user_id === $user->id && $user->hasPermission('studies.review-feedback')); }
    public function delete(User $user, FeasibilityStudy $study): bool { return $user->hasPermission('studies.manage'); }
}
