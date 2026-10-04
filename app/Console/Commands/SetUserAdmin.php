<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetUserAdmin extends Command
{
    protected $signature = 'user:admin {email} {--revoke : Tarik balik akses admin}';

    protected $description = 'Grant (or --revoke) master-admin access for a user by email.';

    public function handle(): int
    {
        $user = User::where('email', strtolower(trim($this->argument('email'))))->first();

        if (! $user) {
            $this->error('User tidak dijumpai.');

            return 1;
        }

        $user->is_admin = ! $this->option('revoke');
        $user->save();

        $this->info(($user->is_admin ? 'Admin diberi kepada ' : 'Admin ditarik dari ').$user->email);

        return 0;
    }
}
