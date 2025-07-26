<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fase;

class FaseController extends Controller
{
    public function show()
    {
        $fase = fase::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'fase' => $fase,
        ]);
    }
}
