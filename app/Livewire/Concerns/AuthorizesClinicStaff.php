<?php

namespace App\Livewire\Concerns;

trait AuthorizesClinicStaff
{
    public function bootAuthorizesClinicStaff(): void
    {
        $user = auth()->user();

        abort_unless($user?->hasWorkspaceAccess() && $user->isClinicStaff(), 403);
    }
}
