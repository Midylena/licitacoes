<template>
    <div class="container mx-auto max-w-xl px-5 py-10">
        <div class="flex flex-col gap-2">
            <h2 class="licTitulo">GESTOR DE LICITAÇÕES PÚBLICAS</h2>
            <div v-if="loading">Carregando...</div>
            <div class="licform-form">
                <div class="liccard-form-layout">
                    <div class="liccard-form-coluna">
                        <label class="liccard-form-label-title">Número do Edital</label>
                        <div>
                            <label class="liccard-form-label-response" v-for="lic in licitacoes" :key="lic.id">{{ lic.nu_edital }}</label>
                        </div>
                        <label class="liccard-form-label-title">Modalidade</label>
                        <div>
                            <label class="liccard-form-label-response" v-for="lic in licitacoes" :key="lic.id">{{ lic.modalidade?.nome }}</label>
                        </div>
                        <label class="liccard-form-label-title">Nome Licitador</label>
                        <div>
                            <label class="liccard-form-label-response" v-for="lic in licitacoes" :key="lic.id">{{ lic.licitador?.nome }}</label>
                        </div>
                        <label class="liccard-form-label-title">CNPJ Licitador</label>
                        <div>
                            <label class="liccard-form-label-response" v-for="lic in licitacoes" :key="lic.id">{{ lic.cnpj_licitador }}</label>
                        </div>
                        <label class="liccard-form-label-title">Prioridade</label>
                        <div>
                            <label class="liccard-form-label-response" v-for="lic in licitacoes" :key="lic.id">{{ lic.prioridade?.nome }}</label>
                        </div>
                        <label class="liccard-form-label-title">Objeto</label>
                        <div>
                            <label class="liccard-form-label-response" v-for="lic in licitacoes" :key="lic.id">{{ lic.objeto }}</label>
                        </div>
                    </div>
                    <div class="liccard-form-coluna">
                        <label class="liccard-form-label-title">Fase</label>
                        <div>
                            <label class="liccard-form-label-response" v-for="lic in licitacoes" :key="lic.id">{{ lic.fase?.nome }}</label>
                        </div>
                        <label class="liccard-form-label-title">Data de Abertura</label>
                        <div>
                            <label class="liccard-form-label-response" v-for="lic in licitacoes" :key="lic.id">{{ formatarData(lic.data_abertura) }}</label>
                        </div>
                        <label class="liccard-form-label-title">Data de Criação</label>
                        <div>
                            <label class="liccard-form-label-response" v-for="lic in licitacoes" :key="lic.id">{{ formatarData(lic.created_at) }}</label>
                        </div>
                        <label class="liccard-form-label-title">Data de Atualização</label>
                        <div>
                            <label class="liccard-form-label-response" v-for="lic in licitacoes" :key="lic.id">{{ formatarData(lic.updated_at) }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="licform-buttons" v-for="lic in licitacoes" :key="lic.id">
        <button class="licform-buttons-button" @click="voltar">VOLTAR</button>
        <button class="licform-buttons-button" @click="editarLicitacao(lic.id)">ATUALIZAR LICITAÇÃO</button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter, useRoute } from 'vue-router'
import Swal from 'sweetalert2'

const router = useRouter()
const route = useRoute()
const id = route.params.id

const loading = ref(true)
const licitacoes = ref([])

onMounted(() => {
  carregarLicitacao(id)
})

async function carregarLicitacao(id) {
  loading.value = true
  try {
    const licitacao = await axios.get(`/api/licitacoes/${id}`)
    licitacoes.value = licitacao.data
  } catch (error) {
    mostrarErro(error, 'carregar licitação')
  } finally {
    loading.value = false
  }
}

function voltar() {
  router.push(`/`)
}

function editarLicitacao(id) {
  router.push(`/form/${id}`)
}

function formatarData(dataString) {
  if (!dataString) return ''
  if (dataString instanceof Date) return dataString.toLocaleDateString('pt-BR')
  if (dataString.includes('/') && dataString.split('/').length === 3) return dataString
  const data = new Date(dataString)
  return isNaN(data) ? 'Data inválida' : data.toLocaleDateString('pt-BR')
}

function mostrarErro(error, acao = 'executar ação') {
  if (error.response) {
    const resposta = error.response.data
    if (resposta.erros) {
      const mensagens = Object.values(resposta.erros).flat()
      Swal.fire({
        title: `Erro ao ${acao}`,
        html: mensagens.join('<br>'),
        icon: 'warning',
        confirmButtonText: 'OK'
      })
    } else {
      Swal.fire({
        title: `Erro ao ${acao}`,
        text: resposta.message || `Erro ao ${acao}.`,
        icon: 'error',
        confirmButtonText: 'OK'
      })
    }
    console.error(`Erro ao ${acao}:`, resposta)
  } else {
    console.error(`Erro inesperado ao ${acao}:`, error)
    Swal.fire({
      title: 'Erro inesperado',
      text: `Erro inesperado ao ${acao}.`,
      icon: 'error',
      confirmButtonText: 'OK'
    })
  }
}
</script>
