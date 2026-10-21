<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GalleryImage;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class GalleryImagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GalleryImage');
    }

    public function view(AuthUser $authUser, GalleryImage $galleryImage): bool
    {
        return $authUser->can('View:GalleryImage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GalleryImage');
    }

    public function update(AuthUser $authUser, GalleryImage $galleryImage): bool
    {
        return $authUser->can('Update:GalleryImage');
    }

    public function delete(AuthUser $authUser, GalleryImage $galleryImage): bool
    {
        return $authUser->can('Delete:GalleryImage');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:GalleryImage');
    }

    public function restore(AuthUser $authUser, GalleryImage $galleryImage): bool
    {
        return $authUser->can('Restore:GalleryImage');
    }

    public function forceDelete(AuthUser $authUser, GalleryImage $galleryImage): bool
    {
        return $authUser->can('ForceDelete:GalleryImage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GalleryImage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GalleryImage');
    }

    public function replicate(AuthUser $authUser, GalleryImage $galleryImage): bool
    {
        return $authUser->can('Replicate:GalleryImage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GalleryImage');
    }
}
