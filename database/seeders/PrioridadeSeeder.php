<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioridadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('prioridade')->insert([
            [
                'nome' => 'Prioridade Muito Baixa',
                'created_at' => now()
            ],
            [
                'nome' => 'Prioridade Baixa',
                'created_at' => now()
            ],
            [
                'nome' => 'Prioridade Média',
                'created_at' => now()
            ],
            [
                'nome' => 'Prioridade Alta',
                'created_at' => now()
            ],
            [
                'nome' => 'Prioridade Muito Alta',
                'created_at' => now()
            ],
        ]);
    }
}
