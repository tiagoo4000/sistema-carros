<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Plus, Edit, Trash2, ExternalLink } from 'lucide-vue-next';
import ActionButton from '@/Components/Admin/ActionButton.vue';

const props = defineProps({
    banners: Array
});

const deleteBanner = (id) => {
    if (confirm('Tem certeza que deseja excluir este banner?')) {
        router.delete(route('admin.layout.banners.destroy', id));
    }
};

const getTypeLabel = (type) => {
    const types = {
        hero: 'Topo (Hero)',
        middle: 'Meio do Site',
        footer: 'Rodapé'
    };
    return types[type] || type;
};
</script>

<template>
    <Head title="Gerenciar Banners" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Banners do Site</h1>
                    <p class="text-gray-600 mt-1">Gerencie os banners exibidos nas diferentes áreas do site.</p>
                </div>
                <Link 
                    :href="route('admin.layout.banners.create')" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <Plus class="w-4 h-4 mr-2" />
                    Novo Banner
                </Link>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Imagem
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Informações
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Localização
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="banner in banners" :key="banner.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="h-16 w-32 rounded bg-gray-100 overflow-hidden border border-gray-200">
                                    <img :src="banner.image_path" class="h-full w-full object-cover" alt="Banner">
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ banner.title || 'Sem título' }}</div>
                                <div v-if="banner.link" class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                    <ExternalLink class="w-3 h-3" />
                                    {{ banner.link }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ getTypeLabel(banner.type) }}
                                </span>
                                <div class="text-xs text-gray-400 mt-1">Ordem: {{ banner.order }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="banner.status === 'ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                >
                                    {{ banner.status === 'ativo' ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                    <ActionButton :to="route('admin.layout.banners.edit', banner.id)" label="Editar" size="sm" variant="primary">
                                        <Edit class="w-4 h-4" />
                                    </ActionButton>
                                    <ActionButton label="Excluir" size="sm" variant="danger" @click="deleteBanner(banner.id)">
                                        <Trash2 class="w-4 h-4" />
                                    </ActionButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="banners.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm">
                                Nenhum banner cadastrado.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
