<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fase')->insert([
            [
                'nome' => 'Em edição',
                'status' => -1,
                'created_at' => now()
            ],
            [
                'nome' => 'Descartada / Cancelada',
                'status' => 0,
                'created_at' => now()
            ],
            [
                'nome' => 'Processada / Em andamento',
                'status' => 1,
                'created_at' => now()
            ],
        ]);
    }
}
