<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Licitacao extends Model
{
    use SoftDeletes;
    protected $table = 'licitacao';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_fase',
        'nu_edital',
        'id_modalidade',
        'data_abertura',
        'id_licitador',
        'cnpj_licitador',
        'id_prioridade',
        'objeto',
    ];

    public function modalidade()
    {
        return $this->belongsTo(Modalidade::class, 'id_modalidade')->select(['id', 'nome']);
    }

    public function licitador()
    {
        return $this->belongsTo(Licitador::class, 'id_licitador')->select(['id', 'nome']);
    }

    public function fase()
    {
        return $this->belongsTo(Fase::class, 'id_fase')->select(['id', 'nome']);
    }
    public function prioridade()
    {
        return $this->belongsTo(Prioridade::class, 'id_prioridade')->select(['id', 'nome']);
    }
}
