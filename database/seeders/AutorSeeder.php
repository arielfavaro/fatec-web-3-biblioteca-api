<?php

namespace Database\Seeders;

use App\Models\Autor;
use App\Models\Livro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AutorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Autor::factory(5)->create()->each(function ($autor) {
            Livro::factory(rand(1, 3))->create(
                [
                    'autor_id' => $autor->id
                ]
            );
        });
    }
}
