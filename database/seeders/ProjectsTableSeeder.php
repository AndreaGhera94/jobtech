<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Generazione di 10 progetti fake
        for ($i = 0; $i < 10; $i++) {
            $title = 'Project ' . ($i+1);
            $slug = Str::slug(Str::uuid() . '-' . $title); // Generazione dello slug
            DB::table('projects')->insert([
                'id' => Str::uuid(), // Generazione dell'UUID
                'title' => $title, // Generazione del titolo
                'description' => 'This is a fake project created for testing purposes.', // Descrizione del progetto
                'slug' => $slug, // Generazione dello slug
            ]);
        }
    }
}
