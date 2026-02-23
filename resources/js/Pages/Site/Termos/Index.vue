<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { FileText, Download } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
  termos: Object
});

const showHref = (id) => (typeof route === 'function' ? route('cliente.termos.show', id) : `/minha-conta/termos/${id}`);
const downloadHref = (id) => (typeof route === 'function' ? route('cliente.termos.download', id) : `/minha-conta/termos/${id}/download`);
</script>

<template>
  <Head title="Meus Termos" />
  <SiteLayout>
    <div class="max-w-5xl mx-auto p-6">
      <h1 class="text-2xl font-bold text-[#002f6c] mb-4">Termos de Arrematação</h1>
      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left">Número</th>
              <th class="px-4 py-3 text-left">Lote</th>
              <th class="px-4 py-3 text-left">Valor</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="t in termos.data" :key="t.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 font-medium text-gray-900">{{ t.numero ?? `#${t.id}` }}</td>
              <td class="px-4 py-3 text-gray-700">#{{ t.lote_id }}</td>
              <td class="px-4 py-3 text-gray-700">R$ {{ Number(t.valor_arrematacao).toFixed(2) }}</td>
              <td class="px-4 py-3">
                <span :class="t.status === 'aceito' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'" class="px-2 py-0.5 rounded-full text-xs font-semibold">
                  {{ t.status === 'aceito' ? 'Aceito' : 'Pendente de aceite' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right space-x-2">
                <Link :href="showHref(t.id)" class="inline-flex items-center px-3 py-1.5 rounded bg-[#002f6c] text-white hover:bg-[#004090]">
                  <FileText class="w-4 h-4 mr-2" /> Visualizar
                </Link>
                <Link v-if="t.status==='aceito'" :href="downloadHref(t.id)" class="inline-flex items-center px-3 py-1.5 rounded bg-emerald-600 text-white hover:bg-emerald-700">
                  <Download class="w-4 h-4 mr-2" /> Baixar PDF
                </Link>
              </td>
            </tr>
            <tr v-if="termos.data.length === 0">
              <td colspan="5" class="px-4 py-8 text-center text-gray-500">Nenhum termo disponível.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </SiteLayout>
  </template>
