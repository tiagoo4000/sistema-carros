<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { ArrowLeft, Upload } from 'lucide-vue-next';

const form = useForm({
    name: '',
    image: null,
});

const imagePreview = ref(null);

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('admin.categorias.store'));
};
</script>

<template>
    <Head title="Nova Categoria" />

    <AdminLayout>
        <div class="mb-8">
            <div class="mb-4">
                <Link :href="route('admin.categorias.index')" class="text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-2">
                    <ArrowLeft class="w-4 h-4" /> Voltar
                </Link>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nova Categoria</h1>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700/60 p-6 max-w-2xl">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Nome -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome da Categoria <span class="text-red-500">*</span></label>
                    <input 
                        v-model="form.name" 
                        type="text" 
                        class="form-input w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-violet-500 focus:ring-violet-500" 
                        placeholder="Ex: Veículos, Imóveis..."
                    >
                    <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                </div>

                <!-- Imagem -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Imagem de Capa <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500 mb-2">Recomendado: 800x600px (JPG, PNG, WEBP)</p>
                    
                    <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 dark:border-gray-600 px-6 py-10 relative hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="text-center">
                            
                            <!-- Preview -->
                            <div v-if="imagePreview" class="mb-4 relative group">
                                <img :src="imagePreview" class="mx-auto h-48 object-cover rounded-md shadow-sm" alt="Preview" />
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-md flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">Trocar imagem</span>
                                </div>
                            </div>

                            <!-- Placeholder Upload -->
                            <div v-else>
                                <Upload class="mx-auto h-12 w-12 text-gray-300" aria-hidden="true" />
                                <div class="mt-4 flex text-sm leading-6 text-gray-600 dark:text-gray-400 justify-center">
                                    <span class="relative cursor-pointer rounded-md font-semibold text-violet-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-violet-600 focus-within:ring-offset-2 hover:text-violet-500">
                                        <span>Upload de um arquivo</span>
                                    </span>
                                </div>
                            </div>
                            
                            <input 
                                id="file-upload" 
                                name="file-upload" 
                                type="file" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                accept="image/png, image/jpeg, image/webp"
                                @change="handleImageUpload"
                            >
                        </div>
                    </div>
                    <div v-if="form.errors.image" class="text-red-500 text-sm mt-1">{{ form.errors.image }}</div>
                </div>

                <!-- Botões -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <Link :href="route('admin.categorias.index')" class="btn border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300">
                        Cancelar
                    </Link>
                    <button 
                        type="submit" 
                        class="btn bg-violet-500 hover:bg-violet-600 text-white disabled:opacity-70 disabled:cursor-not-allowed"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Salvando...</span>
                        <span v-else>Salvar Categoria</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
