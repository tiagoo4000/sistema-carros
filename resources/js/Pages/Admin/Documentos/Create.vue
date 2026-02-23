<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    usuarios: Array
});

const form = useForm({
    usuario_id: '',
    tipo: 'rg',
    arquivo: null,
    observacoes: ''
});

const submit = () => {
    form.post(route('admin.documentos.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Novo Documento" />

    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
            
            <!-- Page header -->
            <div class="mb-8">
                <Link :href="route('admin.documentos.index')" class="text-sm font-medium text-violet-500 hover:text-violet-600">&lt;- Voltar</Link>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold mt-2">Enviar Documento</h1>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
                <form @submit.prevent="submit">
                    <div class="space-y-4">
                        
                        <!-- Usuário -->
                        <div>
                            <label class="block text-sm font-medium mb-1" for="usuario">Usuário <span class="text-rose-500">*</span></label>
                            <select id="usuario" v-model="form.usuario_id" class="form-select w-full" required>
                                <option value="" disabled>Selecione um usuário</option>
                                <option v-for="user in usuarios" :key="user.id" :value="user.id">
                                    {{ user.nome }} ({{ user.email }})
                                </option>
                            </select>
                            <div v-if="form.errors.usuario_id" class="text-xs mt-1 text-rose-500">{{ form.errors.usuario_id }}</div>
                        </div>

                        <!-- Tipo -->
                        <div>
                            <label class="block text-sm font-medium mb-1" for="tipo">Tipo de Documento <span class="text-rose-500">*</span></label>
                            <select id="tipo" v-model="form.tipo" class="form-select w-full" required>
                                <option value="rg">RG</option>
                                <option value="cpf">CPF</option>
                                <option value="comprovante_residencia">Comprovante de Residência</option>
                                <option value="outros">Outros</option>
                            </select>
                            <div v-if="form.errors.tipo" class="text-xs mt-1 text-rose-500">{{ form.errors.tipo }}</div>
                        </div>

                        <!-- Arquivo -->
                        <div>
                            <label class="block text-sm font-medium mb-1" for="arquivo">Arquivo <span class="text-rose-500">*</span></label>
                            <input id="arquivo" type="file" @input="form.arquivo = $event.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100" required />
                            <div v-if="form.errors.arquivo" class="text-xs mt-1 text-rose-500">{{ form.errors.arquivo }}</div>
                        </div>

                        <!-- Observações -->
                        <div>
                            <label class="block text-sm font-medium mb-1" for="observacoes">Observações</label>
                            <textarea id="observacoes" v-model="form.observacoes" class="form-textarea w-full" rows="3"></textarea>
                            <div v-if="form.errors.observacoes" class="text-xs mt-1 text-rose-500">{{ form.errors.observacoes }}</div>
                        </div>

                    </div>

                    <div class="flex justify-end mt-6">
                        <Link :href="route('admin.documentos.index')" class="btn border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300 mr-3">Cancelar</Link>
                        <button class="btn bg-violet-500 hover:bg-violet-600 text-white" :disabled="form.processing">Enviar Documento</button>
                    </div>
                </form>
            </div>

        </div>
    </AdminLayout>
</template>
