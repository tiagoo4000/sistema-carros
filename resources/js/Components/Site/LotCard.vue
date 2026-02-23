<script setup>
import { Link } from '@inertiajs/vue3';
import { 
    MapPin, 
    Calendar, 
    Camera, 
    Video, 
    Heart, 
    Clock, 
    Gauge,
    Info,
    Gavel
} from 'lucide-vue-next';
import { computed, ref, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    lote: Object
});

const status = ref(props.lote.status);
const nowTs = ref(Date.now());
let tickId;
onMounted(() => { tickId = setInterval(() => nowTs.value = Date.now(), 1000); });
onUnmounted(() => { if (tickId) clearInterval(tickId); });
const currentBid = ref(props.lote.maior_lance?.valor || props.lote.valor_inicial);
const isVisible = ref(true);
const vencedorNome = ref(null);

// Watch for prop changes to update local state (if parent refreshes)
watch(() => props.lote, (newVal) => {
    if (newVal) {
        // Only update status if it's not 'vendido' locally (to avoid re-showing if we hid it)
        if (status.value !== 'vendido') {
            status.value = newVal.status;
        }
        // Always update bid if newer
        if (newVal.maior_lance?.valor) {
            currentBid.value = newVal.maior_lance.valor;
        }
    }
}, { deep: true });

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const hasVideo = computed(() => {
    return props.lote.descricao?.includes('youtube') || props.lote.descricao?.includes('vimeo');
});

const phase = computed(() => {
    const t = new Date(nowTs.value);
    const start = props.lote.leilao?.data_inicio ? new Date(props.lote.leilao.data_inicio) : null;
    const end = props.lote.ends_at ? new Date(props.lote.ends_at) : (props.lote.leilao?.data_fim ? new Date(props.lote.leilao.data_fim) : null);
    if (start && t < start) return 'loteando';
    if (end && t < end) return 'aberto';
    return 'encerrado';
});

const statusBadge = computed(() => {
    // prioridade: estados temporais -> overlays finais
    if (phase.value === 'loteando') return { label: 'Loteando', class: 'bg-amber-500 text-white animate-pulse' };
    if (phase.value === 'encerrado' && !['vendido'].includes(status.value)) return { label: 'Encerrado', class: 'bg-gray-600 text-white' };
    switch(status.value) {
        case 'aberto': return { label: 'Ativo', class: 'bg-[#00a550] text-white' };
        case 'dou_lhe_1': return { label: 'Dou-lhe 1', class: 'bg-yellow-500 text-white animate-pulse' };
        case 'dou_lhe_2': return { label: 'Dou-lhe 2', class: 'bg-orange-600 text-white animate-pulse' };
        case 'vendido': return { label: 'Vendido', class: 'bg-red-600 text-white' };
        case 'finalizado': return { label: 'Finalizado', class: 'bg-gray-500 text-white' };
        case 'sem_lances': return { label: 'Sem Lances', class: 'bg-gray-400 text-white' };
        default: return null;
    }
});

let channel = null;

onMounted(() => {
    if (window.Echo) {
        channel = window.Echo.channel(`lote.${props.lote.id}`)
            .listen('.lote.status.updated', (e) => {
                console.log('Evento recebido:', e);
                
                // If new bid (reset state)
                if (e.valor_final && e.valor_final > currentBid.value && status.value !== 'vendido') {
                     currentBid.value = e.valor_final;
                     // If we were in DL1/DL2, reset to aberto (though backend should send status update too)
                }

                if (e.status) {
                    status.value = e.status;
                    if (e.status === 'vendido') {
                        vencedorNome.value = e.vencedor || '';
                    } else {
                        vencedorNome.value = '';
                    }
                }
            });
    }
});

onUnmounted(() => {
    if (channel) {
        channel.stopListening('.lote.status.updated');
    }
});
</script>

