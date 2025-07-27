<template>
    <div class="container mx-auto max-w-xl px-5 py-10">
        <div class="flex flex-col gap-2">
            <h2 class="licTitulo">GESTOR DE LICITAÇÕES PÚBLICAS</h2>
            <div v-if="loading">Carregando...</div>
            <div class="licform-form">
                <label class="licform-form-label">Número do Edital</label>
                <div class="licform-form-input">
                    <input v-model="edital" @input="mascaraEdital" placeholder="A0YAA/2025" type="text"/>
                </div>
                <label class="licform-form-label">Modalidade</label>
                <div class="licform-form-select">
                    <multiselect
                        :select-label="''"
                        :selected-label="''"
                        :deselect-label="''"
                        v-model="padraoModalidade"
                        :options="modalidades"
                        placeholder="Selecione a Modalidade"
                        label="nome"
                        track-by="id"
                    />
                </div>
                <label class="licform-form-label">Nome Licitador</label>
                <div class="licform-form-select">
                    <multiselect
                        :select-label="''"
                        :selected-label="''"
                        :deselect-label="''"
                        v-model="padraoLicitador"
                        :options="licitadores"
                        placeholder="Selecione o Licitador"
                        label="nome"
                        track-by="id"
                    />
                </div>
                <label class="licform-form-label">CNPJ Licitador</label>
                <div class="licform-form-input">
                    <input v-model="cnpj" v-mask="'##.###.###/####-##'" placeholder="00.000.000/0000-00" @blur="validarCNPJ"/>
                    <span v-if="erroCNPJ" class="licitacao-erro">{{ erroCNPJ }}</span>
                </div>
                <label class="licform-form-label">Prioridade</label>
                <div class="licform-form-select">
                    <multiselect
                        :select-label="''"
                        :selected-label="''"
                        :deselect-label="''"
                        v-model="padraoPrioridade"
                        :options="prioridades"
                        placeholder="Selecione a Prioridade"
                        label="nome"
                        track-by="id"
                    />
                </div>
                <label class="licform-form-label">Fase</label>
                <div class="licform-form-select">
                    <multiselect
                        :select-label="''"
                        :selected-label="''"
                        :deselect-label="''"
                        v-model="padraoFase"
                        :options="fases"
                        placeholder="Selecione a Fase"
                        label="nome"
                        track-by="id"
                    />
                </div>
                <label class="licform-form-label">Data de Abertura</label>
                <div class="licform-form-input">
                    <input v-model="data" v-mask="'##/##/####'" placeholder="dd/mm/aaaa" @blur="validarData"/>
                    <span v-if="erroData" class="licform-form-erro">{{ erroData }}</span>
                </div>
                <label class="licform-form-label">Objeto</label>
                <div class="licform-form-textarea">
                    <textarea v-model="objeto" placeholder="Descreva a Licitação" type="text"/>
                </div>
            </div>
        </div>
    </div>
    <div class="licform-buttons">
        <button class="licform-buttons-button" @click="voltar">VOLTAR</button>
        <button class="licform-buttons-button" @click="licitacao(id)">{{ id === 0 ? 'CRIAR LICITAÇÃO' : 'ATUALIZAR LICITAÇÃO' }}</button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter, useRoute } from 'vue-router'
import Multiselect from 'vue-multiselect'
import Swal from 'sweetalert2'
import 'vue-multiselect/dist/vue-multiselect.min.css';

const router = useRouter()
const route = useRoute()
const id = Number(route.params.id)

const loading = ref(true)
const edital = ref('')
const padraoModalidade = ref('')
const padraoLicitador = ref('')
const cnpj = ref('')
const erroCNPJ = ref('');
const padraoPrioridade = ref('')
const padraoFase = ref('')
const data = ref('')
const erroData = ref('')
const objeto = ref('')

const modalidades = ref([])
const licitadores = ref([])
const fases = ref([])
const prioridades = ref([])
const licitacoes = ref([])

onMounted(() => {
  if (id !== 0) carregarLicitacaoUpdate(id)
  else carregarLicitacaoCreate()
})

async function carregarLicitacaoUpdate(id){
  loading.value = true
  try {
    await carregarDadosBase()

    const licitacao = await axios.get(`/api/licitacoes/${id}`)
    licitacoes.value = licitacao.data
    const lic = licitacao.data.licitacao

    edital.value = lic.nu_edital
    padraoModalidade.value = modalidades.value.find(m => m.id === lic.id_modalidade)
    padraoLicitador.value = licitadores.value.find(l => l.id === lic.id_licitador)
    cnpj.value = lic.cnpj_licitador
    padraoPrioridade.value = prioridades.value.find(p => p.id === lic.id_prioridade)
    padraoFase.value = fases.value.find(f => f.id === lic.id_fase)
    data.value = formatarData(lic.data_abertura)
    objeto.value = lic.objeto
  } catch (error) {
    mostrarErro(error, 'carregar licitação')
  } finally {
    loading.value = false
  }
}

async function carregarLicitacaoCreate(){
  loading.value = true
  try {
    await carregarDadosBase()
  } catch (error) {
    mostrarErro(error, 'carregar dados iniciais')
  } finally {
    loading.value = false
  }
}

