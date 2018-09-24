<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Account::class, function (Faker $faker) {
    $withdrawalMethod = array('bank', 'paypal', 'stripe', 'paystask');
    $users = App\Models\User::pluck('id')->all();

    return [
        'user_id' => $faker->unique()->randomElement($users),
        'balance' => rand(200, 5000),
        'total_credit' => rand(50, 5000),
        'total_debit' => rand(0, 200),
        'withdrawal_method' => $withdrawalMethod[rand(0, 3)],
        'payment_email' => $faker->email,
        'bank_name' => $faker->word,
        'bank_branch' => $faker->state,
        'bank_account' => $faker->bankAccountNumber,
        'applied_for_payout' => $faker->numberBetween(0, 1),
        'paid' => $faker->numberBetween(0, 1),
        'country' => $faker->country,
        'other_details' => $faker->paragraph(2, true),
    ];
});
