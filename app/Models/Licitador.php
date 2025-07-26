<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licitador extends Model
{
    protected $table = 'licitador';
    protected $fillable = [
        'nome',
    ];

    public function licitacoes()
    {
        return $this->hasMany(Licitacao::class, 'id_licitador');
    }
}
