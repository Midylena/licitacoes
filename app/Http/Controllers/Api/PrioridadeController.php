<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prioridade;

class PrioridadeController extends Controller
{
    public function show()
    {
        $prioridade = prioridade::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'prioridade' => $prioridade,
        ]);
    }
}
