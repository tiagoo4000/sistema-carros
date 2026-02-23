<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineProps({
    leiloes: Object
});

const formatDate = (date) => {
    return new Intl.DateTimeFormat('pt-BR').format(new Date(date));
};
</script>

<template>
    <Head title="Leilões" />

    <SiteLayout>
        <div class="bg-white">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Todos os Leilões</h2>
                    <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                        Encontre o lote perfeito para você.
                    </p>
                </div>

                <div class="mt-12 grid gap-5 max-w-lg mx-auto lg:grid-cols-3 lg:max-w-none">
                    <div v-for="leilao in leiloes.data" :key="leilao.id" class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                        <div class="flex-shrink-0">
                            <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">Imagem Indisponível</span>
                            </div>
                        </div>
                        <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-indigo-600">
                                    {{ new Date(leilao.data_inicio) > new Date() ? 'Loteando' : (new Date(leilao.data_fim) > new Date() ? 'Aberto' : 'Encerrado') }}
                                </p>
                                <Link :href="route('leiloes.show', leilao.id)" class="block mt-2">
                                    <p class="text-xl font-semibold text-gray-900">
                                        {{ leilao.titulo }}
                                    </p>
                                    <p class="mt-3 text-base text-gray-500">
                                        {{ leilao.descricao }}
                                    </p>
                                </Link>
                            </div>
                            <div class="mt-6 flex items-center">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ leilao.local }}
                                    </p>
                                    <div class="flex space-x-1 text-sm text-gray-500">
                                        <time :datetime="leilao.data_inicio">
                                            {{ formatDate(leilao.data_inicio) }}
                                        </time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>
