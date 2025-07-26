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
                        v-model="padraoLicitador"
                        :options="licitadores"
                        placeholder="Selecione o Licitador"
                        label="nome"
                        track-by="id"
                    />
                </div>
                <label class="licform-form-label">CNPJ Licitador</label>
                <div class="licform-form-input">
                    <input v-model="cnpj" v-mask="'##.###.###/####-##'" placeholder="00.000.000/0000-00"/>
                </div>
                <label class="licform-form-label">Prioridade</label>
                <div class="licform-form-select">
                    <multiselect
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
import 'vue-multiselect/dist/vue-multiselect.min.css';

const router = useRouter()
const route = useRoute()
const id = Number(route.params.id)

const loading = ref(true)
const edital = ref('')
const padraoModalidade = ref('')
const padraoLicitador = ref('')
const cnpj = ref('')
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
  if (id !== 0) {
    carregarLicitacaoUpdate(id)
  }
  else{
    carregarLicitacaoCreate()
  }
})

async function carregarLicitacaoUpdate(id){
    loading.value = true
    try {
        const modalidade = await axios.get('/api/modalidades')
        modalidades.value = modalidade.data.modalidade

        const licitador = await axios.get('/api/licitador')
        licitadores.value = licitador.data.licitador

        const fase = await axios.get('/api/fase')
        fases.value = fase.data.fase

        const prioridade = await axios.get('/api/prioridade')
        prioridades.value = prioridade.data.prioridade

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
        if (error.response) {
            const resposta = error.response.data

            if (resposta.erros) {
            const mensagens = Object.values(resposta.erros).flat()
            alert('Erro ao atualizar:\n' + mensagens.join('\n'))
            } else {
            alert(resposta.message || 'Erro ao atualizar licitação.')
            }

            console.error('Erro ao atualizar licitação:', resposta)
        } else {
            console.error('Erro inesperado:', error)
            alert('Erro inesperado ao atualizar licitação.')
        }
    } finally {
    loading.value = false
  }
}

async function carregarLicitacaoCreate(){
    loading.value = true
    try {
        const modalidade = await axios.get('/api/modalidades')
        modalidades.value = modalidade.data.modalidade

        const licitador = await axios.get('/api/licitador')
        licitadores.value = licitador.data.licitador

        const fase = await axios.get('/api/fase')
        fases.value = fase.data.fase

        const prioridade = await axios.get('/api/prioridade')
        prioridades.value = prioridade.data.prioridade
    } catch (error) {
        if (error.response) {
            const resposta = error.response.data

            if (resposta.erros) {
            const mensagens = Object.values(resposta.erros).flat()
            alert('Erro ao atualizar:\n' + mensagens.join('\n'))
            } else {
            alert(resposta.message || 'Erro ao atualizar licitação.')
            }

            console.error('Erro ao atualizar licitação:', resposta)
        } else {
            console.error('Erro inesperado:', error)
            alert('Erro inesperado ao atualizar licitação.')
        }
    } finally {
    loading.value = false
  }
}

async function licitacao(id) {
  try {
    if (id === 0) {
      await criarLicitacao()
    } else {
      await atualizarLicitacao(id)
    }
  } catch (error) {
        if (error.response) {
            const resposta = error.response.data

            if (resposta.erros) {
            const mensagens = Object.values(resposta.erros).flat()
            alert('Erro ao atualizar:\n' + mensagens.join('\n'))
            } else {
            alert(resposta.message || 'Erro ao atualizar licitação.')
            }

            console.error('Erro ao atualizar licitação:', resposta)
        } else {
            console.error('Erro inesperado:', error)
            alert('Erro inesperado ao atualizar licitação.')
        }
    } finally {
    loading.value = false
  }
}


async function criarLicitacao() {
  try {
    const payload = {
      nu_edital: edital.value,
      id_modalidade: padraoModalidade.value?.id || null,
      id_licitador: padraoLicitador.value?.id || null,
      cnpj_licitador: cnpj.value,
      id_prioridade: padraoPrioridade.value?.id || null,
      id_fase: padraoFase.value?.id || null,
      data_abertura: formatarDataIso(data.value),
      objeto: objeto.value
    }

    await axios.post('/api/licitacoes', payload)
    alert('Licitação criada com sucesso!')
    router.push('/')
  } catch (error) {
        if (error.response) {
            const resposta = error.response.data

            if (resposta.erros) {
            const mensagens = Object.values(resposta.erros).flat()
            alert('Erro ao atualizar:\n' + mensagens.join('\n'))
            } else {
            alert(resposta.message || 'Erro ao atualizar licitação.')
            }

            console.error('Erro ao atualizar licitação:', resposta)
        } else {
            console.error('Erro inesperado:', error)
            alert('Erro inesperado ao atualizar licitação.')
        }
    } finally {
    loading.value = false
  }
}

async function atualizarLicitacao(id){
    try {
    const payload = {
        nu_edital: edital.value,
        id_modalidade: padraoModalidade.value?.id || null,
        id_licitador: padraoLicitador.value?.id || null,
        cnpj_licitador: cnpj.value,
        id_prioridade: padraoPrioridade.value?.id || null,
        id_fase: padraoFase.value?.id || null,
        data_abertura: formatarDataIso(data.value),
        objeto: objeto.value,
    }
        await axios.put(`/api/licitacoes/${id}`, payload)
        alert('Licitação atualizada com sucesso!')
        router.push('/')
    } catch (error) {
        if (error.response) {
            const resposta = error.response.data

            if (resposta.erros) {
            const mensagens = Object.values(resposta.erros).flat()
            alert('Erro ao atualizar:\n' + mensagens.join('\n'))
            } else {
            alert(resposta.message || 'Erro ao atualizar licitação.')
            }

            console.error('Erro ao atualizar licitação:', resposta)
        } else {
            console.error('Erro inesperado:', error)
            alert('Erro inesperado ao atualizar licitação.')
        }
    } finally {
    loading.value = false
  }
}

function voltar() {
    router.back()
}

function mascaraEdital(e) {
  let v = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '')
  if (v.length > 5) {
    v = v.slice(0, 5) + '/' + v.slice(5, 9)
  }
  edital.value = v
}

function formatarDataIso(dataBR) {
  const [dia, mes, ano] = dataBR.split('/')
  return `${ano}-${mes.padStart(2, '0')}-${dia.padStart(2, '0')}`
}

function validarData() {
  const partes = data.value.split('/')
  if (partes.length !== 3) {
    erroData.value = 'Formato inválido (use dd/mm/aaaa)'
    return
  }
  const [dia, mes, ano] = partes.map(Number)
  const dataObj = new Date(ano, mes - 1, dia)
  const dataValida =
    dataObj.getFullYear() === ano &&
    dataObj.getMonth() === mes - 1 &&
    dataObj.getDate() === dia

  if (!dataValida) {
    erroData.value = 'Data inválida'
  } else {
    erroData.value = ''
  }
}

function formatarData(dataString) {
  if (!dataString) return ''

  if (dataString instanceof Date) {
    return dataString.toLocaleDateString('pt-BR')
  }

  if (dataString.includes('/') && dataString.split('/').length === 3) {
    return dataString
  }

  const data = new Date(dataString)
  return isNaN(data) ? 'Data inválida' : data.toLocaleDateString('pt-BR')
}
</script>
