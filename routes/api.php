<?php

use App\Http\Controllers\Api\FaseController;
use App\Http\Controllers\Api\LicitacaoController;
use App\Http\Controllers\Api\LicitadorController;
use App\Http\Controllers\Api\ModalidadeController;
use App\Http\Controllers\Api\PrioridadeController;
use Illuminate\Support\Facades\Route;

Route::get('/licitacoes/all', [LicitacaoController::class, 'listarTodas']); //GET - http://127.0.0.1:8000/api/licitacoes/todas
Route::get('/licitacoes', [LicitacaoController::class, 'index']); //GET - http://127.0.0.1:8000/api/licitacoes?page=1
Route::get('/licitacoes/{licitacao}', [LicitacaoController::class, 'show']); //GET - http://127.0.0.1:8000/api/licitacoes/1
Route::post('/licitacoes', [LicitacaoController::class, 'store']); //POST - http://127.0.0.1:8000/api/licitacoes
Route::put('/licitacoes/{licitacao}', [LicitacaoController::class, 'update']); //PUT - http://127.0.0.1:8000/api/licitacoes/1
Route::delete('/licitacoes/{licitacao}', [LicitacaoController::class, 'destroy']); //DELETE - http://127.0.0.1:8000/api/licitacoes/1
Route::get('/modalidades', [ModalidadeController::class, 'show']); //GET - http://127.0.0.1:8000/api/modalidades
Route::get('/licitador', [LicitadorController::class, 'show']); //GET - http://127.0.0.1:8000/api/licitador
Route::get('/fase', [FaseController::class, 'show']); //GET - http://127.0.0.1:8000/api/fase
Route::get('/prioridade', [PrioridadeController::class, 'show']); //GET - http://127.0.0.1:8000/api/prioridade
