<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Licitador;

class LicitadorController extends Controller
{
    public function show()
    {
        $licitador = Licitador::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'licitador' => $licitador,
        ]);
    }
}
