<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('modalidade', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique();
            $table->timestamps();
        });

        Schema::create('licitacao', function (Blueprint $table) {
            $table->bigIncrements('id_lic');
            $table->tinyInteger('nu_fase');
            $table->string('nu_edital', 80);
            $table->foreignId('id_mod')->constrained('modalidade');
            $table->timestamp('data_abertura');
            $table->string('nome_licitador', 255)->nullable();
            $table->string('cnpj_licitador', 18)->nullable();
            $table->tinyInteger('prioridade');
            $table->text('objeto');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licitacao');
    }
};
