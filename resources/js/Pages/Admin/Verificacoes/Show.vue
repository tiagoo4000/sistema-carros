<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ZoomIn, Download, X, Maximize2, CheckCircle, XCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
  usuario: Object,
  documentos: Array
});

const modal = ref({ open: false, url: null, tipo: null });
const openModal = (url, tipo) => { modal.value = { open: true, url, tipo }; };
const closeModal = () => { modal.value.open = false; };

const isImage = (path) => /\.(jpg|jpeg|png|webp)$/i.test(path || '');
const isPdf = (path) => /\.pdf$/i.test(path || '');

const formApprove = useForm({});
const formForce = useForm({});
const formReject = useForm({ motivo: '' });

const approveAll = () => {
  formApprove.post(route('admin.verificacoes.aprovar', props.usuario.id));
};
const forceApproval = () => {
  const msg = 'Atenção: ao forçar a aprovação, o cliente será liberado imediatamente e o envio de novos documentos pelo site será desativado. Confirma prosseguir?';
  if (!confirm(msg)) return;
  formForce.post(route('admin.verificacoes.forcar_aprovacao', props.usuario.id));
};
const rejectAll = () => {
  if (!formReject.motivo) {
    alert('Informe o motivo da rejeição.');
    return;
  }
  formReject.post(route('admin.verificacoes.rejeitar', props.usuario.id));
};
</script>

<template>
  <Head title="Análise de Documentos" />
  <AdminLayout>
    <div class="max-w-7xl mx-auto space-y-4">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Análise de Documentos</h1>
        <Link :href="route('admin.verificacoes.index')" class="text-sm text-gray-600 hover:text-gray-900">Voltar</Link>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Esquerda: Documentos -->
        <div class="lg:col-span-2 space-y-6">
          <div v-for="doc in documentos" :key="doc.id" class="bg-white rounded-xl border p-4">
            <div class="flex items-center justify-between mb-3">
              <div class="font-semibold capitalize">{{ doc.tipo.replace('_',' ') }}</div>
              <div class="text-xs">
                <span v-if="doc.validado" class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">
                  <CheckCircle class="w-3 h-3 mr-1" /> Aprovado
                </span>
                <span v-else-if="doc.observacoes" class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 text-red-700">
                  <XCircle class="w-3 h-3 mr-1" /> Rejeitado
                </span>
                <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
                  Pendente
                </span>
              </div>
            </div>
            <div class="relative border rounded-lg overflow-hidden bg-gray-50">
              <template v-if="isImage(doc.caminho)">
                <img :src="route('admin.documentos.download', doc.id)" class="w-full max-h-[480px] object-contain bg-black/5" />
              </template>
              <template v-else-if="isPdf(doc.caminho)">
                <iframe :src="route('admin.documentos.download', doc.id)" class="w-full h-[480px] bg-white"></iframe>
              </template>
              <template v-else>
                <div class="p-6 text-sm text-gray-600">Formato não visualizável. Faça o download.</div>
              </template>
              <div class="absolute top-3 right-3 flex gap-2">
                <button @click="openModal(route('admin.documentos.download', doc.id), doc.tipo)" class="p-2 rounded bg-white border hover:bg-gray-50" title="Ampliar">
                  <Maximize2 class="w-4 h-4" />
                </button>
                <a :href="route('admin.documentos.download', doc.id)" target="_blank" class="p-2 rounded bg-white border hover:bg-gray-50" title="Download">
                  <Download class="w-4 h-4" />
                </a>
              </div>
            </div>
            <div v-if="doc.observacoes" class="mt-2 text-sm text-red-700">Motivo: {{ doc.observacoes }}</div>
            <div class="mt-1 text-xs text-gray-500">Enviado em: {{ new Date(doc.created_at).toLocaleString('pt-BR') }}</div>
          </div>
        </div>

        <!-- Direita: Usuário -->
        <div class="space-y-4">
          <div class="bg-white rounded-xl border p-4">
            <div class="font-semibold mb-3">Informações do Usuário</div>
            <div class="text-sm space-y-2">
              <div><span class="text-gray-500">Nome:</span> <span class="font-medium">{{ usuario.nome }}</span></div>
              <div><span class="text-gray-500">CPF:</span> <span class="font-medium">{{ usuario.cpf || '-' }}</span></div>
              <div><span class="text-gray-500">E-mail:</span> <span class="font-medium">{{ usuario.email }}</span></div>
              <div><span class="text-gray-500">Telefone:</span> <span class="font-medium">{{ usuario.telefone || '-' }}</span></div>
              <div><span class="text-gray-500">Cadastro:</span> <span class="font-medium">{{ new Date(usuario.created_at).toLocaleString('pt-BR') }}</span></div>
            </div>
          </div>
          <div class="bg-white rounded-xl border p-4">
            <div class="font-semibold mb-2">Ações</div>
            <div class="flex flex-col gap-2">
              <button @click="approveAll" class="inline-flex items-center justify-center px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
                Aprovar Todos
              </button>
              <button @click="forceApproval" class="inline-flex items-center justify-center px-4 py-2 rounded bg-amber-600 text-white hover:bg-amber-700">
                Forçar Aprovação
              </button>
              <div class="space-y-2">
                <textarea v-model="formReject.motivo" rows="3" class="w-full rounded border-gray-300" placeholder="Motivo da rejeição (obrigatório para rejeitar)"></textarea>
                <button @click="rejectAll" class="inline-flex items-center justify-center px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                  Rejeitar
                </button>
              </div>
            </div>
            <div class="text-xs text-gray-500 mt-2">
              As ações serão registradas no log de auditoria com data, IP e responsável.
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Fullscreen -->
      <div v-if="modal.open" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-6">
        <div class="relative max-w-6xl w-full bg-white rounded-xl overflow-hidden">
          <button @click="closeModal" class="absolute top-3 right-3 p-2 rounded bg-white/90 hover:bg-white shadow">
            <X class="w-5 h-5" />
          </button>
          <div class="p-4">
            <div v-if="isImage(modal.url)" class="overflow-auto">
              <img :src="modal.url" class="w-full max-h-[80vh] object-contain" />
            </div>
            <div v-else class="h-[80vh]">
              <iframe :src="modal.url" class="w-full h-full bg-white"></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
