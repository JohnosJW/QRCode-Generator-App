<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'Admin'
        ]);

        Role::create([
            'name' => 'Moderator'
        ]);

        Role::create([
            'name' => 'Webmaster'
        ]);

        Role::create([
            'name' => 'Buyer'
        ]);
    }
}
