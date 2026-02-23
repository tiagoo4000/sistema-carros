<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Menu, MenuButton, MenuItems, MenuItem, TransitionRoot, TransitionChild } from '@headlessui/vue'
import { 
  User, 
  LayoutDashboard, 
  Gavel, 
  ShoppingBag, 
  LifeBuoy, 
  LogOut,
  FileText
} from 'lucide-vue-next'

const page = usePage()
const user = computed(() => page.props.auth.user || {})

const initials = computed(() => {
  const n = (user.value?.nome || 'Usuário').trim()
  const parts = n.split(/\s+/).slice(0, 2)
  return parts.map(p => p[0]?.toUpperCase() || '').join('').slice(0, 2) || 'U'
})

const menuItems = computed(() => ([
  { label: 'Dashboard', icon: LayoutDashboard, href: route('minha-conta') },
  { label: 'Perfil', icon: User, href: route('minha-conta') },
  { label: 'Verificação de conta', icon: FileText, href: route('minha-conta.documentos') },
  { label: 'Meus documentos', icon: FileText, href: route('minha-conta.meus-documentos') },
  { label: 'Meus lances', icon: Gavel, href: route('minha-conta') },
  { label: 'Meus leilões', icon: ShoppingBag, href: route('minha-conta') },
  { label: 'Suporte', icon: LifeBuoy, href: '#' },
]))
</script>

<template>
  <Menu as="div" class="relative inline-flex">
    <MenuButton class="inline-flex justify-center items-center group gap-2">
      <div class="flex items-center gap-2 bg-gray-50 hover:bg-gray-100 rounded-full pl-1.5 pr-2.5 py-1 border border-gray-200 transition-all">
        <div class="h-7 w-7 rounded-full bg-[#002f6c] text-white flex items-center justify-center font-bold text-xs">
          {{ initials }}
        </div>
        <div class="flex flex-col items-start leading-tight">
          <span class="text-[13px] font-semibold text-[#002f6c] truncate max-w-[96px] normal-case">{{ user?.nome ? user.nome.split(' ')[0] : 'Usuário' }}</span>
          <span class="text-[11px] text-gray-500 normal-case">Minha Conta</span>
        </div>
        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400" viewBox="0 0 12 12">
          <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
        </svg>
      </div>
    </MenuButton>

    <TransitionRoot
      enter="transition ease-out duration-150 transform"
      enter-from="opacity-0 translate-y-1 scale-95"
      enter-to="opacity-100 translate-y-0 scale-100"
      leave="transition ease-in duration-100"
      leave-from="opacity-100"
      leave-to="opacity-0"
    >
      <MenuItems class="origin-top-right z-50 absolute top-full right-0 w-[240px] max-w-[260px] bg-white border border-gray-100 rounded-xl shadow-lg shadow-black/5 overflow-hidden mt-2 focus:outline-none">
        <MenuItem v-slot="{ active }">
          <Link :href="route('minha-conta')" :class="['flex items-center gap-2.5 p-2.5 transition-colors', active ? 'bg-gray-50/70' : 'bg-white']">
            <div class="h-8 w-8 rounded-full bg-[#002f6c] text-white flex items-center justify-center font-bold text-sm shrink-0">
              {{ initials }}
            </div>
            <div class="min-w-0">
              <div class="text-[13px] font-semibold text-gray-900 truncate normal-case">{{ user?.nome || 'Usuário' }}</div>
              <div class="text-[11px] text-gray-500 truncate normal-case">{{ user?.email || '' }}</div>
            </div>
          </Link>
        </MenuItem>

        <div class="py-0.5">
          <MenuItem v-for="item in menuItems" :key="item.label" v-slot="{ active }" as="div">
            <Link
              :href="item.href"
              :class="['flex items-center gap-2 px-2 py-1.5 mx-1 rounded-md text-[13px] transition-colors cursor-pointer', active ? 'bg-gray-50 text-gray-900' : 'text-gray-700']"
            >
              <component :is="item.icon" class="w-4 h-4 text-gray-400" />
              <span class="font-medium normal-case">{{ item.label }}</span>
            </Link>
          </MenuItem>

          <div class="my-1 border-t border-gray-100 mx-1"></div>

          <MenuItem v-slot="{ active }">
            <Link :href="route('logout')" method="post" as="button" :class="['w-[calc(100%-0.5rem)] mx-1 mb-1 flex items-center gap-2 px-2 py-1.5 rounded-md text-[13px] font-medium transition-colors cursor-pointer', active ? 'bg-red-50 text-red-700' : 'text-red-600 hover:bg-red-50']">
              <LogOut class="w-4 h-4" />
              <span>Sair</span>
            </Link>
          </MenuItem>
        </div>
      </MenuItems>
    </TransitionRoot>
  </Menu>
</template>
