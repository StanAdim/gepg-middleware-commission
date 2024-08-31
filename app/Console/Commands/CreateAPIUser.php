<?php

namespace App\Console\Commands;

use App\Enums\TokenNames;
use App\Enums\UserType;
use App\Models\User;
use Hash;
use Illuminate\Console\Command;

class CreateAPIUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-api-user {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an API user for the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address.');
            return;
        }

        // Check if the user already exists
        if (User::where('email', $email)->exists()) {
            $this->error('User with this email already exists.');
            return;
        }

        /**
         * Create the user
         * @var User $user
         */
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'type' => UserType::Normal,
        ]);

        $this->info("User {$user->name} has been successfully registered with email {$user->email}.");

        // Create an API token for the user
        $token = $user->createToken($email)->plainTextToken;

        $this->info("Your API Key is: {$token}");
    }
}
