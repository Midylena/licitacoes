import { createRouter, createWebHistory } from 'vue-router'
import LicCard from './components/LicCard.vue'
import LicForm from './components/LicForm.vue'
import LicList from './components/LicList.vue'
import Licitacoes from './components/Licitacoes.vue'

const routes = [
  {
    path: '/card/:id',
    name: 'card',
    component: LicCard
  },
  {
    path: '/form/:id',
    name: 'form',
    component: LicForm
  },
  {
    path: '/',
    name: 'list',
    component: LicList
  },
  {
    path: '/licitacoes',
    name: 'licitacoes',
    component: Licitacoes
  }

]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
