<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Generazione di 10 task fake
        for ($i = 0; $i < 10; $i++) {
            $title = 'Task ' . ($i+1);
            $slug = Str::slug(Str::uuid() . '-' . $title); // Generazione dello slug
            $difficulty = rand(1, 20); // Generazione della difficoltà
            $priority = collect(['low', 'medium', 'high', 'very high'])->random(); // Generazione della priorità
            $user = DB::table('users')->inRandomOrder()->first(); // Selezione casuale di un utente
            $project = DB::table('projects')->inRandomOrder()->first(); // Selezione casuale di un progetto
            DB::table('tasks')->insert([
                'id' => Str::uuid(), // Generazione dell'UUID
                'title' => $title, // Generazione del titolo
                'user_id' => $user->id, // Colonna che fa riferimento ad un utente della tabella "users"
                'project_id' => $project->id, // Colonna che fa riferimento ad un progetto della tabella "projects"
                'description' => 'This is a fake task created for testing purposes.', // Descrizione del task
                'slug' => $slug, // Generazione dello slug
                'difficulty' => $difficulty, // Generazione della difficoltà
                'priority' => $priority, // Generazione della priorità
            ]);
        }
    }
}
