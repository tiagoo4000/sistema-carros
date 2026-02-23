<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { FileText, Send, Trash2, RefreshCcw, Eye, CloudUpload } from 'lucide-vue-next';
import { reactive } from 'vue';

const props = defineProps({
  termos: Object,
  contas: Array
});

const formGenerate = useForm({});
const gerarHoje = () => {
  formGenerate.post(route('admin.termos.generate_today'));
};

const formResend = useForm({});
const reenviar = (id) => {
  formResend.post(route('admin.termos.resend', id));
};

const formDelete = useForm({});
const remover = (id) => {
  if (confirm('Remover este termo?')) formDelete.delete(route('admin.termos.destroy', id));
};

const formGeneratePending = useForm({});
const gerarPendentes = () => {
  formGeneratePending.post(route('admin.termos.generate_pending'));
};

const modal = reactive({ open: false, termoId: null, contaId: null });
const openDisponibilizar = (termoId) => {
  modal.open = true;
  modal.termoId = termoId;
  modal.contaId = props.contas?.[0]?.id ?? null;
};
const formDisponibilizar = useForm({ conta_id: null });
const confirmarDisponibilizacao = () => {
  formDisponibilizar.conta_id = modal.contaId;
  formDisponibilizar.post(route('admin.termos.disponibilizar', modal.termoId), {
    onSuccess: () => { modal.open = false; }
  });
};
</script>

<template>
  <Head title="Termos de Arrematação" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-slate-900">Termos de Arrematação</h1>
      <div class="flex gap-2">
        <button @click="gerarHoje" class="inline-flex items-center px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
          <RefreshCcw class="w-4 h-4 mr-2" /> Gerar termos do dia
        </button>
        <button @click="gerarPendentes" class="inline-flex items-center px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
          <RefreshCcw class="w-4 h-4 mr-2" /> Gerar termos pendentes
        </button>
      </div>
    </div>
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Arrematante</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="t in termos.data" :key="t.id">
            <td class="px-4 py-3 text-gray-900">{{ t.numero ?? '—' }}</td>
            <td class="px-4 py-3">
              <div class="text-sm text-gray-900">#{{ t.lote_id }}</div>
              <div class="text-xs text-gray-500">Leilão #{{ t.leilao_id }}</div>
            </td>
            <td class="px-4 py-3">
              <div class="text-sm text-gray-900">{{ t.arrematante_nome }}</div>
              <div class="text-xs text-gray-500">{{ t.arrematante_email }}</div>
            </td>
            <td class="px-4 py-3 text-gray-900">R$ {{ Number(t.valor_arrematacao).toFixed(2) }}</td>
            <td class="px-4 py-3 text-sm">
              <span v-if="t.status==='aceito'" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Aceito</span>
              <span v-else-if="t.status==='disponibilizado' && !t.visualizado_em" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Não visualizado</span>
              <span v-else-if="t.status==='disponibilizado' && t.visualizado_em" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Visualizado</span>
              <span v-else class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Pendente</span>
            </td>
            <td class="px-4 py-3 text-right">
              <div class="inline-flex items-center gap-2">
                <Link v-if="t.pdf_path" :href="route('admin.termos.pdf', t.id)" class="text-gray-700 hover:text-gray-900"><FileText class="w-4 h-4" /></Link>
                <button @click="openDisponibilizar(t.id)" class="text-blue-600 hover:text-blue-800" title="Disponibilizar para cliente"><CloudUpload class="w-4 h-4" /></button>
                <button v-if="t.arrematante_email" @click="reenviar(t.id)" class="text-indigo-600 hover:text-indigo-800"><Send class="w-4 h-4" /></button>
                <button @click="remover(t.id)" class="text-red-600 hover:text-red-800"><Trash2 class="w-4 h-4" /></button>
              </div>
            </td>
          </tr>
          <tr v-if="termos.data.length === 0">
            <td colspan="5" class="px-4 py-10 text-center text-gray-500">Nenhum termo gerado.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Modal Seleção de Conta -->
    <div v-if="modal.open" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
      <div class="bg-white w-full max-w-lg rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b">
          <h3 class="text-lg font-semibold">Selecionar conta bancária</h3>
        </div>
        <div class="p-6 space-y-4">
          <div v-if="!contas || contas.length===0" class="text-sm text-gray-600">
            Nenhuma conta ativa encontrada. Cadastre em Termos e Contas > Contas.
          </div>
          <div v-else class="space-y-3">
            <label v-for="c in contas" :key="c.id" class="flex items-start gap-3 p-3 border rounded-lg hover:bg-gray-50">
              <input type="radio" name="conta" :value="c.id" v-model="modal.contaId" />
              <div>
                <div class="text-sm font-medium text-gray-900">{{ c.banco }} • {{ c.tipo_documento?.toUpperCase() }}</div>
                <div class="text-xs text-gray-600">
                  Titular: {{ c.tipo_documento==='cpf' ? (c.nome_completo || '—') : (c.razao_social || '—') }}
                </div>
                <div class="text-xs text-gray-600">
                  PIX: {{ c.chave_pix || '—' }}
                </div>
              </div>
            </label>
          </div>
        </div>
        <div class="px-6 py-4 border-t flex items-center justify-end gap-2">
          <button @click="modal.open=false" class="px-4 py-2 rounded border">Cancelar</button>
          <button :disabled="!modal.contaId" @click="confirmarDisponibilizacao" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50">Disponibilizar</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
