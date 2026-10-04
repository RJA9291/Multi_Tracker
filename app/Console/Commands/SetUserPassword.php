<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetUserPassword extends Command
{
    protected $signature = 'user:password {email} {password}';

    protected $description = 'Set (reset) a user password by email; creates the account if it does not exist.';

    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));
        $password = (string) $this->argument('password');

        if (strlen($password) < 6) {
            $this->error('Password mesti sekurang-kurangnya 6 aksara.');

            return 1;
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->password = Hash::make($password);
            $user->save();
            $this->info("Password dikemaskini untuk {$email}.");
        } else {
            User::create([
                'name' => explode('@', $email)[0],
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $this->info("Akaun baharu dicipta untuk {$email}.");
        }

        return 0;
    }
}
