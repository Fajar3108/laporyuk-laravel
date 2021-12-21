<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'role_id' => 1,
            'nik' => "3271112112241101",
            'username' => 'admin123',
            'name' => 'Admin LaporYuk',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ]);
    }
}
