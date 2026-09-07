<?php

namespace App\Policies;

use App\Models\Kwt;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DomainPolicy
{
    public function view(User $user, Model $record): bool
    {
        if (! $user->is_active) {
            return false;
        }
        if ($user->role !== 'admin_kwt') {
            return in_array($user->role, ['super_admin', 'admin_koperasi']);
        }
        if ($user->kwt?->status !== 'active') {
            return false;
        }
        $kwt = $record instanceof Kwt ? $record->id : ($record->kwt_id ?? $record->cycle?->kwt_id);

        return $user->kwt_id !== null && (int) $kwt === (int) $user->kwt_id;
    }

    public function update(User $user, Model $record): bool
    {
        if (! $this->view($user, $record)) {
            return false;
        }

        return $user->role !== 'admin_koperasi' || $user->can_manage_cultivation || in_array(class_basename($record), ['Kwt', 'HarvestAvailability', 'Post', 'Gallery']);
    }

    public function delete(User $user, Model $record): bool
    {
        return $this->update($user, $record) && ! ($record instanceof Kwt && $user->role === 'admin_kwt');
    }
}
