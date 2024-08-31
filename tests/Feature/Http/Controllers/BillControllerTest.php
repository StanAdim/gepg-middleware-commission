<?php
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

test('creating a wrong bill with wrong json body returns 422', function () {
    Sanctum::actingAs(
        User::factory()->create(),
        ['*']
    );

    $response = $this->postJson(route('bill.store'));
    $response
        ->assertStatus(422);

});

test('creating a bill with correct json body returns 201 with data', function () {
    Sanctum::actingAs(
        User::factory()->create(),
        ['*']
    );

    $faker = Faker\Factory::create();

    $tinNumber = $faker->randomNumber(9, true);
    $spCode = $faker->word();
    $amount = $faker->randomFloat(2, 100, 100_000_000);
    $data = [
        'sp_code' => $spCode,
        'description' => 'Payment for goods',
        'customer' => [
            'name' => $faker->name(),
            'tin_number' => (string) $tinNumber,
            'id_number' => (string) $tinNumber,
            'id_type' => 1,
        ],
        'expires_on' => Carbon::parse($faker->dateTime())->toISOString(),
        'amount' => $amount,
        'callback_url' => $faker->url(),
        'items' => [
            [
                'reference' => (string) $faker->randomNumber(9),
                'sub_sp_code' => $spCode,
                'gfs_code' => $faker->word(),
                'amount' => $amount,
            ],
        ],
    ];

    $response = $this->postJson(route('bill.store'), $data);
    $response
        ->assertStatus(201)
        ->assertJson(fn(AssertableJson $json) => $json->has('data')
            ->where('data.id', 1));
});
