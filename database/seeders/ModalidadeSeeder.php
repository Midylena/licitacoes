<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModalidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('modalidade')->insert([
            [
                'nome' => 'Pregão',
                'created_at' => now()
            ],
            [
                'nome' => 'Concorrência',
                'created_at' => now()
            ],
            [
                'nome' => 'Concurso',
                'created_at' => now()
            ],
            [
                'nome' => 'Leilão',
                'created_at' => now()
            ],
            [
                'nome' => 'Diálogo Competitivo',
                'created_at' => now()
            ],
        ]);
    }
}
