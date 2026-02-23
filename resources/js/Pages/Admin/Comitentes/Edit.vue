<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    comitente: Object
});

const form = useForm({
    _method: 'PUT',
    nome: props.comitente.nome,
    imagem: null,
});

const submit = () => {
    form.post(route('admin.comitentes.update', props.comitente.id));
};

const handleFileChange = (e) => {
    form.imagem = e.target.files[0];
};
</script>

<template>
    <Head title="Editar Comitente" />

    <AdminLayout>
        <div class="max-w-3xl mx-auto">
            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Editar Comitente
                    </h2>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <Link :href="route('admin.comitentes.index')" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <ArrowLeft class="mr-2 h-4 w-4" /> Voltar
                    </Link>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-slate-700">Nome</label>
                            <div class="mt-1">
                                <input type="text" v-model="form.nome" id="nome" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                            </div>
                            <div v-if="form.errors.nome" class="mt-1 text-sm text-red-600">{{ form.errors.nome }}</div>
                        </div>

                        <div>
                            <label for="imagem" class="block text-sm font-medium text-slate-700">Logomarca</label>
                            <div v-if="comitente.imagem" class="mb-2">
                                <img :src="`/storage/${comitente.imagem}`" alt="Logo Atual" class="h-20 w-auto object-contain border rounded p-1" />
                            </div>
                            <div class="mt-1">
                                <input type="file" @change="handleFileChange" id="imagem" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" />
                            </div>
                            <div v-if="form.errors.imagem" class="mt-1 text-sm text-red-600">{{ form.errors.imagem }}</div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="form.processing" class="inline-flex justify-center rounded-md border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
