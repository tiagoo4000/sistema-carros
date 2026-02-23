<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import { X, Clock, MapPin, Gavel } from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    loteId: Number,
});

const emit = defineEmits(['close']);

const loading = ref(false);
const lances = ref([]);
const total = ref(0);
const error = ref(null);

const fetchHistory = async () => {
    if (!props.loteId) return;
    
    loading.value = true;
    error.value = null;
    
    try {
        const response = await axios.get(route('lotes.history', props.loteId));
        lances.value = response.data.lances;
        total.value = response.data.total;
    } catch (err) {
        console.error('Erro ao buscar histórico:', err);
        error.value = 'Não foi possível carregar o histórico de lances.';
    } finally {
        loading.value = false;
    }
};

watch(() => props.show, (newVal) => {
    if (newVal) {
        fetchHistory();
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

const close = () => {
    emit('close');
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
};
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="close"></div>

                <!-- Modal Panel -->
                <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Histórico de Lances</h3>
                            <p class="text-sm text-gray-500 mt-0.5" v-if="!loading">{{ total }} lances recebidos</p>
                        </div>
                        <button @click="close" class="p-2 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="flex-1 overflow-y-auto p-0 scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent">
                        <!-- Loading -->
                        <div v-if="loading" class="p-8 space-y-4">
                            <div v-for="i in 5" :key="i" class="flex items-center gap-4 animate-pulse">
                                <div class="w-10 h-10 rounded-full bg-gray-100"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-4 bg-gray-100 rounded w-1/3"></div>
                                    <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                                </div>
                                <div class="h-6 bg-gray-100 rounded w-20"></div>
                            </div>
                        </div>

                        <!-- Error -->
                        <div v-else-if="error" class="p-8 text-center text-red-500">
                            <p>{{ error }}</p>
                        </div>

                        <!-- Empty State -->
                        <div v-else-if="lances.length === 0" class="p-12 text-center flex flex-col items-center justify-center text-gray-400">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <Gavel class="w-8 h-8 opacity-50" />
                            </div>
                            <p class="font-medium text-gray-600">Nenhum lance registrado até o momento</p>
                            <p class="text-sm mt-1">Seja o primeiro a dar um lance!</p>
                        </div>

                        <!-- List -->
                        <ul v-else class="divide-y divide-gray-50">
                            <li v-for="(lance, index) in lances" :key="lance.id" class="px-6 py-4 hover:bg-gray-50 transition-colors flex items-start gap-4">
                                <!-- Avatar/Icon -->
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                                        {{ lance.usuario.substring(0, 1) }}
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-bold text-gray-900 truncate">{{ lance.usuario }}</span>
                                        <span class="text-base font-bold text-[#00a550]">{{ formatCurrency(lance.valor) }}</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                        <div class="flex items-center gap-1">
                                            <MapPin class="w-3 h-3" />
                                            <span>{{ lance.cidade }}{{ lance.uf ? '/' + lance.uf : '' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <Clock class="w-3 h-3" />
                                            <span>{{ lance.data }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-1.5 flex items-center gap-1.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                            {{ lance.tipo }}
                                        </span>
                                        <span v-if="index === 0" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-700 border border-green-200">
                                            Maior Lance
                                        </span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                        <button @click="close" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-sm">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
