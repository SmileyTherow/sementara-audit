<?php

namespace App\Policies;

use App\Models\CultivationCycle;
use App\Models\Harvest;
use App\Models\HarvestAvailability;
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

        if ($user->isSuperAdmin() || $user->isKoperasi()) {
            return true;
        }

        if (! $user->isOperatorKwt()) {
            return false;
        }

        if (! $user->kwt || ! $user->kwt->is_active) {
            return false;
        }

        return $this->recordBelongsToKwt($record, $user->kwt_id);
    }

    public function update(User $user, Model $record): bool
    {
        if (! $this->view($user, $record)) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isKoperasi()) {
            return $record instanceof HarvestAvailability;
        }

        return $user->isOperatorKwt()
            && ! $record instanceof Kwt;
    }

    public function delete(User $user, Model $record): bool
    {
        if (! $this->update($user, $record)) {
            return false;
        }

        if ($user->isOperatorKwt() && $record instanceof Kwt) {
            return false;
        }

        return true;
    }

    private function recordBelongsToKwt(
        Model $record,
        ?int $kwtId
    ): bool {
        if (! $kwtId) {
            return false;
        }

        if ($record instanceof Kwt) {
            return (int) $record->id === (int) $kwtId;
        }

        if ($record instanceof CultivationCycle) {
            return (int) $record->kwt_id === (int) $kwtId;
        }

        if ($record instanceof Harvest) {
            return (int) $record->cultivationCycle?->kwt_id === (int) $kwtId;
        }

        if ($record instanceof HarvestAvailability) {
            return (int) $record->harvest?->cultivationCycle?->kwt_id
                === (int) $kwtId;
        }

        if (isset($record->kwt_id)) {
            return (int) $record->kwt_id === (int) $kwtId;
        }

        return false;
    }
}
