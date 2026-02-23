<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { FileText, Check, X, Plus, Search } from 'lucide-vue-next';

const props = defineProps({
    documentos: Object
});

const form = useForm({});

const validarDocumento = (id) => {
    if (confirm('Tem certeza que deseja validar este documento?')) {
        form.post(route('admin.documentos.validar', id));
    }
};

const rejeitarDocumento = (id) => {
    const motivo = prompt('Motivo da rejeição (opcional):');
    form.post(route('admin.documentos.rejeitar', id), { data: { motivo } });
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('pt-BR');
};
</script>

<template>
    <Head title="Gerenciar Documentos" />

    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
            
            <!-- Page header -->
            <div class="sm:flex sm:justify-between sm:items-center mb-8">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Documentos</h1>
                </div>
                <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                    <!-- Add button -->
                    <Link :href="route('admin.documentos.create')" class="btn bg-violet-500 hover:bg-violet-600 text-white">
                        <Plus class="w-4 h-4 fill-current opacity-50 shrink-0" />
                        <span class="hidden xs:block ml-2">Enviar Documento</span>
                    </Link>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl relative border border-gray-100 dark:border-gray-700/60">
                <div class="overflow-x-auto">
                    <table class="table-auto w-full text-left">
                        <!-- Table header -->
                        <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700/60">
                            <tr>
                                <th class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-left">Usuário</div>
                                </th>
                                <th class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-left">Tipo</div>
                                </th>
                                <th class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-left">Arquivo</div>
                                </th>
                                <th class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-left">Data Envio</div>
                                </th>
                                <th class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-left">Status</div>
                                </th>
                                <th class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-right">Ações</div>
                                </th>
                            </tr>
                        </thead>
                        <!-- Table body -->
                        <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                            <tr v-for="doc in documentos.data" :key="doc.id">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ doc.usuario.nome }}</div>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ doc.usuario.email }}</div>
                                    <div class="text-xs text-gray-500">CPF: {{ doc.usuario.cpf || '-' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-left font-medium text-gray-800 dark:text-gray-100 capitalize">{{ doc.tipo.replace('_', ' ') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a :href="route('admin.documentos.download', doc.id)" target="_blank" class="text-violet-500 hover:text-violet-600 flex items-center gap-1">
                                        <FileText class="w-4 h-4" />
                                        Visualizar
                                    </a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-left">{{ formatDate(doc.created_at) }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div v-if="doc.validado" class="inline-flex font-medium rounded-full text-center px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-400/30 text-emerald-600 dark:text-emerald-400">
                                        Validado
                                    </div>
                                    <div v-else-if="!doc.validado && doc.observacoes" class="inline-flex font-medium rounded-full text-center px-2.5 py-0.5 bg-red-100 dark:bg-red-400/30 text-red-600 dark:text-red-400">
                                        Rejeitado
                                    </div>
                                    <div v-else class="inline-flex font-medium rounded-full text-center px-2.5 py-0.5 bg-amber-100 dark:bg-amber-400/30 text-amber-600 dark:text-amber-400">
                                        Pendente
                                    </div>
                                    <div v-if="!doc.validado && doc.observacoes" class="text-xs text-red-600 mt-1">
                                        Motivo: {{ doc.observacoes }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <button v-if="!doc.validado" @click="validarDocumento(doc.id)" class="text-emerald-500 hover:text-emerald-600 font-medium text-sm flex items-center justify-end gap-1 ml-auto">
                                        <Check class="w-4 h-4" /> Validar
                                    </button>
                                    <button v-if="!doc.validado" @click="rejeitarDocumento(doc.id)" class="ml-3 text-red-500 hover:text-red-600 font-medium text-sm">
                                        Rejeitar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="documentos.data.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Nenhum documento encontrado.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination (if needed) -->
            <!-- Add pagination component here later if needed -->
        </div>
    </AdminLayout>
</template>
