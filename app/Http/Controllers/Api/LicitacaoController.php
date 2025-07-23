<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LicitacaoRequest;
use App\Models\Licitacao;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LicitacaoController extends Controller
{
    public function index(): JsonResponse
    {
        $licitacoes = Licitacao::orderBy('id_lic', 'asc')->paginate(10);

        return response()->json([
            'status' => true,
            'licitacoes' => $licitacoes,
        ]);
    }

    public function show(Licitacao $licitacao): JsonResponse
    {
        return response()->json([
            'status' => true,
            'licitacao' => $licitacao,
        ]);
    }

    public function store(LicitacaoRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {

            $licitacao = Licitacao::create([
                'nu_fase' => $request->input('nu_fase'),
                'nu_edital' => $request->input('nu_edital'),
                'id_mod' => $request->input('id_mod'),
                'data_abertura' => $request->input('data_abertura'),
                'nome_licitador' => $request->input('nome_licitador'),
                'cnpj_licitador' => $request->input('cnpj_licitador'),
                'prioridade' => $request->input('prioridade'),
                'objeto' => $request->input('objeto'),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'licitacao' => $licitacao,
                'message' => "Licitação casdastrada!",
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => "Licitação não casdastrada!",
            ]);
        }
    }

    public function update(LicitacaoRequest $request, Licitacao $licitacao): JsonResponse
    {
        DB::beginTransaction();
        try {
            $licitacao->update([
                'nu_fase' => $request->input('nu_fase'),
                'nu_edital' => $request->input('nu_edital'),
                'id_mod' => $request->input('id_mod'),
                'data_abertura' => $request->input('data_abertura'),
                'nome_licitador' => $request->input('nome_licitador'),
                'cnpj_licitador' => $request->input('cnpj_licitador'),
                'prioridade' => $request->input('prioridade'),
                'objeto' => $request->input('objeto'),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'licitacao' => $licitacao,
                'message' => "Licitação Editada!",
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => "Licitação não Editada!",
            ]);
        }
    }

    public function destroy(Licitacao $licitacao): JsonResponse
    {
        try {
            $licitacao->delete();

            return response()->json([
                'status' => true,
                'licitacao' => $licitacao,
                'message' => "Licitação Deletada!",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Licitação não Deletada!",
            ]);
        }
    }
}
