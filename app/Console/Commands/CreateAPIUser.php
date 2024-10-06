<?php

namespace App\Console\Commands;

use App\Enums\TokenNames;
use App\Enums\UserType;
use App\Models\User;
use Hash;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class CreateAPIUser extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-api-user {name} {email}';

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

        $password = $this->secret('password');

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

        $tokenAbilities = $this->choice(
            'Select the token abilities',
            User::getAbilityChoices(),
            0,
            null,
            true,
        );

        // Create an API token for the user
        $token = $user->createToken($email, $tokenAbilities)->plainTextToken;

        $tokenAbilities = implode(', ', $tokenAbilities);
        $this->info("Your API Key is: {$token}\n\tAbilities: {$tokenAbilities}");
    }
}
