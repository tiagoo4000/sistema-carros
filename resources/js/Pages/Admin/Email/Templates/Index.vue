<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  templates: Array,
  missing: Boolean
})
</script>

<template>
  <AdminLayout title="Templates de E-mail">
    <Head title="Templates de E-mail" />
    <div class="max-w-5xl mx-auto">
      <div v-if="missing" class="mb-4 p-4 rounded-lg border border-amber-300 bg-amber-50 text-amber-800 dark:bg-amber-900/20 dark:border-amber-700 dark:text-amber-200">
        <div class="font-semibold mb-1">É necessário executar as migrations.</div>
        <div class="text-sm">
          Rode as migrations para criar a tabela <strong>email_templates</strong>:
          <div class="mt-2 font-mono text-xs bg-white/70 dark:bg-gray-800/60 px-3 py-2 rounded border border-amber-200 dark:border-amber-700">
            php artisan migrate
          </div>
          Após isso, recarregue esta página.
        </div>
      </div>
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Templates de E-mail</h2>
        <div class="text-sm text-gray-500 dark:text-gray-400">
          Defina o conteúdo das mensagens automáticas do sistema.
        </div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900/30">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última atualização</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="t in templates" :key="t.id">
              <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ t.name }}
              </td>
              <td class="px-6 py-4">
                <span :class="t.enabled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700/40 dark:text-gray-300'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ t.enabled ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                {{ new Date(t.updated_at).toLocaleString() }}
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex gap-3 justify-end">
                  <Link :href="route('admin.layout.email.templates.edit', t.key)" class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    Editar
                  </Link>
                  <a :href="route('admin.layout.email.templates.preview', t.key)" target="_blank" class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    Visualizar
                  </a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>
