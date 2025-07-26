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

        Schema::create('licitador', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique();
            $table->timestamps();
        });

        Schema::create('prioridade', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique();
            $table->timestamps();
        });

        Schema::create('fase', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique();
            $table->timestamps();
        });

        Schema::create('licitacao', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('id_fase')->constrained('fase');
            $table->string('nu_edital', 80);
            $table->foreignId('id_modalidade')->constrained('modalidade');
            $table->timestamp('data_abertura');
            $table->foreignId('id_licitador')->nullable()->constrained('licitador');
            $table->string('cnpj_licitador', 18)->nullable();
            $table->foreignId('id_prioridade')->constrained('prioridade');
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
        Schema::dropIfExists('modalidade');
        Schema::dropIfExists('licitador');
        Schema::dropIfExists('prioridade');
        Schema::dropIfExists('licitacao');
        Schema::dropIfExists('fase');
    }
};
