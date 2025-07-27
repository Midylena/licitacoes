<template>
    <div class="container mx-auto px-6 py-6 w-full max-w-none">
        <div class="flex flex-col gap-2">
            <h2 class="licitacao-titulo">GESTOR DE LICITAÇÕES PÚBLICAS</h2>
            <div v-if="loadingLicitacao">Carregando...</div>
            <div v-else>
                <div v-if="!controleTable" class="licitacao-form">
                    <div class="licitacao-layout">
                        <div class="licitacao-coluna">
                            <label class="licitacao-label">Número do Edital</label>
                            <div class="licitacao-input">
                                <input v-model="edital" @input="mascaraEdital" placeholder="A0YAA/2025" type="text"/>
                            </div>
                            <label class="licitacao-label">Nome Licitador</label>
                            <div class="licitacao-multselect">
                                <multiselect
                                    :select-label="''"
                                    :selected-label="''"
                                    :deselect-label="''"
                                    v-model="licitador"
                                    :options="licitadores"
                                    placeholder="Selecione o Licitador"
                                    label="nome"
                                    track-by="id"
                                />
                            </div>
                            <label class="licitacao-label">CNPJ Licitador</label>
                            <div class="licitacao-input">
                                <input v-model="cnpj" v-mask="'##.###.###/####-##'" placeholder="00.000.000/0000-00" @blur="validarCNPJ"/>
                            </div>
                        </div>
                        <div class="licitacao-coluna">
                            <label class="licitacao-label">Modalidade</label>
                            <div class="licitacao-multselect">
                                <multiselect
                                    :select-label="''"
                                    :selected-label="''"
                                    :deselect-label="''"
                                    v-model="modalidade"
                                    :options="modalidades"
                                    placeholder="Selecione a Modalidade"
                                    label="nome"
                                    track-by="id"/>
                            </div>
                            <label class="licitacao-label">Prioridade</label>
                            <div class="licitacao-multselect">
                                <multiselect
                                    :select-label="''"
                                    :selected-label="''"
                                    :deselect-label="''"
                                    v-model="prioridade"
                                    :options="prioridades"
                                    placeholder="Selecione a Prioridade"
                                    label="nome"
                                    track-by="id"/>
                            </div>
                            <label class="licitacao-label">Fase</label>
                            <div class="licitacao-multselect">
                                <multiselect
                                    :select-label="''"
                                    :selected-label="''"
                                    :deselect-label="''"
                                    v-model="fase"
                                    :options="fases"
                                    placeholder="Selecione a Fase"
                                    label="nome"
                                    track-by="id"/>
                            </div>
                        </div>
                        <div class="licitacao-coluna">
                            <label class="licitacao-label">Data de Abertura</label>
                                <div class="licitacao-input">
                                    <input v-model="data" v-mask="'##/##/####'" placeholder="dd/mm/aaaa" @blur="validarData"/>
                                </div>
                            <label class="licitacao-label">Objeto</label>
                            <div class="licitacao-textarea">
                                <textarea v-model="objeto" placeholder="Descreva a Licitação" type="text" />
                            </div>
                        </div>
                    </div>
                    <div class="licitacao-meio">
                        <div class="licitacao-meio-input">
                            <button class="licitacao-button"
                            @click="modificarLicitacao(), controleButton = true">{{ id === 0 ? 'CRIAR LICITAÇÃO' : 'NOVA LICITAÇÃO'}}
                            <img src="/public/img/novaLicitacao.svg" alt="Adicionar"/>
                            </button>
                            <button class="licitacao-button" :disabled="controleButton" :class="{ 'botao-desativado': controleButton }" @click="atualizarLicitacao(id)">
                            ATUALIZAR LICITAÇÃO
                            </button>
                            <button class="licitacao-button" :style="{ color: '#e13030' }" :disabled="controleButton" :class="{ 'botao-desativado': controleButton }"
                            @click="deletarLicitacao(id)">DELETAR</button>
                        </div>
                        <div class="licitacao-meio-form">
                            <span class="licitacao-meio-label">Criação:</span>
                            <span class="licitacao-meio-sub-label">{{ created_at }}</span>
                            <span class="licitacao-meio-label">Atualização:</span>
                            <span class="licitacao-meio-sub-label">{{ updated_at }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div v-if="loading">Carregando...</div>
            <div v-else>
                <div class="licitacao-wrapper" :class="{ 'licitacao-wrapper-full': controleTable }">
                    <table class="licitacao-table">
                        <thead>
                            <tr>
                                <th @click="ordenarPor('id')">ID LICITAÇÃO<span>{{ direcaoOrdenacao('id') }}</span></th>
                                <th @click="ordenarPor('data_abertura')">DATA ABERTURA<span>{{ direcaoOrdenacao('data_abertura') }}</span></th>
                                <th @click="ordenarPor('nu_edital')">N° EDITAL<span>{{ direcaoOrdenacao('nu_edital') }}</span></th>
                                <th @click="ordenarPor('licitador.nome')">NOME LICITADOR<span>{{ direcaoOrdenacao('licitador.nome') }}</span></th>
                                <th @click="ordenarPor('cnpj_licitador')">CNPJ LICITADOR<span>{{ direcaoOrdenacao('cnpj_licitador') }}</span></th>
                                <th @click="ordenarPor('modalidade.nome')">MODALIDADE<span>{{ direcaoOrdenacao('modalidade.nome') }}</span></th>
                                <th @click="ordenarPor('prioridade.nome')">PRIORIDADE<span>{{ direcaoOrdenacao('prioridade.nome') }}</span></th>
                                <th @click="ordenarPor('fase.nome')">FASE<span>{{ direcaoOrdenacao('fase.nome') }}</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lic in licitacoes" :key="lic.id" @click="carregarLicitacao(lic.id); controleButton = false;
                             linhaSelecionada = (linhaSelecionada === lic.id ? null : lic.id)"
                             :class="{ 'linha-selecionada': linhaSelecionada === lic.id }">
                                <td :data-label="'ID LICITAÇÃO'">{{ lic.id }}</td>
                                <td :data-label="'DATA ABERTURA'">{{ formatarData(lic.data_abertura) }}</td>
                                <td :data-label="'N° EDITAL'">{{ lic.nu_edital }}</td>
                                <td :data-label="'NOME LICITADOR'">{{ lic.licitador?.nome }}</td>
                                <td :data-label="'CNPJ LICITADOR'">{{ lic.cnpj_licitador }}</td>
                                <td :data-label="'MODALIDADE'">{{ lic.modalidade?.nome }}</td>
                                <td :data-label="'PRIORIDADE'">{{ lic.prioridade?.nome }}</td>
                                <td :data-label="'FASE'">{{ lic.fase?.nome }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="licitacao-botton-form">
            <div class="licitacao-input-filtro">
                    <input v-model="filtroGlobal" type="text" placeholder="Filtro" />
                <label>
                    Início:
                    <input type="date" v-model="dataInicio" :style="{ color: dataInicio ? '#000' : 'gray' }">
                </label>
                <label>
                    Fim:
                    <input type="date" v-model="dataFim" :style="{ color: dataFim ? '#000' : 'gray' }">
                </label>
                <button class="licitacao-button" @click="aplicarTodosOsFiltros">
                    FILTRAR
                </button>
            </div>

            <div class="licitacao-pagination">
                <button @click="anteriorPagina" :disabled="resposta.current_page === 1">‹</button>
                <label class="licitacao-label-paginate"> {{resposta.current_page}} </label>
                <label> de </label>
                <label> {{resposta.last_page}} </label>
                <button @click="proximaPagina" :disabled="resposta.current_page === resposta.last_page">›</button>
                <label>  Total de registros encontrados: </label>
                <label class="licitacao-label-paginate"> {{resposta.total}} </label>
                <select v-if=controleTable v-model="itensPorPagina" @change="carregarLicitacoes(1)">
                    <option v-for="n in [5, 10, 25, 50, 100]" :key="n" :value="n">{{ n }}</option>
                </select>
            </div>
            <div>
                <button class="licitacao-botton-button" @click="controleTable = !controleTable">
                    {{ controleTable === true ? 'VOLTAR' : 'VISUALIZAR TABELA'}}
                </button>
            </div>
        </div>
    </div>
</template>



<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css';
import Swal from 'sweetalert2'

const controleTable = ref(false)
const controleButton = ref(false)
const loading = ref(true)
const loadingLicitacao = ref(true)
const ordemCrescente = ref(true)

watch(controleTable, (ativo) => {
  itensPorPagina.value = ativo ? 50 : 12
  carregarLicitacoes(1)
})

const itensPorPagina = ref(12)
const dataInicio = ref('')
const dataFim = ref('')
const campoOrdenacao = ref('')
const filtroGlobal = ref('')
const linhaSelecionada = ref(null)

const id = ref(0)
const edital = ref('')
const modalidade = ref('')
const licitador = ref('')
const cnpj = ref('')
const prioridade = ref('')
const fase = ref('')
const data = ref('')
const objeto = ref('')
const created_at = ref('')
const updated_at = ref('')

const modalidades = ref([])
const licitadores = ref([])
const fases = ref([])
const prioridades = ref([])
const licitacoes = ref([])

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
  carregarNovaLicitacao()
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

        const modalidade = await axios.get('/api/modalidades')
        modalidades.value = modalidade.data.modalidade

        const licitador = await axios.get('/api/licitador')
        licitadores.value = licitador.data.licitador

        const fase = await axios.get('/api/fase')
        fases.value = fase.data.fase

        const prioridade = await axios.get('/api/prioridade')
        prioridades.value = prioridade.data.prioridade

        const response = await axios.get(`/api/licitacoes?${params.toString()}`)

        resposta.value = response.data.licitacoes
        licitacoes.value = resposta.value.data

    } catch (error) {
        mostrarErro(error, 'atualizar licitação')
    } finally {
        loading.value = false
    }
}

