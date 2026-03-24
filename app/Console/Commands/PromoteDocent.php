<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PromoteDocent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docent:promote
                            {email : Email address of the user}
                            {--create : Create the user if they do not exist}
                            {--name= : Name to use when creating the user}
                            {--password= : Password to use when creating the user}
                            {--force : Allow promotion when a docent already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote a user to docent (optionally create the user)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (User::query()->where('is_docent', true)->exists() && ! $this->option('force')) {
            $this->error('A docent already exists. Use --force to promote another user.');

            return self::FAILURE;
        }

        $email = (string) $this->argument('email');
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            if (! $this->option('create')) {
                $this->error('User not found. Use --create to create them.');

                return self::FAILURE;
            }

            $name = (string) ($this->option('name') ?: $email);
            $password = (string) ($this->option('password') ?: Str::random(12));

            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_docent' => true,
            ]);

            $this->info('User created and promoted to docent.');
            $this->line('Generated password: '.$password);

            return self::SUCCESS;
        }

        $user->forceFill(['is_docent' => true])->save();

        $this->info('User promoted to docent.');

        return self::SUCCESS;
    }
}
