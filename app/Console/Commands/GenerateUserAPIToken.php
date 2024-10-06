<?php

namespace App\Console\Commands;

use App\Enums\TokenNames;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateUserAPIToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-user-api-token {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerates a token for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address.');
            return;
        }

        $user = User::where('email', $email)->first();
        // Check if the user already exists
        if (!$user) {
            $this->error("A user with email: $email was not found");
            return;
        }

        $tokenAbilities = $this->choice(
            'Select the token abilities',
            User::getAbilityChoices(),
            0,
            null,
            true,
        );

        $token = $user->createToken($email, $tokenAbilities)->plainTextToken;

        $this->info("Your new token is: $token");

    }
}
