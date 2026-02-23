<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  template: Object,
  variables: Object
})

const form = useForm({
  enabled: props.template.enabled,
  text: props.template.text || '',
})

const testForm = useForm({
  test_to: ''
})

const restoreDefault = () => {
  form.text = props.template.text || ''
}

const submit = () => {
  form.post(route('admin.layout.whatsapp.templates.update', props.template.key), {
    preserveScroll: true
  })
}

const sendTest = () => {
  testForm.post(route('admin.layout.whatsapp.templates.test', props.template.key), {
    preserveScroll: true
  })
}
</script>

<template>
  <AdminLayout :title="`Editar WhatsApp: ${template.name}`">
    <Head :title="`Editar WhatsApp: ${template.name}`" />
    <div class="max-w-6xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Conteúdo</h3>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
              <input type="checkbox" v-model="form.enabled" class="rounded border-gray-300 dark:border-gray-600" />
              Ativo
            </label>
          </div>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mensagem (texto puro)</label>
              <textarea v-model="form.text" rows="10" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900"></textarea>
            </div>
            <div class="flex items-center gap-2">
              <button type="button" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300" :disabled="form.processing" @click="restoreDefault">Restaurar</button>
              <button type="button" class="px-3 py-2 rounded-md bg-violet-600 text-white hover:bg-violet-700 disabled:opacity-50" :disabled="form.processing" @click="submit">Salvar</button>
              <Link :href="route('admin.layout.whatsapp.templates.index')" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300">Voltar</Link>
            </div>
          </div>
        </div>
        <div class="space-y-6">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Variáveis disponíveis</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div v-for="(vars, group) in variables" :key="group">
                <div class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">{{ group }}</div>
                <div class="text-xs space-y-1">
                  <div v-for="v in vars" :key="v" class="font-mono px-2 py-1 rounded bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700/60 text-gray-700 dark:text-gray-200">
                    {{ v }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Teste rápido</h3>
            <div class="flex items-end gap-2">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número (com DDI)</label>
                <input v-model="testForm.test_to" type="text" placeholder="5511999999999" class="w-64 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900" />
              </div>
              <button type="button" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300" :disabled="testForm.processing || !testForm.test_to" @click="sendTest">Enviar teste</button>
              <a :href="route('admin.layout.whatsapp.templates.preview', template.key)" target="_blank" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300">Visualizar</a>
            </div>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Preview (texto)</h3>
            <iframe :src="route('admin.layout.whatsapp.templates.preview', template.key)" class="w-full h-64 rounded-md border border-gray-200 dark:border-gray-700 bg-white"></iframe>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