async function carregarNovaLicitacao() {
    loadingLicitacao.value = true
    try {
        id.value = 0
        edital.value = ''
        modalidade.value = ''
        licitador.value = ''
        cnpj.value = ''
        prioridade.value = ''
        fase.value = ''
        data.value = ''
        objeto.value = ''
        updated_at.value = ''
        created_at.value = ''
    } catch (error) {
        mostrarErro(error, 'atualizar licitação')
    } finally {
        loadingLicitacao.value = false
    }
}

async function carregarLicitacao(id_licitacao) {
    loadingLicitacao.value = true
    try {
        const resultado = licitacoes.value.find(lic => lic.id === id_licitacao)

        id.value = resultado.id
        edital.value = resultado.nu_edital
        modalidade.value = modalidades.value.find(m => m.id === resultado.id_modalidade)
        licitador.value = licitadores.value.find(l => l.id === resultado.id_licitador)
        cnpj.value = resultado.cnpj_licitador
        prioridade.value = prioridades.value.find(p => p.id === resultado.id_prioridade)
        fase.value = fases.value.find(f => f.id === resultado.id_fase)
        data.value = formatarData(resultado.data_abertura)
        objeto.value = resultado.objeto
        updated_at.value = formatarData(resultado.updated_at)
        created_at.value = formatarData(resultado.created_at)
    } catch (error) {
        mostrarErro(error, 'atualizar licitação')
    } finally {
        loadingLicitacao.value = false
    }
}

