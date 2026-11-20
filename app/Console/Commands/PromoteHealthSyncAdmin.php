<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:promote-health-sync-admin {email : Email address of an existing account}')]
#[Description('Grant administrator workspace access to an existing account')]
class PromoteHealthSyncAdmin extends Command
{
    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error('No account exists with that email address.');

            return self::FAILURE;
        }

        $user->forceFill([
            'role' => User::ROLE_ADMIN,
            'approval_status' => User::APPROVAL_APPROVED,
            'is_active' => true,
        ])->save();

        $this->info("Administrator access granted to {$user->email}.");

        return self::SUCCESS;
    }
}
