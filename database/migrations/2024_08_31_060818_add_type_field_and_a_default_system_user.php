<?php

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('type', 64);
        });

        $systemUserName = env('SYSTEM_USER_NAME');
        DB::table('users')->insert([
            'name' => $systemUserName,
            'email' => env('SYSTEM_USER_EMAIL'),
            'password' => Hash::make(env('SYSTEM_USER_PASSWORD', $systemUserName)),
            'type' => UserType::System,
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::where('name', env('SYSTEM_USER_NAME'))->delete();
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
