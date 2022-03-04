<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pass ='Password1';
        DB::table('users')->insert([
            'name' => 'sergio',
            'email' => 'sergio@app.com',
            'password' => Hash::make($pass),
            'role' => 'Administrador'
        ]);

        DB::table('users')->insert([
            'name' => 'sergioparticular',
            'email' => 'sergio1@app.com',
            'password' => Hash::make($pass),
            'role' => 'Particular'
        ]);
    }
}
