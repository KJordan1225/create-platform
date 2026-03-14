<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;

class CreatorAccessService
{
    public function canViewPost(?User $viewer, Post $post): bool
    {
        if (! $post->is_locked) {
            return true;
        }

        if (! $viewer) {
            return false;
        }

        if ($viewer->id === $post->user_id) {
            return true;
        }

        if ($viewer->isAdmin()) {
            return true;
        }

        return $viewer->hasActiveSubscriptionTo($post->user);
    }

    public function canInteractWithCreator(?User $viewer, User $creator): bool
    {
        if (! $creator->isApprovedCreator() || ! $creator->is_active) {
            return false;
        }

        if (! $viewer) {
            return false;
        }

        if ($viewer->id === $creator->id) {
            return false;
        }

        return true;
    }
}
