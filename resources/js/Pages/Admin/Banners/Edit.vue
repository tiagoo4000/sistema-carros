<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeft, Save, Image as ImageIcon } from 'lucide-vue-next';

const props = defineProps({
    banner: Object
});

const form = useForm({
    title: props.banner.title || '',
    link: props.banner.link || '',
    type: props.banner.type,
    status: props.banner.status,
    order: props.banner.order,
    image: null,
    _method: 'PUT' // Necessary for file upload in Inertia with PUT/PATCH
});

const submit = () => {
    form.post(route('admin.layout.banners.update', props.banner.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Editar Banner" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto py-8">
            <div class="flex items-center gap-4 mb-8">
                <Link :href="route('admin.layout.banners.index')" class="p-2 rounded-full hover:bg-gray-100 text-gray-500">
                    <ArrowLeft class="w-6 h-6" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Banner</h1>
                    <p class="text-gray-600 mt-1">Atualize as informações do banner.</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Imagem -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Imagem do Banner</label>
                        
                        <div class="mb-4 bg-gray-100 rounded-lg p-2 border border-gray-200 inline-block">
                            <img :src="banner.image_path" class="h-32 w-auto object-cover rounded" alt="Atual">
                        </div>

                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-400 transition-colors bg-gray-50">
                            <div class="space-y-1 text-center">
                                <div v-if="form.image" class="mb-4">
                                    <p class="text-sm text-green-600 font-medium flex items-center justify-center gap-2">
                                        <ImageIcon class="w-4 h-4" />
                                        Nova imagem selecionada: {{ form.image.name }}
                                    </p>
                                </div>
                                <ImageIcon v-else class="mx-auto h-12 w-12 text-gray-400" />
                                
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 px-2">
                                        <span>Alterar imagem (Opcional)</span>
                                        <input id="file-upload" name="file-upload" type="file" class="sr-only" @input="form.image = $event.target.files[0]" accept="image/*">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF até 5MB</p>
                            </div>
                        </div>
                        <div v-if="form.errors.image" class="text-red-500 text-sm mt-1">{{ form.errors.image }}</div>
                    </div>

                    <!-- Configurações -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Localização *</label>
                        <select v-model="form.type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="hero">Topo (Hero)</option>
                            <option value="middle">Meio do Site</option>
                            <option value="footer">Rodapé</option>
                        </select>
                        <div v-if="form.errors.type" class="text-red-500 text-sm mt-1">{{ form.errors.type }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select v-model="form.status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                        <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título (Opcional)</label>
                        <input 
                            v-model="form.title"
                            type="text" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Texto descritivo do banner"
                        >
                        <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link de Redirecionamento (Opcional)</label>
                        <input 
                            v-model="form.link"
                            type="url" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="https://..."
                        >
                        <div v-if="form.errors.link" class="text-red-500 text-sm mt-1">{{ form.errors.link }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ordem de Exibição</label>
                        <input 
                            v-model="form.order"
                            type="number" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        <p class="text-xs text-gray-500 mt-1">Menor número aparece primeiro.</p>
                        <div v-if="form.errors.order" class="text-red-500 text-sm mt-1">{{ form.errors.order }}</div>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-colors"
                    >
                        <Save class="w-4 h-4 mr-2" />
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
