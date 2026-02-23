<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { LayoutTemplate, Check } from 'lucide-vue-next';

defineProps({
    currentLayout: String
});
</script>

<template>
    <Head title="Configuração de Layout" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Layout do Site</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Visualização do tema atual do sistema.
                        </p>
                    </div>
                </div>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="shadow sm:rounded-md sm:overflow-hidden">
                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                            
                            <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <LayoutTemplate class="h-5 w-5 text-indigo-400" aria-hidden="true" />
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-indigo-700">
                                            O sistema está configurado para utilizar o <strong>Template Carro</strong> como layout padrão definitivo.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="relative flex flex-col p-4 border-2 border-indigo-600 bg-indigo-50 rounded-lg">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-medium text-gray-900">Layout Carro (Ativo)</h4>
                                    <Check class="w-5 h-5 text-indigo-600" />
                                </div>
                                <div class="flex-1 bg-white rounded border border-gray-200 p-4 flex items-center justify-center aspect-video">
                                    <span class="text-sm text-gray-500 font-medium">Layout Fixo</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
