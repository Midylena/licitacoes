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
                'created_at' => now()
            ],
            [
                'nome' => 'Descartada / Cancelada',
                'created_at' => now()
            ],
            [
                'nome' => 'Publicada / Em andamento',
                'created_at' => now()
            ],
        ]);
    }
}
