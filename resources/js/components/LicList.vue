<template>
    <div class="container mx-auto px-4 py-6 w-full max-w-none">
        <div class="flex flex-col gap-2">
            <h2 class="licTitulo">GESTOR DE LICITAÇÕES PÚBLICAS</h2>

            <div class="liclist-filtros">
                <div class="liclist-filtros-input">
                    <input v-model="filtroGlobal" type="text" placeholder="Filtro" />
                    <button v-if="!mostrarFiltroPeriodo" @click="aplicarTodosOsFiltros" class="liclist-filtros-button">
                        FILTRAR
                    </button>
                    <button class="liclist-filtros-button-img" @click="mostrarFiltroPeriodo = !mostrarFiltroPeriodo">
                        <img src="/public/img/filtro.svg" alt="Filtrar"/>
                    </button>
                    <div v-if="mostrarFiltroPeriodo" class="liclist-data-input">
                        <label>
                            Início:
                            <input type="date" v-model="dataInicio" :style="{ color: dataInicio ? '#000' : '#b0b1b2' }">
                        </label>
                        <label>
                            Fim:
                            <input type="date" v-model="dataFim" :style="{ color: dataFim ? '#000' : '#b0b1b2' }">
                        </label>
                        <button class="liclist-filtros-button" @click="aplicarTodosOsFiltros">
                            FILTRAR
                        </button>
                    </div>

                </div>
                <button class="liclist-button" @click="editarLicitacao(0)">
                    NOVA LICITAÇÃO
                    <img src="/public/img/novaLicitacao.svg" alt="Adicionar"/>
                </button>
            </div>

            <div v-if="loading">Carregando...</div>
            <div v-else>
                <table class="liclist-table w-full table-auto">
                    <thead>
                        <tr>
                            <th @click="ordenarPor('id')">ID LICITAÇÃO <span>{{ direcaoOrdenacao('id') }}</span></th>
                            <th @click="ordenarPor('data_abertura')">DATA ABERTURA <span>{{ direcaoOrdenacao('data_abertura') }}</span></th>
                            <th @click="ordenarPor('nu_edital')">N° EDITAL <span>{{ direcaoOrdenacao('nu_edital') }}</span></th>
                            <th @click="ordenarPor('licitador.nome')">NOME LICITADOR <span>{{ direcaoOrdenacao('licitador.nome') }}</span></th>
                            <th @click="ordenarPor('cnpj_licitador')">CNPJ LICITADOR <span>{{ direcaoOrdenacao('cnpj_licitador') }}</span></th>
                            <th @click="ordenarPor('modalidade.nome')">MODALIDADE <span>{{ direcaoOrdenacao('modalidade.nome') }}</span></th>
                            <th @click="ordenarPor('prioridade.nome')">PRIORIDADE <span>{{ direcaoOrdenacao('prioridade.nome') }}</span></th>
                            <th @click="ordenarPor('fase.nome')">FASE <span>{{ direcaoOrdenacao('fase.nome') }}</span></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lic in licitacoes" :key="lic.id">
                            <td>{{ lic.id }}</td>
                            <td>{{ formatarData(lic.data_abertura) }}</td>
                            <td>{{ lic.nu_edital }}</td>
                            <td>{{ lic.licitador?.nome }}</td>
                            <td>{{ lic.cnpj_licitador }}</td>
                            <td>{{ lic.modalidade?.nome }}</td>
                            <td>{{ lic.prioridade?.nome }}</td>
                            <td>{{ lic.fase?.nome }}</td>
                            <td>
                                <button @click="verDetalhes(lic.id)" class="liclist-table-button">Visualizar</button>
                                <button @click="editarLicitacao(lic.id)" class="liclist-table-button">Editar</button>
                                <button @click="deletarLicitacao(lic.id)" class="liclist-table-button">Deletar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="liclist-pagination">
        <button @click="anteriorPagina" :disabled="resposta.current_page === 1">‹</button>
        <button v-for="page in paginasVisiveis" :key="page" :class="{ ativo: page === resposta.current_page }"
            @click="page !== '...' && carregarLicitacoes(page)" :disabled="page === '...'">
            {{ page }}
        </button>
        <button @click="proximaPagina" :disabled="resposta.current_page === resposta.last_page">›</button>
        <select v-model="itensPorPagina" @change="carregarLicitacoes(1)">
            <option v-for="n in [5, 10, 25, 50, 100]" :key="n" :value="n">{{ n }}</option>
        </select>
