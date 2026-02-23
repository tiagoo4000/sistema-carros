<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Plus, Edit, Trash2 } from 'lucide-vue-next';
import ActionButton from '@/Components/Admin/ActionButton.vue';

const props = defineProps({
    comitentes: Array
});

const deleteComitente = (id) => {
    if (confirm('Tem certeza que deseja excluir este comitente?')) {
        router.delete(route('admin.comitentes.destroy', id));
    }
};
</script>

<template>
    <Head title="Gerenciar Comitentes" />

    <AdminLayout>
        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Comitentes</h1>
                <p class="mt-2 text-sm text-slate-700">
                    Gerencie os comitentes e suas logomarcas.
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <Link :href="route('admin.comitentes.create')" class="inline-flex items-center justify-center rounded-md border border-transparent bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 sm:w-auto">
                    <Plus class="mr-2 h-4 w-4" />
                    Novo Comitente
                </Link>
            </div>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Logo</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nome</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Ações</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="comitente in comitentes" :key="comitente.id">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                <img v-if="comitente.imagem" :src="`/storage/${comitente.imagem}`" :alt="comitente.nome" class="h-10 w-10 object-contain rounded-full bg-gray-50" />
                                <div v-else class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-xs">Sem img</div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ comitente.nome }}</td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                    <ActionButton :to="route('admin.comitentes.edit', comitente.id)" label="Editar" size="sm" variant="primary">
                                        <Edit class="w-4 h-4" />
                                    </ActionButton>
                                    <ActionButton label="Excluir" size="sm" variant="danger" @click="deleteComitente(comitente.id)">
                                        <Trash2 class="w-4 h-4" />
                                    </ActionButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="comitentes.length === 0">
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                Nenhum comitente cadastrado.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
