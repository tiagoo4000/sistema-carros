<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    leilao: Object,
    comitentes: Array
});

const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const offset = date.getTimezoneOffset() * 60000;
    return new Date(date.getTime() - offset).toISOString().slice(0, 16);
};

const form = useForm({
    titulo: props.leilao.titulo,
    descricao: props.leilao.descricao,
    local: props.leilao.local,
    data_inicio: formatDateForInput(props.leilao.data_inicio),
    data_fim: formatDateForInput(props.leilao.data_fim),
    status: props.leilao.status,
    tipo: props.leilao.tipo ?? 'leilao',
    comitente_id: props.leilao.comitente_id,
});

const submit = () => {
    form.put(route('admin.leiloes.update', props.leilao.id));
};
</script>

<template>
    <Head title="Editar Leilão" />

    <AdminLayout>
        <div class="max-w-3xl mx-auto">
            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Editar Leilão: {{ leilao.titulo }}
                    </h2>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="titulo" class="block text-sm font-medium text-slate-700">Título</label>
                            <div class="mt-1">
                                <input v-model="form.titulo" type="text" name="titulo" id="titulo" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                            </div>
                            <div v-if="form.errors.titulo" class="mt-1 text-sm text-red-600">{{ form.errors.titulo }}</div>
                        </div>

                        <div>
                            <label for="descricao" class="block text-sm font-medium text-slate-700">Descrição</label>
                            <div class="mt-1">
                                <textarea v-model="form.descricao" id="descricao" name="descricao" rows="3" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"></textarea>
                            </div>
                        </div>

                        <div>
                            <label for="local" class="block text-sm font-medium text-slate-700">Local (Físico ou Online)</label>
                            <div class="mt-1">
                                <input v-model="form.local" type="text" name="local" id="local" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                            </div>
                        </div>

                        <div>
                            <label for="comitente_id" class="block text-sm font-medium text-slate-700">Comitente (Organizador)</label>
                            <div class="mt-1">
                                <select v-model="form.comitente_id" id="comitente_id" name="comitente_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <option value="">Selecione um comitente...</option>
                                    <option v-for="comitente in comitentes" :key="comitente.id" :value="comitente.id">
                                        {{ comitente.nome }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="form.errors.comitente_id" class="mt-1 text-sm text-red-600">{{ form.errors.comitente_id }}</div>
                        </div>

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="data_inicio" class="block text-sm font-medium text-slate-700">Data de Início</label>
                                <div class="mt-1">
                                    <input v-model="form.data_inicio" type="datetime-local" name="data_inicio" id="data_inicio" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                                </div>
                            </div>

                            <div>
                                <label for="data_fim" class="block text-sm font-medium text-slate-700">Data de Fim</label>
                                <div class="mt-1">
                                    <input v-model="form.data_fim" type="datetime-local" name="data_fim" id="data_fim" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                                <div class="mt-1">
                                    <select v-model="form.status" id="status" name="status" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                        <option value="ativo">Ativo</option>
                                        <option value="finalizado">Finalizado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-slate-700">Tipo do Evento</label>
                                <div class="mt-1">
                                    <select v-model="form.tipo" id="tipo" name="tipo" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                        <option value="leilao">Leilão</option>
                                        <option value="venda_direta">Venda Direta</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <Link :href="route('admin.leiloes.index')" class="rounded-md border border-slate-300 bg-white py-2 px-4 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">Cancelar</Link>
                            <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
