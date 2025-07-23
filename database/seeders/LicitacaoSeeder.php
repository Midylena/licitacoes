<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LicitacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('pt_BR');

        foreach (range(1, 10) as $i) {
            DB::table('licitacao')->insert([
                'nu_fase' => $faker->randomElement([-1, 0, 1]),
                'nu_edital' => strtoupper(Str::random(5)) . '/' . $faker->year(),
                'id_mod' => rand(1, 5), // Certifique-se de ter modalidades com esses IDs
                'data_abertura' => $faker->dateTimeBetween('now', '+30 days'),
                'nome_licitador' => $faker->company(),
                'cnpj_licitador' => $faker->cnpj(),
                'prioridade' => rand(1, 10),
                'objeto' => $faker->sentence(12),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
