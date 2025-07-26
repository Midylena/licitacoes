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
        ], 422));
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
            'id_fase' => 'required',
            'nu_edital' => [
                'required',
                Rule::unique('licitacao')
                    ->ignore($licitacaoId?->id, 'id')
                    ->where(
                        fn($query) => $query
                            ->where('cnpj_licitador', $this->input('cnpj_licitador'))
                            ->where('id_modalidade', $this->input('id_modalidade'))
                    ),
            ],
            'id_modalidade' => 'required',
            'data_abertura' => 'required|date',
            'id_licitador' => 'nullable',
            'cnpj_licitador' => 'nullable|string|size:18',
            'id_prioridade' => 'required',
            'objeto' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id_fase.required' => 'Campo Fase é obrigatório!',
            'nu_edital.required' => 'Campo Edital é obrigatório!',
            'nu_edital.unique' => 'Essa Licitação já foi Cadastrada!',
            'id_modalidade.required' => 'Campo Modalidade é obrigatório!',
            'data_abertura.required' => 'Campo Data de Abertura é obrigatório!',
            'data_abertura.date' => 'A Data de Abertura precisa ser Valida!',
            'cnpj_licitador.size' => 'Campo CNPJ precisa ter :size caracteres!',
            'id_prioridade.required' => 'Campo Prioridade é obrigatório!',
            'objeto.required' => 'Campo Objeto é obrigatório!',
        ];
    }
}
