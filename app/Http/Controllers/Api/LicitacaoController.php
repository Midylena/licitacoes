<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LicitacaoRequest;
use App\Models\Licitacao;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LicitacaoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Licitacao::with(['modalidade:id,nome', 'licitador:id,nome', 'fase:id,nome', 'prioridade:id,nome']);

        //Filtro global
        if ($request->filled('filtro')) {
            $termo = strtolower($request->input('filtro'));
            $query->where(function ($q) use ($termo) {
                $q->whereRaw('LOWER(nu_edital) LIKE ?', ["%{$termo}%"])
                    ->orWhereHas('licitador', fn($q) => $q->whereRaw('LOWER(nome) LIKE ?', ["%{$termo}%"]))
                    ->orWhereRaw('LOWER(cnpj_licitador) LIKE ?', ["%{$termo}%"])
                    ->orWhereHas('modalidade', fn($q) => $q->whereRaw('LOWER(nome) LIKE ?', ["%{$termo}%"]))
                    ->orWhereHas('prioridade', fn($q) => $q->whereRaw('LOWER(nome) LIKE ?', ["%{$termo}%"]))
                    ->orWhereHas('fase', fn($q) => $q->whereRaw('LOWER(nome) LIKE ?', ["%{$termo}%"]));
            });
        }

        //Filtro por período
        if ($request->filled('dataInicio') && $request->filled('dataFim')) {
            $inicio = $request->input('dataInicio');
            $fim = $request->input('dataFim');
            $query->whereBetween('data_abertura', [$inicio, $fim]);
        }

        //Ordenação
        if ($request->filled('ordenarPor')) {
            $campo = $request->input('ordenarPor');
            $ordem = $request->input('ordem', 'asc');

            if (str_contains($campo, '.')) {
                [$relacao, $coluna] = explode('.', $campo);
                $tabela = match ($relacao) {
                    'modalidade' => 'modalidade',
                    'licitador' => 'licitador',
                    'fase' => 'fase',
                    'prioridade' => 'prioridade',
                    default => null,
                };

                if ($tabela) {
                    $query->join($tabela, "{$tabela}.id", '=', "licitacao.id_{$relacao}")
                        ->orderBy("{$tabela}.{$coluna}", $ordem)
                        ->select('licitacao.*');
                }
            } else {
                $query->orderBy($campo, $ordem);
            }
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);
        $licitacoes = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'licitacoes' => $licitacoes,
        ]);
    }



    public function listarTodas(): JsonResponse
    {
        $licitacoes = Licitacao::with(['modalidade:id,nome', 'licitador:id,nome', 'fase:id,nome', 'prioridade:id,nome'])->orderBy('id', 'asc')->get();
        return response()->json([
            'status' => true,
            'licitacoes' => $licitacoes,
        ], 200);
    }
    public function show(Licitacao $licitacao): JsonResponse
    {
        $licitacao->load('modalidade', 'licitador', 'fase', 'prioridade');
        return response()->json([
            'status' => true,
            'licitacao' => $licitacao,
        ], 200);
    }

    public function store(LicitacaoRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {

            $licitacao = Licitacao::create([
                'id_fase' => $request->input('id_fase'),
                'nu_edital' => $request->input('nu_edital'),
                'id_modalidade' => $request->input('id_modalidade'),
                'data_abertura' => $request->input('data_abertura'),
                'id_licitador' => $request->input('id_licitador'),
                'cnpj_licitador' => $request->input('cnpj_licitador'),
                'id_prioridade' => $request->input('id_prioridade'),
                'objeto' => $request->input('objeto'),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'licitacao' => $licitacao,
                'message' => "Licitação casdastrada!",
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => "Licitação não casdastrada!",
            ], 400);
        }
    }

    public function update(LicitacaoRequest $request, Licitacao $licitacao): JsonResponse
    {
        DB::beginTransaction();
        try {
            $licitacao->update([
                'id_fase' => $request->input('id_fase'),
                'nu_edital' => $request->input('nu_edital'),
                'id_modalidade' => $request->input('id_modalidade'),
                'data_abertura' => $request->input('data_abertura'),
                'id_licitador' => $request->input('id_licitador'),
                'cnpj_licitador' => $request->input('cnpj_licitador'),
                'id_prioridade' => $request->input('id_prioridade'),
                'objeto' => $request->input('objeto'),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'licitacao' => $licitacao,
                'message' => "Licitação Editada!",
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => "Licitação não Editada!",
            ], 400);
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
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Licitação não Deletada!",
            ], 400);
        }
    }
}
