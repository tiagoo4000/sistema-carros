<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Edit, Ban, CheckCircle, Phone } from 'lucide-vue-next';

defineProps({
    usuarios: Object
});
</script>

<template>
    <Head title="Gerenciar Usuários" />

    <AdminLayout>
        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Usuários</h1>
                <p class="mt-2 text-sm text-slate-700">Gestão de usuários registrados no sistema.</p>
            </div>
        </div>

        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Nome/Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">CPF/Telefone</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">Documentos</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <tr v-for="user in usuarios.data" :key="user.id">
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm font-medium text-slate-900">{{ user.nome }}</div>
                            <div class="text-sm text-slate-500">{{ user.email }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-slate-900">{{ user.cpf || '--' }}</div>
                            <div class="text-sm text-slate-500">{{ user.telefone || '--' }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span v-if="user.bloqueado" class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">
                                Bloqueado
                            </span>
                            <span v-else class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                Ativo
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span v-if="user.documentos_validos" class="inline-flex items-center text-sm text-green-600">
                                <CheckCircle class="mr-1.5 h-4 w-4" /> Validado
                            </span>
                            <span v-else class="inline-flex items-center text-sm text-amber-600">
                                <Ban class="mr-1.5 h-4 w-4" /> Pendente
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                <a v-if="user.telefone" :href="`https://wa.me/${user.telefone.replace(/\\D/g,'')}`" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-md bg-emerald-600 hover:bg-emerald-700 text-white border border-emerald-600">
                                    <Phone class="h-4 w-4" />
                                    WhatsApp
                                </a>
                                <Link :href="route('admin.usuarios.edit', user.id)" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-md bg-white hover:bg-gray-50 text-gray-700 border border-gray-300">
                                    <Edit class="h-4 w-4" />
                                    Editar
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