</div>
</template>


<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'


const router = useRouter()

const loading = ref(true)
const licitacoes = ref([])
const mostrarFiltroPeriodo = ref(false)
const dataInicio = ref('')
const dataFim = ref('')
const campoOrdenacao = ref('')
const ordemCrescente = ref(true)
const itensPorPagina = ref(10)
const filtroGlobal = ref('')

const resposta = ref({
  data: [],
  current_page: 1,
  last_page: 1,
})

const paginasVisiveis = computed(() => {
  const total = resposta.value.last_page
  const atual = resposta.value.current_page
  const paginas = []

  paginas.push(1)

  if (atual > 3) {
    paginas.push('...')
  }

  const inicio = Math.max(2, atual - 1)
  const fim = Math.min(total - 1, atual + 1)

  for (let i = inicio; i <= fim; i++) {
    if (i !== 1 && i !== total) {
      paginas.push(i)
    }
  }

  if (atual < total - 2) {
    paginas.push('...')
  }

  if (total > 1) {
    paginas.push(total)
  }

  return paginas
})

onMounted(() => {
  carregarLicitacoes()
})

async function carregarLicitacoes(pagina = 1) {
  loading.value = true
  try {
    const params = new URLSearchParams()
    params.append('page', pagina)
    params.append('perPage', itensPorPagina.value)

    params.append('page', pagina)

    if (campoOrdenacao.value) {
      params.append('ordenarPor', campoOrdenacao.value)
      params.append('ordem', ordemCrescente.value ? 'asc' : 'desc')
    }

    if (filtroGlobal.value) {
      params.append('filtro', filtroGlobal.value)
    }

    if (dataInicio.value) {
      params.append('dataInicio', dataInicio.value)
    }

    if (dataFim.value) {
      params.append('dataFim', dataFim.value)
    }

    const response = await axios.get(`/api/licitacoes?${params.toString()}`)
    resposta.value = response.data.licitacoes
    licitacoes.value = resposta.value.data
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

async function deletarLicitacao(id) {
  if (!confirm('Tem certeza que deseja excluir esta licitação?')) return
  try {
    const response = await axios.delete(`/api/licitacoes/${id}`)

    if (response.data.status) {
      alert('Licitação deletada com sucesso!')
      carregarLicitacoes()
    } else {
      alert('Não foi possível deletar a licitação.')
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

function verDetalhes(id) {
  router.push(`/card/${id}`)
}

function editarLicitacao(id) {
  router.push(`/form/${id}`)
}

function aplicarTodosOsFiltros() {
  carregarLicitacoes(1)
}

function ordenarPor(campo) {
  if (campoOrdenacao.value === campo) {
    ordemCrescente.value = !ordemCrescente.value
  } else {
    campoOrdenacao.value = campo
    ordemCrescente.value = true
  }
  carregarLicitacoes(1)
}

function direcaoOrdenacao(campo) {
  if (campoOrdenacao.value !== campo) return ''
  return ordemCrescente.value ? '▲' : '▼'
}

function anteriorPagina() {
  if (resposta.value.current_page > 1) {
    carregarLicitacoes(resposta.value.current_page - 1)
  }
}

function proximaPagina() {
  if (resposta.value.current_page < resposta.value.last_page) {
    carregarLicitacoes(resposta.value.current_page + 1)
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
