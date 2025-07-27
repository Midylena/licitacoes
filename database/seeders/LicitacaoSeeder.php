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

        foreach (range(1, 200) as $i) {
            DB::table('licitacao')->insert([
                'id_fase' => rand(1, 3),
                'nu_edital' => strtoupper(Str::random(5)) . '/' . $faker->year(),
                'id_modalidade' => rand(1, 5),
                'data_abertura' => $faker->dateTimeBetween('now', '+30 days'),
                'id_licitador' => rand(1, 10),
                'cnpj_licitador' => $faker->cnpj(),
                'id_prioridade' => rand(1, 5),
                'objeto' => $faker->sentence(12),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
