<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Filter, Search, CheckCircle, XCircle, Clock } from 'lucide-vue-next';

const props = defineProps({
  registros: Object,
  filters: Object
});

const form = useForm({
  status: props.filters.status || '',
  nome: props.filters.nome || '',
  cpf: props.filters.cpf || '',
  apenas_pendentes: props.filters.apenas_pendentes || false,
  de: props.filters.de || '',
  ate: props.filters.ate || ''
});

const applyFilters = () => {
  form.get(route('admin.verificacoes.index'), { preserveState: true, preserveScroll: true });
};

const badgeClass = (st) => st === 'approved' ? 'bg-emerald-100 text-emerald-700' : st === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700';
</script>

<template>
  <Head title="Verificação de Documentos" />
  <AdminLayout>
    <div class="w-full max-w-7xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Verificação de Documentos</h1>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded-xl border p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
          <div>
            <label class="text-xs text-gray-500">Status</label>
            <select v-model="form.status" class="w-full rounded border-gray-300">
              <option value="">Todos</option>
              <option value="pending">Pendente</option>
              <option value="approved">Aprovado</option>
              <option value="rejected">Rejeitado</option>
            </select>
          </div>
          <div>
            <label class="text-xs text-gray-500">Nome</label>
            <input v-model="form.nome" type="text" class="w-full rounded border-gray-300" placeholder="Nome" />
          </div>
          <div>
            <label class="text-xs text-gray-500">CPF</label>
            <input v-model="form.cpf" type="text" class="w-full rounded border-gray-300" placeholder="CPF" />
          </div>
          <div>
            <label class="text-xs text-gray-500">De</label>
            <input v-model="form.de" type="date" class="w-full rounded border-gray-300" />
          </div>
          <div>
            <label class="text-xs text-gray-500">Até</label>
            <input v-model="form.ate" type="date" class="w-full rounded border-gray-300" />
          </div>
          <div class="flex items-center gap-2">
            <label class="inline-flex items-center gap-2 mt-5">
              <input v-model="form.apenas_pendentes" type="checkbox" />
              <span class="text-sm">Apenas pendentes</span>
            </label>
            <button @click="applyFilters" class="ml-auto inline-flex items-center px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
              <Filter class="w-4 h-4 mr-2" /> Filtrar
            </button>
          </div>
        </div>
      </div>

      <!-- Tabela -->
      <div class="bg-white rounded-xl border overflow-hidden">
        <table class="table-auto w-full">
          <thead class="text-xs uppercase text-gray-500 bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left">Usuário</th>
              <th class="px-4 py-3 text-left">CPF</th>
              <th class="px-4 py-3 text-left">Data Envio</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-left">Prioridade</th>
              <th class="px-4 py-3 text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="u in registros.data" :key="u.id" class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <div class="font-medium text-gray-900">{{ u.nome }}</div>
                <div class="text-xs text-gray-500">{{ u.email }}</div>
              </td>
              <td class="px-4 py-3">{{ u.cpf || '-' }}</td>
              <td class="px-4 py-3">{{ u.ultimo_envio ? new Date(u.ultimo_envio).toLocaleString('pt-BR') : '-' }}</td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium" :class="badgeClass(u.status)">
                  <template v-if="u.status === 'approved'"><CheckCircle class="w-3 h-3 mr-1" /> Aprovado</template>
                  <template v-else-if="u.status === 'rejected'"><XCircle class="w-3 h-3 mr-1" /> Rejeitado</template>
                  <template v-else><Clock class="w-3 h-3 mr-1" /> Pendente</template>
                </span>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="u.prioridade === 'Alta' ? 'bg-red-100 text-red-700' : u.prioridade === 'Média' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700'">
                  {{ u.prioridade }}
                </span>
              </td>
              <td class="px-4 py-3 text-right">
                <Link :href="route('admin.verificacoes.show', u.id)" class="inline-flex items-center px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-sm">
                  Analisar
                </Link>
              </td>
            </tr>
            <tr v-if="registros.data.length === 0">
              <td colspan="6" class="px-4 py-8 text-center text-gray-500">Nenhuma verificação encontrada.</td>
            </tr>
          </tbody>
        </table>
        <div class="px-4 py-3 border-t bg-white flex items-center justify-between" v-if="registros">
          <div class="text-sm text-gray-600">
            Página {{ registros.current_page }} de {{ registros.last_page }}
          </div>
          <div class="flex items-center gap-2">
            <Link
              v-if="registros.current_page > 1"
              :href="route('admin.verificacoes.index', { ...form.data(), page: registros.current_page - 1 })"
              class="px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-sm"
              preserve-scroll
              preserve-state
            >
              Anterior
            </Link>
            <Link
              v-if="registros.current_page < registros.last_page"
              :href="route('admin.verificacoes.index', { ...form.data(), page: registros.current_page + 1 })"
              class="px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-sm"
              preserve-scroll
              preserve-state
            >
              Próxima
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
