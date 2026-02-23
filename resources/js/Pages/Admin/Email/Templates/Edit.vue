<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  template: Object,
  variables: Object
})

const form = useForm({
  enabled: props.template.enabled,
  subject: props.template.subject || '',
  html: props.template.html || '',
  text: props.template.text || '',
})

const testForm = useForm({
  test_to: ''
})

const htmlRef = ref(null)
const setHtmlFromEditor = () => {
  form.html = htmlRef.value?.innerHTML || ''
}
watch(() => form.html, (v) => {
  if (htmlRef.value && htmlRef.value.innerHTML !== v) {
    htmlRef.value.innerHTML = v || ''
  }
})

const exec = (cmd, arg = null) => {
  document.execCommand(cmd, false, arg)
  setHtmlFromEditor()
}

const restoreDefault = () => {
  // Apenas limpa o conteúdo para padrão simples; um reset completo exigiria buscar padrão no backend
  form.subject = props.template.subject || ''
  form.html = props.template.html || ''
  form.text = props.template.text || ''
}

const submit = () => {
  form.post(route('admin.layout.email.templates.update', props.template.key), {
    preserveScroll: true
  })
}

const sendTest = () => {
  testForm.post(route('admin.layout.email.templates.test', props.template.key), {
    preserveScroll: true
  })
}
</script>

<template>
  <AdminLayout :title="`Editar Template: ${template.name}`">
    <Head :title="`Editar Template: ${template.name}`" />
    <div class="max-w-6xl mx-auto">
      <div class="mb-4">
        <Link :href="route('admin.layout.email.templates.index')" class="text-violet-600 hover:text-violet-800 text-sm">&larr; Voltar</Link>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-4">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Configuração</h2>
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" v-model="form.enabled" class="rounded border-gray-300 dark:border-gray-600"/>
                Ativo
              </label>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assunto</label>
              <input v-model="form.subject" type="text" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900" />
            </div>

            <div class="mb-2 text-sm text-gray-500 dark:text-gray-400">
              Variáveis disponíveis:
              <ul class="mt-1 flex flex-wrap gap-2">
                <li v-for="(group, gk) in variables" :key="gk" class="w-full">
                  <div class="font-medium text-gray-700 dark:text-gray-300">{{ gk }}</div>
                  <div class="flex flex-wrap gap-2 mt-1">
                    <span v-for="v in group" :key="v" class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700/40 text-gray-700 dark:text-gray-200 text-xs">{{ v }}</span>
                  </div>
                </li>
              </ul>
            </div>

            <div>
              <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Editor HTML</label>
                <div class="flex gap-2">
                  <button class="px-2 py-1 rounded border text-xs" @click.prevent="exec('bold')">Negrito</button>
                  <button class="px-2 py-1 rounded border text-xs" @click.prevent="exec('insertUnorderedList')">Lista</button>
                  <button class="px-2 py-1 rounded border text-xs" @click.prevent="exec('createLink', prompt('URL do link:') || '')">Link</button>
                  <button class="px-2 py-1 rounded border text-xs" @click.prevent="exec('insertImage', prompt('URL da imagem:') || '')">Imagem</button>
                  <button class="px-2 py-1 rounded border text-xs" @click.prevent="exec('removeFormat')">Limpar formatação</button>
                </div>
              </div>
              <div ref="htmlRef" contenteditable="true" class="min-h-[220px] rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-900 p-3 prose prose-sm max-w-none dark:prose-invert" @input="setHtmlFromEditor" v-html="form.html"></div>
              <div class="mt-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Texto puro (fallback)</label>
                <textarea v-model="form.text" rows="4" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900"></textarea>
              </div>
            </div>

            <div class="mt-4 flex items-center justify-end gap-2">
              <button type="button" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300" @click="restoreDefault">Restaurar padrão</button>
              <button type="button" class="px-3 py-2 rounded-md bg-violet-600 text-white hover:bg-violet-700 disabled:opacity-50" :disabled="form.processing" @click="submit">Salvar</button>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-6">
            <div class="flex items-center gap-3">
              <input v-model="testForm.test_to" type="email" placeholder="E-mail para teste" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900"/>
              <button type="button" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300" :disabled="testForm.processing || !testForm.test_to" @click="sendTest">Enviar teste</button>
              <a :href="route('admin.layout.email.templates.preview', template.key)" target="_blank" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300">Visualizar</a>
            </div>
          </div>
        </div>

        <div>
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Preview (estrutura padrão)</h3>
            <iframe :src="route('admin.layout.email.templates.preview', template.key)" class="w-full h-[700px] rounded-md border border-gray-200 dark:border-gray-700"></iframe>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

