<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Form from './Form.vue';

const props = defineProps({
  conta: Object
});

const submit = (form) => {
  form.put(route('admin.contas.update', props.conta.id));
};
</script>

<template>
  <Head title="Editar Conta Bancária" />
  <AdminLayout>
    <div class="max-w-3xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Editar Conta Bancária</h1>
        <Link :href="route('admin.contas.index')" class="text-sm text-gray-600 hover:text-gray-900">Voltar</Link>
      </div>
      <div class="bg-white shadow rounded-lg p-6">
        <Form :initial="conta" v-slot="{ form }">
          <div class="mt-4 flex justify-end">
            <button @click="submit(form)" type="button" class="inline-flex items-center px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
              Atualizar
            </button>
          </div>
        </Form>
      </div>
    </div>
  </AdminLayout>
</template>