<template>
    <div v-if="isVisible" class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 group flex flex-col h-full relative"
         :class="{ 'opacity-80': status === 'vendido' }">
        <!-- Link Overlay -->
        <Link :href="route('lotes.show', lote.id)" class="absolute inset-0 z-10" :aria-label="'Ver lote ' + lote.titulo"></Link>

        <!-- Status Overlays / Animations -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform opacity-0 scale-50"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-50"
        >
            <div v-if="status === 'dou_lhe_1'" class="absolute inset-0 z-30 flex items-center justify-center pointer-events-none bg-yellow-500/20 backdrop-blur-[2px]">
                <div class="bg-yellow-500 text-white px-6 py-3 rounded-full shadow-xl transform animate-bounce-slow">
                    <span class="text-2xl font-black uppercase tracking-widest drop-shadow-md">Dou-lhe 1!</span>
                </div>
            </div>
        </Transition>

        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform opacity-0 scale-150"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-150"
        >
            <div v-if="status === 'dou_lhe_2'" class="absolute inset-0 z-30 flex items-center justify-center pointer-events-none bg-orange-600/30 backdrop-blur-[2px]">
                <div class="bg-orange-600 text-white px-8 py-4 rounded-full shadow-2xl transform animate-pulse">
                    <span class="text-3xl font-black uppercase tracking-widest drop-shadow-md">Dou-lhe 2!</span>
                </div>
            </div>
        </Transition>

        <Transition
            enter-active-class="transition ease-out duration-500"
            enter-from-class="transform opacity-0 scale-[3.0] rotate-45"
            enter-to-class="transform opacity-100 scale-100 rotate-[-10deg]"
        >
            <div v-if="status === 'vendido'" class="absolute inset-0 z-40 flex flex-col items-center justify-center bg-black/30 backdrop-blur-[1px] pointer-events-none text-center p-4">
                <div class="border-2 border-white p-3 rounded-xl bg-red-600 text-white shadow-xl transform -rotate-6 mb-2">
                    <span class="text-3xl md:text-4xl font-black uppercase tracking-widest border-2 border-white px-4 py-1.5 block">VENDIDO</span>
                </div>
                <div class="text-white font-bold text-lg mt-3 animate-fade-in-up">
                    <p class="text-xs uppercase opacity-80 mb-1">Valor Final</p>
                    <p class="text-2xl text-white">{{ formatCurrency(currentBid) }}</p>
                    <p v-if="vencedorNome" class="text-sm mt-2 opacity-80">Vencedor: {{ vencedorNome }}</p>
                </div>
            </div>
        </Transition>

        <Transition
            enter-active-class="transition ease-out duration-400"
            enter-from-class="transform opacity-0 scale-90"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-90"
        >
            <div v-if="status === 'sem_lances' || status === 'finalizado'" class="absolute inset-0 z-30 flex flex-col items-center justify-center bg-black/25 backdrop-blur-[1px] pointer-events-none text-center p-4">
                <div class="bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md mb-2">
                    <span class="text-xl font-black uppercase tracking-widest">ENCERRADO</span>
                </div>
                <div class="text-white text-sm opacity-90">
                    Lote sem arrematante
                </div>
            </div>
        </Transition>

        <!-- Image Container -->
        <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
            <img 
                :src="lote.foto_capa ? `/storage/${lote.foto_capa}` : 'https://via.placeholder.com/400x300?text=Sem+Foto'" 
                :alt="lote.titulo"
                loading="lazy"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
            
            <!-- Badges Top Left -->
            <div class="absolute top-2 left-2 flex flex-col gap-1 z-20">
                <span v-if="statusBadge" :class="statusBadge.class" class="text-[10px] font-bold px-2 py-1 rounded backdrop-blur-sm uppercase tracking-wider shadow-sm">
                    {{ statusBadge.label }}
                </span>
                <span class="bg-black/70 text-white text-[10px] font-bold px-2 py-1 rounded backdrop-blur-sm uppercase tracking-wider">
                    Lote {{ lote.ordem || lote.id }}
                </span>
                <span v-if="lote.localizacao" class="bg-[#00a550] text-white text-[10px] font-bold px-2 py-1 rounded backdrop-blur-sm flex items-center gap-1">
                    <MapPin class="w-3 h-3" /> {{ lote.localizacao.substring(0, 2).toUpperCase() }}
                </span>
            </div>

            <!-- Badges Top Right -->
            <div class="absolute top-2 right-2 flex gap-1 z-20">
                <button class="bg-white/90 p-1.5 rounded-full hover:bg-white text-gray-400 hover:text-red-500 transition-colors shadow-sm">
                    <Heart class="w-4 h-4" />
                </button>
            </div>

            <!-- Media Indicators Bottom -->
            <div class="absolute bottom-2 left-2 flex gap-1 z-20">
                <div v-if="lote.fotos_count > 0" class="flex items-center gap-1 bg-black/60 text-white text-[10px] px-2 py-0.5 rounded-full backdrop-blur-sm">
                    <Camera class="w-3 h-3" /> {{ lote.fotos_count }}
                </div>
                <div v-if="hasVideo" class="flex items-center gap-1 bg-red-600/80 text-white text-[10px] px-2 py-0.5 rounded-full backdrop-blur-sm">
                    <Video class="w-3 h-3" /> Vídeo
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 flex flex-col flex-grow relative z-20 pointer-events-none">
            <!-- Title & Status -->
            <div class="mb-3">
                <div v-if="lote.tipo_retomada" class="inline-block px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] font-bold uppercase tracking-wide mb-2 border border-blue-100">
                    {{ lote.tipo_retomada }}
                </div>
                <h3 class="font-bold text-gray-800 leading-snug text-base line-clamp-2 group-hover:text-[#00a550] transition-colors">
                    {{ lote.titulo }}
                </h3>
                <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ lote.subtitulo || lote.descricao }}</p>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-2 mb-4">
                <div class="flex items-center gap-1.5 text-xs text-gray-600 bg-gray-50 p-1.5 rounded">
                    <Calendar class="w-3.5 h-3.5 text-gray-400" />
                    <span class="font-medium">{{ lote.ano || '---' }}</span>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-gray-600 bg-gray-50 p-1.5 rounded">
                    <Gauge class="w-3.5 h-3.5 text-gray-400" />
                    <span class="font-medium">{{ lote.quilometragem ? lote.quilometragem + ' km' : '---' }}</span>
                </div>
            </div>

            <div class="mt-auto pt-3 border-t border-gray-100">
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-[10px] uppercase text-gray-400 font-bold tracking-wider mb-0.5">Lance Atual</p>
                        <p class="text-lg font-black text-[#00a550] leading-none transition-all duration-300" :class="{'scale-110 text-orange-500': status === 'dou_lhe_1', 'scale-125 text-red-600': status === 'dou_lhe_2'}">
                            {{ formatCurrency(currentBid) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes bounce-slow {
  0%, 100% {
    transform: translateY(-5%);
    animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
  }
  50% {
    transform: translateY(0);
    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
  }
}
.animate-bounce-slow {
  animation: bounce-slow 1s infinite;
}
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
