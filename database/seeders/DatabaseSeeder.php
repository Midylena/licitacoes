<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ModalidadeSeeder::class,
            LicitadorSeeder::class,
            PrioridadeSeeder::class,
            FaseSeeder::class,
            LicitacaoSeeder::class,
        ]);
    }
}