async function carregarDadosBase() {
  const [modalidade, licitador, fase, prioridade] = await Promise.all([
    axios.get('/api/modalidades'),
    axios.get('/api/licitador'),
    axios.get('/api/fase'),
    axios.get('/api/prioridade')
  ])
  modalidades.value = modalidade.data.modalidade
  licitadores.value = licitador.data.licitador
  fases.value = fase.data.fase
  prioridades.value = prioridade.data.prioridade
}

async function licitacao(id) {
  try {
    loading.value = true
    if (id === 0) await criarLicitacao()
    else await atualizarLicitacao(id)
  } catch (error) {
    mostrarErro(error, id === 0 ? 'criar licitação' : 'atualizar licitação')
  } finally {
    loading.value = false
  }
}



async function criarLicitacao() {
  const payload = montarPayload()
  await axios.post('/api/licitacoes', payload)
  Swal.fire({
    title: 'Sucesso',
    text: 'Licitação criada com sucesso!',
    icon: 'success',
    confirmButtonText: 'OK'
  })
  router.push('/')
}

async function atualizarLicitacao(id) {
  const payload = montarPayload()
  await axios.put(`/api/licitacoes/${id}`, payload)
  Swal.fire({
    title: 'Sucesso',
    text: 'Licitação atualizada com sucesso!',
    icon: 'success',
    confirmButtonText: 'OK'
  })
  router.push('/')
}

function montarPayload() {
  return {
    nu_edital: edital.value,
    id_modalidade: padraoModalidade.value?.id || null,
    id_licitador: padraoLicitador.value?.id || null,
    cnpj_licitador: cnpj.value,
    id_prioridade: padraoPrioridade.value?.id || null,
    id_fase: padraoFase.value?.id || null,
    data_abertura: formatarDataIso(data.value),
    objeto: objeto.value
  }
}

function mostrarErro(error, acao = 'executar ação') {
  if (error.response) {
    const resposta = error.response.data
    if (resposta.erros) {
      const mensagens = Object.values(resposta.erros).flat()
      Swal.fire({ title: `Erro ao ${acao}`, html: mensagens.join('<br>'), icon: 'warning', confirmButtonText: 'OK' })
    } else {
      Swal.fire({ title: `Erro ao ${acao}`, text: resposta.message || `Erro ao ${acao}.`, icon: 'error', confirmButtonText: 'OK' })
    }
    console.error(`Erro ao ${acao}:`, resposta)
  } else {
    console.error(`Erro inesperado ao ${acao}:`, error)
    Swal.fire({ title: 'Erro inesperado', text: `Erro inesperado ao ${acao}.`, icon: 'error', confirmButtonText: 'OK' })
  }
}

function voltar() {
  router.back()
}

function mascaraEdital(e) {
  let v = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '')
  if (v.length > 5) v = v.slice(0, 5) + '/' + v.slice(5, 9)
  edital.value = v
}

function formatarDataIso(dataBR) {
    if (data.value === '') {
        return ''
    }
    else{
        const [dia, mes, ano] = dataBR.split('/')
        return `${ano}-${mes.padStart(2, '0')}-${dia.padStart(2, '0')}`
    }
}

function validarData() {
  const partes = data.value.split('/')
  if (data.value === '') {
    return true
  } else {
        if (partes.length !== 3) {
            Swal.fire('Erro', 'Formato inválido (use dd/mm/aaaa)', 'error')
        }
        const [dia, mes, ano] = partes.map(Number)
        const dataObj = new Date(ano, mes - 1, dia)
        const dataValida =
            dataObj.getFullYear() === ano &&
            dataObj.getMonth() === mes - 1 &&
            dataObj.getDate() === dia

        if (!dataValida) {
            Swal.fire('Erro', 'Data inválida', 'error')
            return false
        } else {
            return true
        }
    }
}

function formatarData(dataString) {
  if (!dataString) return ''
  if (dataString instanceof Date) return dataString.toLocaleDateString('pt-BR')
  if (dataString.includes('/') && dataString.split('/').length === 3) return dataString
  const data = new Date(dataString)
  return isNaN(data) ? 'Data inválida' : data.toLocaleDateString('pt-BR')
}

function validarCNPJ() {
  const cnpjLimpo = cnpj.value.replace(/[^\d]+/g, '');

  if (cnpjLimpo.length !== 14) {
    return true;
  }

  if (/^(\d)\1+$/.test(cnpjLimpo)) {
    Swal.fire('Erro', 'CNPJ com todos os dígitos iguais é inválido.', 'error');
    return false;
  }

  let tamanho = 12;
  let numeros = cnpjLimpo.substring(0, tamanho);
  let digitos = cnpjLimpo.substring(tamanho);
  let soma = 0;
  let pos = tamanho - 7;

  for (let i = tamanho; i >= 1; i--) {
    soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
    if (pos < 2) pos = 9;
  }

  let resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
  if (resultado != parseInt(digitos.charAt(0))) {
    Swal.fire('Erro', 'Dígito verificador 1 do CNPJ está incorreto.', 'error');
    return false;
  }

  tamanho = 13;
  numeros = cnpjLimpo.substring(0, tamanho);
  soma = 0;
  pos = tamanho - 7;

  for (let i = tamanho; i >= 1; i--) {
    soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
    if (pos < 2) pos = 9;
  }

  resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
  if (resultado != parseInt(digitos.charAt(1))) {
    Swal.fire('Erro', 'Dígito verificador 2 do CNPJ está incorreto.', 'error');
    return false;
  }

  return true;
}
</script>
