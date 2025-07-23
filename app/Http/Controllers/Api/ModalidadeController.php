<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Modalidade;

class ModalidadeController extends Controller
{
    public function show()
    {
        $modalidade = Modalidade::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'modalidade' => $modalidade,
        ]);
    }
}
