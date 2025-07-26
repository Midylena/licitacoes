<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LicitadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('pt_BR');

        foreach (range(1, 10) as $i) {
            DB::table('licitador')->insert([
                'nome' => $faker->company(),
                'created_at' => now()
            ]);
        }
    }
}
