<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prioridade extends Model
{
    protected $table = 'prioridade';
    protected $fillable = [
        'nome',
    ];

    public function licitacoes()
    {
        return $this->hasMany(Licitacao::class, 'id_prioridade');
    }
}
