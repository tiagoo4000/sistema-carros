<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';

const props = defineProps({
    leilao: Object
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const formatDate = (date) => {
    return new Intl.DateTimeFormat('pt-BR', { 
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    }).format(new Date(date));
};
</script>

<template>
    <Head :title="leilao.titulo" />

    <SiteLayout>
        <div class="bg-gray-50 min-h-screen">
            <!-- Header do Leilão -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="lg:text-center">
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 mb-2">
                            {{ leilao.status.toUpperCase() }}
                        </span>
                        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl mb-2">
                            {{ leilao.titulo }}
                        </h2>
                        <p class="max-w-2xl text-lg text-gray-500 lg:mx-auto mb-4">
                            {{ leilao.descricao }}
                        </p>
                        <div class="flex flex-wrap justify-center gap-4 text-sm text-gray-500">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                                Início: {{ formatDate(leilao.data_inicio) }}
                            </div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Fim: {{ formatDate(leilao.data_fim) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Lotes (Grid Compacto) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Lotes do Leilão ({{ leilao.lotes.length }})</h3>
                    
                    <!-- Filtros/Ordenação (Placeholder) -->
                    <div class="flex space-x-2">
                        <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option>Ordenar por: Lote</option>
                            <option>Maior Valor</option>
                            <option>Menor Valor</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div v-for="lote in leilao.lotes" :key="lote.id" class="group bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden border border-gray-200 flex flex-col">
                        <!-- Link Principal (Envolve o card ou parte dele) -->
                        <Link :href="route('lotes.show', lote.id)" class="block relative">
                            <!-- Imagem do Lote -->
                            <div class="aspect-w-16 aspect-h-10 bg-gray-200 w-full h-48 flex items-center justify-center relative overflow-hidden">
                                <!-- Imagem ou Placeholder -->
                                <img v-if="lote.foto_capa" :src="`/storage/${lote.foto_capa}`" :alt="lote.titulo" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
                                <span v-else class="text-gray-400 font-medium">Sem Foto</span>
                                
                                <!-- Badge Status -->
                                <div class="absolute top-2 left-2">
                                    <span :class="{
                                        'bg-green-500': lote.status === 'aberto',
                                        'bg-red-500': lote.status === 'arrematado',
                                        'bg-gray-500': lote.status === 'sem_lances'
                                    }" class="text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm uppercase tracking-wider">
                                        {{ lote.status }}
                                    </span>
                                </div>

                                <!-- Badge Tipo Retomada -->
                                <div v-if="lote.tipo_retomada" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2 pt-8">
                                    <span class="text-white text-xs font-medium truncate block">
                                        {{ lote.tipo_retomada }}
                                    </span>
                                </div>

                                <!-- Botão Favoritar (Absoluto) -->
                                <button class="absolute top-2 right-2 p-1.5 bg-white/80 rounded-full hover:bg-white text-gray-400 hover:text-red-500 transition-colors z-10" @click.prevent>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </Link>

                        <!-- Conteúdo do Card -->
                        <div class="p-4 flex-1 flex flex-col">
                            <!-- Título -->
                            <div class="mb-2">
                                <Link :href="route('lotes.show', lote.id)" class="text-sm font-bold text-gray-900 hover:text-indigo-600 line-clamp-2 min-h-[40px]">
                                    Lote {{ lote.ordem }} - {{ lote.titulo }}
                                </Link>
                            </div>

                            <!-- Infos Rápidas -->
                            <div class="flex items-center space-x-4 text-xs text-gray-500 mb-3">
                                <div v-if="lote.ano" class="flex items-center">
                                    <span class="font-medium">{{ lote.ano }}</span>
                                </div>
                                <div v-if="lote.quilometragem" class="flex items-center">
                                    <span>{{ lote.quilometragem }} km</span>
                                </div>
                            </div>

                            <!-- Ícones (Media) -->
                            <div class="flex items-center space-x-2 mb-4 border-b border-gray-100 pb-3">
                                <div class="flex items-center text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                    <span>5</span> <!-- Placeholder count -->
                                </div>
                            </div>

                            <!-- Valores e Ação -->
                            <div class="mt-auto">
                                <div class="mb-3">
                                    <p class="text-xs text-gray-500">Valor Atual</p>
                                    <p class="text-lg font-bold text-indigo-600">
                                        {{ lote.maior_lance ? formatCurrency(lote.maior_lance.valor) : formatCurrency(lote.valor_inicial) }}
                                    </p>
                                </div>
                                
                                <Link 
                                    :href="route('lotes.show', lote.id)"
                                    class="block w-full text-center py-2 px-4 border border-indigo-600 rounded text-sm font-medium text-indigo-600 hover:bg-indigo-50 transition-colors"
                                >
                                    Ver Detalhes
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>
