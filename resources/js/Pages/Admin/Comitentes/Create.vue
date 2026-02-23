<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    nome: '',
    imagem: null,
});

const submit = () => {
    form.post(route('admin.comitentes.store'));
};

const handleFileChange = (e) => {
    form.imagem = e.target.files[0];
};
</script>

<template>
    <Head title="Novo Comitente" />

    <AdminLayout>
        <div class="max-w-3xl mx-auto">
            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Novo Comitente
                    </h2>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <Link :href="route('admin.comitentes.index')" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-md bg-white hover:bg-gray-50 text-gray-900 border border-gray-300">
                        <ArrowLeft class="mr-1 h-4 w-4" /> Voltar
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
                            <div class="mt-1">
                                <input type="file" @change="handleFileChange" id="imagem" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" />
                            </div>
                            <div v-if="form.errors.imagem" class="mt-1 text-sm text-red-600">{{ form.errors.imagem }}</div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="form.processing" class="inline-flex justify-center rounded-md border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
