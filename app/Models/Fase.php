<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fase extends Model
{
    protected $table = 'fase';
    protected $fillable = [
        'nome',
    ];

    public function licitacoes()
    {
        return $this->hasMany(Licitacao::class, 'id_fase');
    }
}
