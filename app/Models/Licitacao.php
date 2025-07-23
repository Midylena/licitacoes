<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Licitacao extends Model
{
    use SoftDeletes;
    protected $table = 'licitacao';
    protected $primaryKey = 'id_lic';
    protected $fillable = [
        'nu_fase',
        'nu_edital',
        'id_mod',
        'data_abertura',
        'nome_licitador',
        'cnpj_licitador',
        'prioridade',
        'objeto',
    ];
}
