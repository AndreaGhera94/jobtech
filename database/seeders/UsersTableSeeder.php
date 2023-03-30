<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Generazione di 10 utenti fake
        for ($i = 0; $i < 10; $i++) {
            DB::table('users')->insert([
                'id' => Str::uuid(), // Generazione dell'UUID
                'email' => Str::random(10) . '@example.com', // Generazione di un'email casuale
                'username' => Str::random(8), // Generazione di uno username casuale
                'name' => Str::random(12), // Generazione di un nome casuale
                'password' => bcrypt('password'), // Impostazione di una password di default
            ]);
        }
    }
}
