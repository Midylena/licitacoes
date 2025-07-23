<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class LicitacaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'erros' => $validator->errors(),
        ], ));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $licitacaoId = $this->route('licitacao');
        return [
            'nu_fase' => 'required',
            'nu_edital' => [
                'required',
                Rule::unique('licitacao')
                    ->ignore($licitacaoId?->id_lic, 'id_lic')
                    ->where(
                        fn($query) => $query
                            ->where('cnpj_licitador', $this->input('cnpj_licitador'))
                            ->where('id_mod', $this->input('id_mod'))
                    ),
            ],
            'id_mod' => 'required',
            'data_abertura' => 'required|date',
            'nome_licitador' => 'nullable|string',
            'cnpj_licitador' => 'nullable|string|size:18',
            'prioridade' => 'required|integer',
            'objeto' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nu_fase.required' => 'Campo Fase é obrigatório!',
            'nu_edital.required' => 'Campo Edital é obrigatório!',
            'nu_edital.unique' => 'Essa Licitação já foi Cadastrada!',
            'id_mod.required' => 'Campo Modalidade é obrigatório!',
            'data_abertura.required' => 'Campo Data de Abertura é obrigatório!',
            'data_abertura.date' => 'A Data de Abertura precisa ser Valida!',
            'cnpj_licitador.size' => 'Campo CNPJ precisa ter :size caracteres!',
            'prioridade.required' => 'Campo Prioridade é obrigatório!',
            'objeto.required' => 'Campo Objeto é obrigatório!',
        ];
    }
}
