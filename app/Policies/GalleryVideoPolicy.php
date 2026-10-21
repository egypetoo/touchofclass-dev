<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GalleryVideo;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class GalleryVideoPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GalleryVideo');
    }

    public function view(AuthUser $authUser, GalleryVideo $galleryVideo): bool
    {
        return $authUser->can('View:GalleryVideo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GalleryVideo');
    }

    public function update(AuthUser $authUser, GalleryVideo $galleryVideo): bool
    {
        return $authUser->can('Update:GalleryVideo');
    }

    public function delete(AuthUser $authUser, GalleryVideo $galleryVideo): bool
    {
        return $authUser->can('Delete:GalleryVideo');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:GalleryVideo');
    }

    public function restore(AuthUser $authUser, GalleryVideo $galleryVideo): bool
    {
        return $authUser->can('Restore:GalleryVideo');
    }

    public function forceDelete(AuthUser $authUser, GalleryVideo $galleryVideo): bool
    {
        return $authUser->can('ForceDelete:GalleryVideo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GalleryVideo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GalleryVideo');
    }

    public function replicate(AuthUser $authUser, GalleryVideo $galleryVideo): bool
    {
        return $authUser->can('Replicate:GalleryVideo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GalleryVideo');
    }
}
