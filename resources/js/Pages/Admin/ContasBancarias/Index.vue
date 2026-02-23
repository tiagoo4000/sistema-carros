<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Plus, Edit, Trash2, CheckCircle, XCircle, Star } from 'lucide-vue-next';

const props = defineProps({
  contas: Object,
});

const formDelete = useForm({});
const remover = (id) => {
  if (confirm('Remover esta conta?')) formDelete.delete(route('admin.contas.destroy', id));
};

const formPadrao = useForm({});
const definirPadrao = (id) => {
  formPadrao.patch(route('admin.contas.definir_padrao', id));
};
</script>

<template>
  <Head title="Contas Bancárias" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-slate-900">Contas Bancárias</h1>
      <Link :href="route('admin.contas.create')" class="inline-flex items-center px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
        <Plus class="w-4 h-4 mr-2" /> Nova Conta
      </Link>
    </div>
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Banco</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agência/Conta</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Beneficiário</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="c in contas.data" :key="c.id">
            <td class="px-4 py-3">
              <div class="font-medium text-gray-900">{{ c.banco }}</div>
            </td>
            <td class="px-4 py-3 text-gray-700">
              {{ c.agencia }} / {{ c.conta }} ({{ c.tipo_conta === 'corrente' ? 'Corrente' : 'Poupança' }})
            </td>
            <td class="px-4 py-3 text-gray-700">
              <div v-if="c.tipo_documento === 'cpf'">
                {{ c.nome_completo }} — CPF {{ c.cpf }}
              </div>
              <div v-else>
                {{ c.razao_social }} — CNPJ {{ c.cnpj }}
              </div>
            </td>
            <td class="px-4 py-3">
              <span v-if="c.ativa" class="inline-flex items-center text-emerald-700 text-sm"><CheckCircle class="w-4 h-4 mr-1" /> Ativa</span>
              <span v-else class="inline-flex items-center text-gray-500 text-sm"><XCircle class="w-4 h-4 mr-1" /> Inativa</span>
              <span v-if="c.padrao" class="ml-2 inline-flex items-center text-amber-600 text-xs"><Star class="w-4 h-4 mr-1" /> Padrão</span>
            </td>
            <td class="px-4 py-3 text-right text-sm">
              <div class="inline-flex items-center gap-2">
                <button v-if="!c.padrao" @click="definirPadrao(c.id)" class="px-2 py-1 rounded border hover:bg-gray-50">Definir padrão</button>
                <Link :href="route('admin.contas.edit', c.id)" class="text-indigo-600 hover:text-indigo-800"><Edit class="w-4 h-4" /></Link>
                <button @click="remover(c.id)" class="text-red-600 hover:text-red-800"><Trash2 class="w-4 h-4" /></button>
              </div>
            </td>
          </tr>
          <tr v-if="contas.data.length === 0">
            <td colspan="5" class="px-4 py-10 text-center text-gray-500">Nenhuma conta cadastrada.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
  </template>

