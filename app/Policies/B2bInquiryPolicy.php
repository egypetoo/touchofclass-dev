<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\B2bInquiry;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class B2bInquiryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:B2bInquiry');
    }

    public function view(AuthUser $authUser, B2bInquiry $b2bInquiry): bool
    {
        return $authUser->can('View:B2bInquiry');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:B2bInquiry');
    }

    public function update(AuthUser $authUser, B2bInquiry $b2bInquiry): bool
    {
        return $authUser->can('Update:B2bInquiry');
    }

    public function delete(AuthUser $authUser, B2bInquiry $b2bInquiry): bool
    {
        return $authUser->can('Delete:B2bInquiry');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:B2bInquiry');
    }

    public function restore(AuthUser $authUser, B2bInquiry $b2bInquiry): bool
    {
        return $authUser->can('Restore:B2bInquiry');
    }

    public function forceDelete(AuthUser $authUser, B2bInquiry $b2bInquiry): bool
    {
        return $authUser->can('ForceDelete:B2bInquiry');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:B2bInquiry');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:B2bInquiry');
    }

    public function replicate(AuthUser $authUser, B2bInquiry $b2bInquiry): bool
    {
        return $authUser->can('Replicate:B2bInquiry');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:B2bInquiry');
    }
}
