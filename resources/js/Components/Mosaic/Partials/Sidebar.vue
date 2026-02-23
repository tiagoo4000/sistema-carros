<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import SidebarLinkGroup from './SidebarLinkGroup.vue'
import { 
  LayoutDashboard, 
  Users, 
  Gavel, 
  FileText,
  Settings,
  Layers,
  HelpCircle,
  File,
  LayoutTemplate
} from 'lucide-vue-next'

const props = defineProps(['sidebarOpen', 'variant'])
const emit = defineEmits(['close-sidebar'])

const trigger = ref(null)
const sidebar = ref(null)

const storedSidebarExpanded = localStorage.getItem('sidebar-expanded')
const sidebarExpanded = ref(storedSidebarExpanded === null ? false : storedSidebarExpanded === 'true')

const page = usePage()
const currentRoute = computed(() => {
  return {
    fullPath: window.location.pathname
  }
})

// check if route is active
const isRouteActive = (route) => {
  return currentRoute.value.fullPath.startsWith(route) || window.location.href.includes(route)
}

const clickHandler = ({ target }) => {
  if (!sidebar.value || !trigger.value) return
  if (!props.sidebarOpen || sidebar.value.contains(target) || trigger.value.contains(target)) return
  emit('close-sidebar')
}

const keyHandler = ({ keyCode }) => {
  if (!props.sidebarOpen || keyCode !== 27) return
  emit('close-sidebar')
}

onMounted(() => {
  document.addEventListener('click', clickHandler)
  document.addEventListener('keydown', keyHandler)
})

onUnmounted(() => {
  document.removeEventListener('click', clickHandler)
  document.removeEventListener('keydown', keyHandler)
})

const expandSidebar = () => {
  sidebarExpanded.value = true
  localStorage.setItem('sidebar-expanded', sidebarExpanded.value)
  if (sidebarExpanded.value) {
    document.querySelector('body').classList.add('sidebar-expanded')
  } else {
    document.querySelector('body').classList.remove('sidebar-expanded')
  }
}
</script>

