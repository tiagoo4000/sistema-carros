<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { CheckCircle, FileText, Download } from 'lucide-vue-next';
import { onMounted, computed } from 'vue';

const props = defineProps({
  termo: Object
});

const form = useForm({ aceite: false });
const submit = () => {
  const acceptUrl = typeof route === 'function'
    ? route('cliente.termos.accept', props.termo.id)
    : `/minha-conta/termos/${props.termo.id}/aceitar`;
  form.post(acceptUrl);
};

onMounted(() => {
  const meta = document.querySelector('meta[name="csrf-token"]');
  const token = meta && meta.getAttribute('content') ? meta.getAttribute('content') : null;
  const headers = { 'X-Requested-With': 'XMLHttpRequest' };
  if (token) headers['X-CSRF-TOKEN'] = token;
  const viewedUrl = typeof route === 'function'
    ? route('cliente.termos.viewed', props.termo.id)
    : `/minha-conta/termos/${props.termo.id}/visualizado`;
  fetch(viewedUrl, {
    method: 'POST',
    headers
  }).catch(() => {});
});

const pdfUrl = computed(() => {
  return typeof route === 'function'
    ? route('cliente.termos.pdf', props.termo.id)
    : `/minha-conta/termos/${props.termo.id}/pdf`;
});

const downloadUrl = computed(() => {
  return typeof route === 'function'
    ? route('cliente.termos.download', props.termo.id)
    : `/minha-conta/termos/${props.termo.id}/download`;
});
</script>

<template>
  <Head :title="`Termo ${termo.numero || '#' + termo.id}`" />
  <SiteLayout>
    <div class="max-w-5xl mx-auto p-6 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-[#002f6c]">Termo de Arrematação</h1>
          <p class="text-sm text-gray-600 mt-1">
            Número: <span class="font-medium text-gray-900">{{ termo.numero ?? `#${termo.id}` }}</span>
          </p>
        </div>
        <div>
          <Link v-if="termo.status==='aceito'" :href="downloadUrl" class="inline-flex items-center px-3 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
            <Download class="w-4 h-4 mr-2" /> Baixar PDF
          </Link>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <FileText class="w-5 h-5 text-gray-500" />
            <span class="text-sm text-gray-700">Visualização do documento</span>
          </div>
          <span :class="termo.status==='aceito' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'" class="px-2 py-0.5 rounded-full text-xs font-semibold">
            {{ termo.status==='aceito' ? 'Aceito pelo arrematante' : 'Pendente de aceite' }}
          </span>
        </div>
        <div class="aspect-[1/1.3] w-full">
          <iframe :src="pdfUrl" class="w-full h-full"></iframe>
        </div>
      </div>

      <div v-if="termo.status!=='aceito'" class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <label class="flex items-start gap-3">
          <input type="checkbox" v-model="form.aceite" class="mt-1" />
          <span class="text-sm text-gray-700">
            Li e aceito os termos do documento acima.
          </span>
        </label>
        <div class="flex items-center gap-3">
          <button :disabled="!form.aceite || form.processing" @click="submit" class="inline-flex items-center px-4 py-2 rounded bg-[#002f6c] text-white hover:bg-[#004090] disabled:opacity-50">
            <CheckCircle class="w-4 h-4 mr-2" /> Aceitar Termo
          </button>
          <div v-if="form.errors.aceite" class="text-sm text-red-600">{{ form.errors.aceite }}</div>
        </div>
      </div>
    </div>
  </SiteLayout>
</template>
