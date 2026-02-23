<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  config: Object
})

const form = useForm({
  mail_driver: props.config?.mail_driver || 'smtp',
  mail_host: props.config?.mail_host || '',
  mail_port: props.config?.mail_port || '',
  mail_username: props.config?.mail_username || '',
  mail_password: '',
  mail_encryption: props.config?.mail_encryption || 'tls',
  mail_from_name: props.config?.mail_from_name || '',
  mail_from_address: props.config?.mail_from_address || '',
  mail_reply_to: props.config?.mail_reply_to || '',
  mail_enabled: props.config?.mail_enabled === '1' || false,
})

const submit = () => {
  form.post(route('admin.email.update'), {
    preserveScroll: true,
    onSuccess: () => {},
  })
}

const testForm = useForm({
  test_to: ''
})
const submitTest = () => {
  testForm.post(route('admin.email.test'), {
    preserveScroll: true
  })
}
</script>

<template>
  <AdminLayout title="Configurações de E-mail">
    <Head title="Configurações de E-mail" />
    <div class="max-w-3xl mx-auto">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-6">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">Configurações de E-mail</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Defina aqui as credenciais que serão usadas para envio de e-mails transacionais.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Driver</label>
            <select v-model="form.mail_driver" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900">
              <option value="smtp">SMTP</option>
              <option value="sendmail">Sendmail</option>
              <option value="log">Log</option>
              <option value="mailgun">Mailgun</option>
              <option value="ses">SES</option>
              <option value="postmark">Postmark</option>
            </select>
          </div>
          <div class="flex items-end">
            <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
              <input type="checkbox" v-model="form.mail_enabled" class="rounded border-gray-300 dark:border-gray-600" />
              Ativar envio de e-mails
            </label>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Host</label>
            <input v-model="form.mail_host" type="text" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Porta</label>
            <input v-model="form.mail_port" type="number" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuário</label>
            <input v-model="form.mail_username" type="text" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senha</label>
            <input v-model="form.mail_password" type="password" placeholder="Deixe em branco para manter" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Criptografia</label>
            <select v-model="form.mail_encryption" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900">
              <option value="tls">TLS</option>
              <option value="ssl">SSL</option>
              <option value="null">Nenhuma</option>
            </select>
          </div>
          <div></div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remetente (Nome)</label>
            <input v-model="form.mail_from_name" type="text" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remetente (E-mail)</label>
            <input v-model="form.mail_from_address" type="email" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Responder para</label>
            <input v-model="form.mail_reply_to" type="email" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900" />
          </div>
        </div>

        <div class="mt-6 flex items-center justify-between gap-3 flex-wrap">
          <div class="flex items-end gap-2">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail para teste</label>
              <input v-model="testForm.test_to" type="email" placeholder="seu@email.com" class="w-64 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900" />
            </div>
            <button type="button" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30" :disabled="testForm.processing || !testForm.test_to" @click="submitTest">
              Enviar teste
            </button>
          </div>
          <button type="button" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30" @click="form.reset()">Cancelar</button>
          <button type="button" class="px-4 py-2 rounded-md bg-violet-600 text-white hover:bg-violet-700 disabled:opacity-50" :disabled="form.processing" @click="submit">
            Salvar
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
  </template>
