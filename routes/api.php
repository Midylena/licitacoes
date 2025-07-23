<?php

use App\Http\Controllers\Api\LicitacaoController;
use App\Http\Controllers\Api\ModalidadeController;
use Illuminate\Support\Facades\Route;

Route::get('/licitacoes', [LicitacaoController::class, 'index']); //GET - http://127.0.0.1:8000/api/licitacoes?page=1
Route::get('/licitacoes/{licitacao}', [LicitacaoController::class, 'show']); //GET - http://127.0.0.1:8000/api/licitacoes/1
Route::post('/licitacoes', [LicitacaoController::class, 'store']); //POST - http://127.0.0.1:8000/api/licitacoes
Route::put('/licitacoes/{licitacao}', [LicitacaoController::class, 'update']); //PUT - http://127.0.0.1:8000/api/licitacoes/1
Route::delete('/licitacoes/{licitacao}', [LicitacaoController::class, 'destroy']); //DELETE - http://127.0.0.1:8000/api/licitacoes/1
Route::get('/modalidades', [ModalidadeController::class, 'show']); //GET - http://127.0.0.1:8000/api/modalidades