<template>
  <div class="min-w-fit">
    <!-- Sidebar backdrop (mobile only) -->
    <div class="fixed inset-0 bg-gray-900/30 z-40 lg:hidden lg:z-auto transition-opacity duration-200" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'" aria-hidden="true"></div>

    <!-- Sidebar -->
    <div
      id="sidebar"
      ref="sidebar"
      class="flex lg:!flex flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-[100dvh] overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-20 lg:sidebar-expanded:!w-64 2xl:!w-64 shrink-0 bg-white dark:bg-gray-800 p-4 transition-all duration-200 ease-in-out"
      :class="[variant === 'v2' ?  'border-r border-gray-200 dark:border-gray-700/60' : 'rounded-r-2xl shadow-xs', sidebarOpen ? 'translate-x-0' : '-translate-x-64']"
    >

      <!-- Sidebar header -->
      <div class="flex justify-between mb-10 pr-3 sm:px-2">
        <!-- Close button -->
        <button
          ref="trigger"
          class="lg:hidden text-gray-500 hover:text-gray-400"
          @click.stop="$emit('close-sidebar')"
          aria-controls="sidebar"
          :aria-expanded="sidebarOpen"
        >
          <span class="sr-only">Close sidebar</span>
          <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z" />
          </svg>
        </button>
        <!-- Logo -->
        <Link class="block" :href="route('admin.dashboard')">
          <svg class="fill-violet-500" xmlns="http://www.w3.org/2000/svg" width="32" height="32">
            <path d="M31.956 14.8C31.372 6.92 25.08.628 17.2.044V5.76a9.04 9.04 0 0 0 9.04 9.04h5.716ZM14.8 26.24v5.716C6.92 31.372.63 25.08.044 17.2H5.76a9.04 9.04 0 0 1 9.04 9.04Zm11.44-9.04h5.716c-.584 7.88-6.876 14.172-14.756 14.756V26.24a9.04 9.04 0 0 1 9.04-9.04ZM.044 14.8C.63 6.92 6.92.628 14.8.044V5.76a9.04 9.04 0 0 1-9.04 9.04H.044Z" />
          </svg>
        </Link>
      </div>

      <!-- Links -->
      <div class="space-y-8">
        <!-- Dashboard -->
        <div>
          <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Principal</span>
          </h3>
          <ul class="mt-3">
            <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0" :class="route().current('admin.dashboard') && 'bg-violet-500/[0.12] dark:bg-violet-500/[0.24]'">
              <Link class="block text-gray-800 dark:text-gray-100 truncate transition duration-150" :class="route().current('admin.dashboard') ? 'text-violet-500' : 'hover:text-gray-900 dark:hover:text-white'" :href="route('admin.dashboard')">
                <div class="flex items-center">
                  <LayoutDashboard class="shrink-0 w-6 h-6" :class="route().current('admin.dashboard') ? 'text-violet-500' : 'text-gray-400 dark:text-gray-500'" />
                  <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Dashboard</span>
                </div>
              </Link>
            </li>
          </ul>
        </div>

        

        <!-- Usuários -->
        <div>
          <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Usuários</span>
          </h3>
          <ul class="mt-3">
            <SidebarLinkGroup v-slot="{ expanded, handleClick }" :activeCondition="isRouteActive('admin/usuarios')">
              <a class="block text-gray-800 dark:text-gray-100 truncate transition duration-150" :class="expanded ? '' : 'hover:text-gray-900 dark:hover:text-white'" href="#0" @click.prevent="handleClick(); sidebarExpanded = true">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <Users class="shrink-0 w-6 h-6" :class="isRouteActive('admin/usuarios') ? 'text-violet-500' : 'text-gray-400 dark:text-gray-500'" />
                    <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Usuários</span>
                  </div>
                  <div class="flex shrink-0 ml-2">
                    <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 transform transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" viewBox="0 0 12 12">
                      <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                    </svg>
                  </div>
                </div>
              </a>
              <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                <ul class="pl-9 mt-1" :class="expanded ? '!block' : 'hidden'">
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.usuarios.admins') && 'text-violet-500'" :href="route('admin.usuarios.admins')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Administradores</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.usuarios.index') && 'text-violet-500'" :href="route('admin.usuarios.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Clientes</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.verificacoes.*') && 'text-violet-500'" :href="route('admin.verificacoes.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Verificação de Documentos</span>
                    </Link>
                  </li>
                </ul>
              </div>
            </SidebarLinkGroup>
          </ul>
        </div>

        <!-- Leilões e Lotes -->
        <div>
          <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Leilões e Lotes</span>
          </h3>
          <ul class="mt-3">
            <SidebarLinkGroup v-slot="{ expanded, handleClick }" :activeCondition="isRouteActive('admin/leiloes') || isRouteActive('admin/lotes')">
              <a class="block text-gray-800 dark:text-gray-100 truncate transition duration-150" :class="expanded ? '' : 'hover:text-gray-900 dark:hover:text-white'" href="#0" @click.prevent="handleClick(); sidebarExpanded = true">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <Gavel class="shrink-0 w-6 h-6" :class="isRouteActive('admin/leiloes') || isRouteActive('admin/lotes') ? 'text-violet-500' : 'text-gray-400 dark:text-gray-500'" />
                    <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Gestão de Leilões</span>
                  </div>
                  <div class="flex shrink-0 ml-2">
                    <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 transform transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" viewBox="0 0 12 12">
                      <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                    </svg>
                  </div>
                </div>
              </a>
              <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                <ul class="pl-9 mt-1" :class="expanded ? '!block' : 'hidden'">
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.leiloes.*') && 'text-violet-500'" :href="route('admin.leiloes.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Leilão</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.lotes.*') && 'text-violet-500'" :href="route('admin.lotes.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Lotes</span>
                    </Link>
                  </li>
                </ul>
              </div>
            </SidebarLinkGroup>
          </ul>
        </div>

        <!-- Geral -->
        <div>
          <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Geral</span>
          </h3>
          <ul class="mt-3">
            <SidebarLinkGroup v-slot="{ expanded, handleClick }" :activeCondition="isRouteActive('admin/categorias') || isRouteActive('admin/comitentes')">
              <a class="block text-gray-800 dark:text-gray-100 truncate transition duration-150" :class="expanded ? '' : 'hover:text-gray-900 dark:hover:text-white'" href="#0" @click.prevent="handleClick(); sidebarExpanded = true">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <Settings class="shrink-0 w-6 h-6" :class="isRouteActive('admin/categorias') || isRouteActive('admin/comitentes') ? 'text-violet-500' : 'text-gray-400 dark:text-gray-500'" />
                    <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Cadastros</span>
                  </div>
                  <div class="flex shrink-0 ml-2">
                    <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 transform transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" viewBox="0 0 12 12">
                      <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                    </svg>
                  </div>
                </div>
              </a>
              <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                <ul class="pl-9 mt-1" :class="expanded ? '!block' : 'hidden'">
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.categorias.*') && 'text-violet-500'" :href="route('admin.categorias.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Categorias</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.comitentes.*') && 'text-violet-500'" :href="route('admin.comitentes.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Comitentes</span>
                    </Link>
                  </li>
                </ul>
              </div>
            </SidebarLinkGroup>
          </ul>
        </div>

        <div>
          <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Termos e Contas</span>
          </h3>
          <ul class="mt-3">
            <SidebarLinkGroup v-slot="{ expanded, handleClick }" :activeCondition="isRouteActive('admin/termos') || isRouteActive('admin/contas')">
              <a class="block text-gray-800 dark:text-gray-100 truncate transition duration-150" :class="expanded ? '' : 'hover:text-gray-900 dark:hover:text-white'" href="#0" @click.prevent="handleClick(); sidebarExpanded = true">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <FileText class="shrink-0 w-6 h-6" :class="isRouteActive('admin/termos') || isRouteActive('admin/contas') ? 'text-violet-500' : 'text-gray-400 dark:text-gray-500'" />
                    <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Termos e Contas</span>
                  </div>
                  <div class="flex shrink-0 ml-2">
                    <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 transform transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" viewBox="0 0 12 12">
                      <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                    </svg>
                  </div>
                </div>
              </a>
              <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                <ul class="pl-9 mt-1" :class="expanded ? '!block' : 'hidden'">
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.termos.*') && 'text-violet-500'" :href="route('admin.termos.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Termos de Arrematação</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.contas.*') && 'text-violet-500'" :href="route('admin.contas.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Contas Bancárias</span>
                    </Link>
                  </li>
                </ul>
              </div>
            </SidebarLinkGroup>
          </ul>
        </div>

        <!-- Páginas -->
        <div>
          <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Páginas</span>
          </h3>
          <ul class="mt-3">
            <SidebarLinkGroup v-slot="{ expanded, handleClick }" :activeCondition="isRouteActive('admin/paginas') && !isRouteActive('admin/paginas/duvidas-frequentes') && !isRouteActive('admin/paginas/politica-privacidade') && !isRouteActive('admin/paginas/como-funciona') && !isRouteActive('admin/paginas/termos-uso')">
              <a class="block text-gray-800 dark:text-gray-100 truncate transition duration-150" :class="expanded ? '' : 'hover:text-gray-900 dark:hover:text-white'" href="#0" @click.prevent="handleClick(); sidebarExpanded = true">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <Layers class="shrink-0 w-6 h-6" :class="(isRouteActive('admin/paginas') && !isRouteActive('admin/paginas/duvidas-frequentes') && !isRouteActive('admin/paginas/politica-privacidade') && !isRouteActive('admin/paginas/como-funciona') && !isRouteActive('admin/paginas/termos-uso')) ? 'text-violet-500' : 'text-gray-400 dark:text-gray-500'" />
                    <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Institucional</span>
                  </div>
                  <div class="flex shrink-0 ml-2">
                    <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 transform transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" viewBox="0 0 12 12">
                      <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                    </svg>
                  </div>
                </div>
              </a>
              <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                <ul class="pl-9 mt-1" :class="expanded ? '!block' : 'hidden'">
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.paginas.leiloes') && 'text-violet-500'" :href="route('admin.paginas.leiloes')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Leilões</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.paginas.quem_somos') && 'text-violet-500'" :href="route('admin.paginas.quem_somos')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Quem somos</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.paginas.patios') && 'text-violet-500'" :href="route('admin.paginas.patios')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Pátios</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.paginas.fale_conosco') && 'text-violet-500'" :href="route('admin.paginas.fale_conosco')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Fale conosco</span>
                    </Link>
                  </li>
                </ul>
              </div>
            </SidebarLinkGroup>
          </ul>
        </div>

        <!-- Páginas Rodapé -->
        <div>
          <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Páginas Rodapé</span>
          </h3>
          <ul class="mt-3">
            <SidebarLinkGroup v-slot="{ expanded, handleClick }" :activeCondition="isRouteActive('admin/paginas/duvidas-frequentes') || isRouteActive('admin/paginas/politica-privacidade') || isRouteActive('admin/paginas/como-funciona') || isRouteActive('admin/paginas/termos-uso')">
              <a class="block text-gray-800 dark:text-gray-100 truncate transition duration-150" :class="expanded ? '' : 'hover:text-gray-900 dark:hover:text-white'" href="#0" @click.prevent="handleClick(); sidebarExpanded = true">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <HelpCircle class="shrink-0 w-6 h-6" :class="(isRouteActive('admin/paginas/duvidas-frequentes') || isRouteActive('admin/paginas/politica-privacidade') || isRouteActive('admin/paginas/como-funciona') || isRouteActive('admin/paginas/termos-uso')) ? 'text-violet-500' : 'text-gray-400 dark:text-gray-500'" />
                    <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Ajuda e Legal</span>
                  </div>
                  <div class="flex shrink-0 ml-2">
                    <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 transform transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" viewBox="0 0 12 12">
                      <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                    </svg>
                  </div>
                </div>
              </a>
              <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                <ul class="pl-9 mt-1" :class="expanded ? '!block' : 'hidden'">
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.paginas.duvidas_frequentes') && 'text-violet-500'" :href="route('admin.paginas.duvidas_frequentes')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Dúvidas frequentes</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.paginas.politica_privacidade') && 'text-violet-500'" :href="route('admin.paginas.politica_privacidade')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Política de privacidade</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.paginas.como_funciona') && 'text-violet-500'" :href="route('admin.paginas.como_funciona')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Como funciona</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.paginas.termos_uso') && 'text-violet-500'" :href="route('admin.paginas.termos_uso')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Termos de uso</span>
                    </Link>
                  </li>
                </ul>
              </div>
            </SidebarLinkGroup>
          </ul>
        </div>

        <!-- Contas -->
        <div>
          <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Contas</span>
          </h3>
          <ul class="mt-3">
            <SidebarLinkGroup v-slot="{ expanded, handleClick }" :activeCondition="isRouteActive('admin/contas')">
              <a class="block text-gray-800 dark:text-gray-100 truncate transition duration-150" :class="expanded ? '' : 'hover:text-gray-900 dark:hover:text-white'" href="#0" @click.prevent="handleClick(); sidebarExpanded = true">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <FileText class="shrink-0 w-6 h-6" :class="isRouteActive('admin/contas') ? 'text-violet-500' : 'text-gray-400 dark:text-gray-500'" />
                    <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Contas</span>
                  </div>
                  <div class="flex shrink-0 ml-2">
                    <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 transform transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" viewBox="0 0 12 12">
                      <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                    </svg>
                  </div>
                </div>
              </a>
              <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                <ul class="pl-9 mt-1" :class="expanded ? '!block' : 'hidden'">
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.contas.*') && 'text-violet-500'" :href="route('admin.contas.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Contas Bancárias</span>
                    </Link>
                  </li>
                </ul>
              </div>
            </SidebarLinkGroup>
          </ul>
        </div>

        <!-- Configurações -->
        <div>
          <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
            <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
            <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Configurações</span>
          </h3>
          <ul class="mt-3">
            <SidebarLinkGroup v-slot="{ expanded, handleClick }" :activeCondition="isRouteActive('admin/configuracoes') || isRouteActive('admin/layout')">
              <a class="block text-gray-800 dark:text-gray-100 truncate transition duration-150" :class="expanded ? '' : 'hover:text-gray-900 dark:hover:text-white'" href="#0" @click.prevent="handleClick(); sidebarExpanded = true">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <LayoutTemplate class="shrink-0 w-6 h-6" :class="isRouteActive('admin/configuracoes') || isRouteActive('admin/layout') ? 'text-violet-500' : 'text-gray-400 dark:text-gray-500'" />
                    <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Layout do Site</span>
                  </div>
                  <div class="flex shrink-0 ml-2">
                    <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 transform transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" viewBox="0 0 12 12">
                      <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                    </svg>
                  </div>
                </div>
              </a>
              <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                <ul class="pl-9 mt-1" :class="expanded ? '!block' : 'hidden'">
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.layout.config.index') && 'text-violet-500'" :href="route('admin.layout.config.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Configuração</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.layout.banners.index') && 'text-violet-500'" :href="route('admin.layout.banners.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Banners</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.config.layout.index') && 'text-violet-500'" :href="route('admin.config.layout.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Template</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.email.index') && 'text-violet-500'" :href="route('admin.email.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">E-mail (SMTP)</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.layout.email.templates.index') && 'text-violet-500'" :href="route('admin.layout.email.templates.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">E-mail (Mensagens)</span>
                    </Link>
                  </li>
                  <li class="mb-1 last:mb-0">
                    <Link class="block text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 truncate" :class="route().current('admin.layout.whatsapp.templates.index') && 'text-violet-500'" :href="route('admin.layout.whatsapp.templates.index')">
                      <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">WhatsApp (Mensagens)</span>
                    </Link>
                  </li>
                </ul>
              </div>
            </SidebarLinkGroup>
          </ul>
        </div>
      </div>

      <!-- Expand / collapse button -->
      <div class="pt-3 hidden lg:inline-flex 2xl:hidden justify-end mt-auto">
        <div class="px-3 py-2">
          <button @click.prevent="sidebarExpanded = !sidebarExpanded" @click="expandSidebar">
            <span class="sr-only">Expand / collapse sidebar</span>
            <svg class="w-6 h-6 fill-current sidebar-expanded:rotate-180" viewBox="0 0 24 24">
              <path class="text-gray-400" d="M19.586 11l-5-5L16 4.586 23.414 12 16 19.414 14.586 18l5-5H7v-2z" />
              <path class="text-gray-600" d="M3 23H1V1h2z" />
            </svg>
          </button>
        </div>
      </div>

    </div>
  </div>
</template>