async function modificarLicitacao() {
    if (!validarCNPJ()) return;
    if (!validarData()) return;

    try {
        if (id.value === 0) {
            await criarLicitacao()
        } else {
            await carregarNovaLicitacao()
        }
        } catch (error) {
        mostrarErro(error, 'atualizar licitação')
        }

}

async function criarLicitacao() {
    try {
        const payload = {
            nu_edital: edital.value,
            id_modalidade: modalidade.value?.id || null,
            id_licitador: licitador.value?.id || null,
            cnpj_licitador: cnpj.value,
            id_prioridade: prioridade.value?.id || null,
            id_fase: fase.value?.id || null,
            data_abertura: formatarDataIso(data.value),
            objeto: objeto.value
        }

        await axios.post('/api/licitacoes', payload)
        Swal.fire('Sucesso!', 'Licitação criada com sucesso!', 'success')
        carregarLicitacoes()
    } catch (error) {
        mostrarErro(error, 'atualizar licitação')
    }
}

async function atualizarLicitacao(id_licitacao) {
    try {
        const payload = {
            nu_edital: edital.value,
            id_modalidade: modalidade.value?.id || null,
            id_licitador: licitador.value?.id || null,
            cnpj_licitador: cnpj.value,
            id_prioridade: prioridade.value?.id || null,
            id_fase: fase.value?.id || null,
            data_abertura: formatarDataIso(data.value),
            objeto: objeto.value
        }
        await axios.put(`/api/licitacoes/${id_licitacao}`, payload)
        Swal.fire('Sucesso!', 'Licitação atualizada com sucesso!', 'success')
        carregarLicitacoes()
    } catch (error) {
        mostrarErro(error, 'atualizar licitação')
    }
}

async function deletarLicitacao(id_licitacao) {
    const confirm = await Swal.fire({
        title: 'Tem certeza?',
        text: 'Deseja realmente excluir esta licitação?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, excluir!'
    })

    if (!confirm.isConfirmed) return

    try {
        const response = await axios.delete(`/api/licitacoes/${id_licitacao}`)

        if (response.data.status) {
            Swal.fire('Excluído!', 'Licitação deletada com sucesso!', 'success')
            carregarNovaLicitacao()
            carregarLicitacoes()
        } else {
            Swal.fire('Erro', 'Não foi possível deletar a licitação.', 'error')
        }
    } catch (error) {
        mostrarErro(error, 'deletar licitação')
    }
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

function mascaraEdital(e) {
  let v = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '')
  if (v.length > 5) {
    v = v.slice(0, 5) + '/' + v.slice(5, 9)
  }
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

  if (dataString instanceof Date) {
    return dataString.toLocaleDateString('pt-BR')
  }

  if (dataString.includes('/') && dataString.split('/').length === 3) {
    return dataString
  }

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
