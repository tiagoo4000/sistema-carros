<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Plus, Edit, Trash2 } from 'lucide-vue-next';
import ActionButton from '@/Components/Admin/ActionButton.vue';

defineProps({
    categories: Array,
});

const deleteCategory = (id) => {
    if (confirm('Tem certeza que deseja excluir esta categoria?')) {
        router.delete(route('admin.categorias.destroy', id));
    }
};
</script>

<template>
    <Head title="Categorias" />

    <AdminLayout>
        <div class="mb-8 sm:flex sm:justify-between sm:items-center">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Categorias</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <Link :href="route('admin.categorias.create')" class="btn bg-violet-500 hover:bg-violet-600 text-white">
                    <Plus class="w-4 h-4 fill-current opacity-50 shrink-0" />
                    <span class="hidden xs:block ml-2">Nova Categoria</span>
                </Link>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700/60 sm:rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Imagem
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nome
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="category in categories" :key="category.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="h-12 w-20 rounded bg-gray-100 dark:bg-gray-700 overflow-hidden border border-gray-200 dark:border-gray-600">
                                    <img 
                                        :src="`/storage/${category.image_path}`" 
                                        :alt="category.name" 
                                        class="h-full w-full object-cover"
                                    >
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ category.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    Ativo
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                    <ActionButton :to="route('admin.categorias.edit', category.id)" label="Editar" size="sm" variant="primary">
                                        <Edit class="w-4 h-4" />
                                    </ActionButton>
                                    <ActionButton label="Excluir" size="sm" variant="danger" @click="deleteCategory(category.id)">
                                        <Trash2 class="w-4 h-4" />
                                    </ActionButton>
                                </div>
                            </td>
                        </tr>
                        
                        <tr v-if="categories.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-4 mb-3">
                                        <Plus class="w-6 h-6 text-gray-400" />
                                    </div>
                                    <p class="text-base font-medium">Nenhuma categoria encontrada</p>
                                    <p class="text-sm mt-1 mb-4">Comece criando sua primeira categoria.</p>
                                    <Link :href="route('admin.categorias.create')" class="btn bg-violet-500 hover:bg-violet-600 text-white btn-sm">
                                        Nova Categoria
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
