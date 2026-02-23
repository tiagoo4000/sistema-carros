<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { CheckCircle, XCircle, Clock, Download } from 'lucide-vue-next';

const props = defineProps({
  documentos: Object
});

const badgeClass = (d) => d.validado ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : d.observacoes ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-700 border-amber-200';
</script>

<template>
  <Head title="Meus Documentos" />
  <SiteLayout>
    <div class="min-h-[70vh] bg-gray-50">
      <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-2xl font-bold text-gray-900">Meus Documentos</h1>
          <Link :href="route('minha-conta.documentos')" class="text-sm text-emerald-700 hover:text-emerald-800">Enviar/Verificação</Link>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
          <table class="table-auto w-full">
            <thead class="text-xs uppercase text-gray-500 bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left">Tipo</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Motivo</th>
                <th class="px-4 py-3 text-left">Enviado em</th>
                <th class="px-4 py-3 text-right">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr v-for="d in documentos.data" :key="d.id" class="hover:bg-gray-50">
                <td class="px-4 py-3 capitalize">{{ d.tipo.replace('_',' ') }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full border" :class="badgeClass(d)">
                    <CheckCircle v-if="d.validado" class="w-3 h-3" />
                    <XCircle v-else-if="d.observacoes" class="w-3 h-3" />
                    <Clock v-else class="w-3 h-3" />
                    <span v-if="d.validado">Aprovado</span>
                    <span v-else-if="d.observacoes">Rejeitado</span>
                    <span v-else>Pendente</span>
                  </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                  <span v-if="d.observacoes">{{ d.observacoes }}</span>
                  <span v-else class="text-gray-400">—</span>
                </td>
                <td class="px-4 py-3">{{ new Date(d.created_at).toLocaleString('pt-BR') }}</td>
                <td class="px-4 py-3 text-right">
                  <Link :href="route('minha-conta.documentos.download', d.id)" class="inline-flex items-center px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-sm">
                    <Download class="w-4 h-4 mr-1" /> Download
                  </Link>
                </td>
              </tr>
              <tr v-if="documentos.data.length === 0">
                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Você ainda não enviou nenhum documento.</td>
              </tr>
            </tbody>
          </table>

          <div class="px-4 py-3 border-t bg-white flex items-center justify-between" v-if="documentos">
            <div class="text-sm text-gray-600">
              Página {{ documentos.current_page }} de {{ documentos.last_page }}
            </div>
            <div class="flex items-center gap-2">
              <Link
                v-if="documentos.current_page > 1"
                :href="route('minha-conta.meus-documentos', { page: documentos.current_page - 1 })"
                class="px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-sm"
                preserve-scroll
                preserve-state
              >
                Anterior
              </Link>
              <Link
                v-if="documentos.current_page < documentos.last_page"
                :href="route('minha-conta.meus-documentos', { page: documentos.current_page + 1 })"
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
    </div>
  </SiteLayout>
  </template>
