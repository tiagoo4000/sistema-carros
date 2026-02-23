<template>
  <div class="relative inline-flex">
    <button
      ref="trigger"
      class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 lg:hover:bg-gray-200 dark:hover:bg-gray-700/50 dark:lg:hover:bg-gray-800 rounded-full"
      :class="{ 'bg-gray-200 dark:bg-gray-800': dropdownOpen }"
      aria-haspopup="true"
      @click.prevent="toggleOpen"
      :aria-expanded="dropdownOpen"
    >
      <span class="sr-only">Notificações</span>
      <svg class="fill-current text-gray-500/80 dark:text-gray-400/80" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
        <path d="M7 0a7 7 0 0 0-7 7c0 1.202.308 2.33.84 3.316l-.789 2.368a1 1 0 0 0 1.265 1.265l2.595-.865a1 1 0 0 0-.632-1.898l-.698.233.3-.9a1 1 0 0 0-.104-.85A4.97 4.97 0 0 1 2 7a5 5 0 0 1 5-5 4.99 4.99 0 0 1 4.093 2.135 1 1 0 1 0 1.638-1.148A6.99 6.99 0 0 0 7 0Z" />
        <path d="M11 6a5 5 0 0 0 0 10c.807 0 1.567-.194 2.24-.533l1.444.482a1 1 0 0 0 1.265-1.265l-.482-1.444A4.962 4.962 0 0 0 16 11a5 5 0 0 0-5-5Zm-3 5a3 3 0 0 1 6 0c0 .588-.171 1.134-.466 1.6a1 1 0 0 0-.115.82 1 1 0 0 0-.82.114A2.973 2.973 0 0 1 11 14a3 3 0 0 1-3-3Z" />                                        
      </svg>
      <div v-if="hasUnread" class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-500 border-2 border-gray-100 dark:border-gray-900 rounded-full"></div>
    </button>
    <transition
      enter-active-class="transition ease-out duration-200 transform"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-out duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-show="dropdownOpen" class="origin-top-right z-10 absolute top-full -mr-48 sm:mr-0 min-w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 py-1.5 rounded-lg shadow-lg overflow-hidden mt-1" :class="align === 'right' ? 'right-0' : 'left-0'">
        <div class="flex items-center justify-between pt-1.5 pb-2 px-4">
          <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">Lances Recentes</div>
          <button @click="clearItems" class="text-[11px] font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            Limpar
          </button>
        </div>
        <div class="max-h-80 overflow-y-auto">
          <ul
            ref="dropdown"
            @focusin="dropdownOpen = true"
            @focusout="dropdownOpen = false"
          >
            <li v-if="loading" class="py-4 px-4 text-sm text-gray-500">Carregando...</li>
            <li v-else-if="error" class="py-4 px-4 text-sm text-red-600">Erro ao carregar</li>
            <li v-else-if="items.length === 0" class="py-4 px-4 text-sm text-gray-500">Sem lances recentes</li>
            <li v-for="n in items" :key="n.id" class="border-b border-gray-200 dark:border-gray-700/60 last:border-0">
              <a class="block py-2.5 px-4 hover:bg-gray-50 dark:hover:bg-gray-700/20" :href="n.lote.url" @click="dropdownOpen = false">
                <div class="flex items-center justify-between">
                  <div class="text-sm">
                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ n.usuario.nome }}</span>
                    <span class="text-gray-500 dark:text-gray-400"> deu lance de </span>
                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ formatBRL(n.valor) }}</span>
                  </div>
                </div>
                <div class="text-xs mt-1 text-gray-500 dark:text-gray-400">
                  <span>{{ n.lote.titulo || 'Lote' }}</span>
                  <span class="mx-2">•</span>
                  <span>{{ formatDateTime(n.criado_em) }}</span>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div> 
    </transition>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

export default {
  name: 'DropdownNotifications',
  props: ['align'],
  setup() {
    const dropdownOpen = ref(false)
    const trigger = ref(null)
    const dropdown = ref(null)
    const items = ref([])
    const loading = ref(false)
    const error = ref(false)
    const hasUnread = ref(true)

    const fetchItems = async () => {
      loading.value = true
      error.value = false
      try {
        const { data } = await axios.get('/admin/api/lances-recentes')
        items.value = data || []
        hasUnread.value = items.value.length > 0
      } catch (e) {
        error.value = true
      } finally {
        loading.value = false
      }
    }

    const toggleOpen = async () => {
      dropdownOpen.value = !dropdownOpen.value
      if (dropdownOpen.value && items.value.length === 0 && !loading.value) {
        await fetchItems()
        hasUnread.value = false
      }
    }

    const clickHandler = ({ target }) => {
      if (!dropdownOpen.value || dropdown.value.contains(target) || trigger.value.contains(target)) return
      dropdownOpen.value = false
    }
    const keyHandler = ({ keyCode }) => {
      if (!dropdownOpen.value || keyCode !== 27) return
      dropdownOpen.value = false
    }

    const formatBRL = (v) => {
      try {
        return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v || 0)
      } catch { return `R$ ${Number(v || 0).toFixed(2)}` }
    }
    const formatDateTime = (iso) => {
      const d = new Date(iso)
      const dia = d.toLocaleDateString('pt-BR')
      const hora = d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
      return `${dia} ${hora}`
    }
    const clearItems = async () => {
      try {
        await axios.post('/admin/api/notificacoes/limpar')
      } catch (e) {
        // ignorar erro silenciosamente, limpeza é otimista
      } finally {
        items.value = []
        hasUnread.value = false
      }
    }

    onMounted(() => {
      document.addEventListener('click', clickHandler)
      document.addEventListener('keydown', keyHandler)
    })
    onUnmounted(() => {
      document.removeEventListener('click', clickHandler)
      document.removeEventListener('keydown', keyHandler)
    })

    return {
      dropdownOpen,
      trigger,
      dropdown,
      items,
      loading,
      error,
      hasUnread,
      toggleOpen,
      formatBRL,
      formatDateTime,
      clearItems,
    }
  }
}
</script>
