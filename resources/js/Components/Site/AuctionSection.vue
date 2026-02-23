<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Clock, Calendar, ArrowRight, Gavel, ChevronLeft, ChevronRight, AlertCircle } from 'lucide-vue-next';
import LotCard from '@/Components/Site/LotCard.vue';
import CountDown from '@/Components/Site/CountDown.vue';

const props = defineProps({
    leilao: Object
});

const page = usePage();
const serverNow = computed(() => page.props.serverNow || null);
const clientServerDelta = computed(() => {
    if (!serverNow.value) return 0;
    return new Date(serverNow.value).getTime() - Date.now();
});

const locationBadge = computed(() => {
    return props.leilao?.tipo === 'venda_direta' ? 'Venda Direta' : (props.leilao?.local || 'Online');
});
const isVendaDireta = computed(() => props.leilao?.tipo === 'venda_direta');
const scrollContainer = ref(null);

const scrollLeft = () => {
    if (scrollContainer.value) {
        scrollContainer.value.scrollBy({ left: -320, behavior: 'smooth' });
    }
};

const scrollRight = () => {
    if (scrollContainer.value) {
        scrollContainer.value.scrollBy({ left: 320, behavior: 'smooth' });
    }
};

const activeLoteEnd = computed(() => {
    const lotes = props.leilao?.lotes || [];
    const active = lotes
        .filter(l => l.status === 'aberto' && l.ends_at)
        .sort((a, b) => new Date(a.ends_at).getTime() - new Date(b.ends_at).getTime());
    return active.length ? active[0].ends_at : null;
});
const hasActive = computed(() => !!activeLoteEnd.value);
const hasAnyLote = computed(() => Array.isArray(props.leilao?.lotes) && props.leilao.lotes.length > 0);
const isFinalized = computed(() => {
    if (props.leilao?.status === 'finalizado') return true;
    if (!hasAnyLote.value) return false;
    const anyActive = props.leilao.lotes.some(l => l.status === 'aberto');
    return !anyActive;
});

const formattedDate = computed(() => {
    const date = new Date(props.leilao.data_fim);
    const weekday = new Intl.DateTimeFormat('pt-BR', { weekday: 'long' }).format(date);
    const dayMonthYear = new Intl.DateTimeFormat('pt-BR').format(date);
    const hours = new Intl.DateTimeFormat('pt-BR', { hour: '2-digit', minute: '2-digit' }).format(date);
    
    const weekdayCap = weekday.charAt(0).toUpperCase() + weekday.slice(1);
    
    return `${weekdayCap} • ${dayMonthYear} • ${hours}h`;
});

const urgent = ref(false);
let urgencyTimer = null;
const updateUrgency = () => {
    if (!activeLoteEnd.value) {
        urgent.value = false;
        return;
    }
    const endTs = new Date(activeLoteEnd.value).getTime();
    const nowServer = Date.now() + clientServerDelta.value;
    const diffSec = Math.floor((endTs - nowServer) / 1000);
    urgent.value = diffSec > 0 && diffSec < 60;
};

onMounted(() => {
    updateUrgency();
    urgencyTimer = setInterval(updateUrgency, 1000);
});
onUnmounted(() => {
    if (urgencyTimer) clearInterval(urgencyTimer);
});
watch(activeLoteEnd, updateUrgency);
</script>

<template>
    <div class="relative transition-all duration-500
        after:content-[''] after:block after:h-px after:bg-gradient-to-r after:from-transparent after:via-[#eaeaea] after:to-transparent after:mx-auto after:w-[60%] after:max-w-[1100px] after:rounded-full last:after:hidden
        after:mt-6 after:mb-6 md:after:mt-8 md:after:mb-8">
        <!-- Main Card Container -->
        <div class="bg-white rounded-2xl md:rounded-3xl shadow-lg border overflow-hidden" :class="[ isFinalized ? 'opacity-75 grayscale-[0.5]' : '', isVendaDireta ? 'border-rose-200 bg-rose-50' : 'border-gray-100' ]">
            
            <!-- Auction Header / Info Block -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 p-8 md:p-10 border-b relative" :class="isVendaDireta ? 'bg-rose-50 border-rose-200' : 'bg-gray-50 border-gray-200'">
                <div class="absolute left-0 top-0 h-full w-1" :class="isVendaDireta ? 'bg-rose-600' : 'bg-[#cc0000]'"></div>
                
                <!-- Finalized Overlay Badge -->
                <div v-if="isFinalized" class="absolute top-0 right-0 bg-gray-800 text-white text-xs font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider z-10">
                    Encerrado
                </div>

                <!-- Left: Identity & Date -->
                <div class="flex items-center gap-6">
                    <!-- Logo Block -->
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-50 rounded-xl flex items-center justify-center border border-gray-100 shrink-0 overflow-hidden">
                        <!-- Comitente Logo or Placeholder -->
                        <img 
                            v-if="leilao.comitente && leilao.comitente.imagem" 
                            :src="`/storage/${leilao.comitente.imagem}`" 
                            :alt="leilao.comitente.nome"
                            class="w-full h-full object-contain p-2"
                        />
                        <Gavel v-else class="w-8 h-8 text-[#00a550]" :class="{ 'text-gray-500': isFinalized }" />
                    </div>

                    <div>
                        <div class="flex items-center gap-3 text-sm text-gray-600 font-medium mb-2">
                            <Calendar class="w-4 h-4" />
                            <span class="capitalize">{{ formattedDate }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded font-bold uppercase tracking-wide text-[10px]" :class="isVendaDireta ? 'bg-rose-50 border border-rose-200 text-rose-700' : 'bg-white border border-gray-200 text-gray-700'">
                                {{ locationBadge }}
                            </span>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-black text-gray-900 uppercase italic tracking-tight leading-none">
                            {{ leilao.titulo }}
                        </h2>
                        <!-- Subinfo opcional deixada propositalmente vazia para reduzir ruído visual -->
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                    <div 
                        v-if="!isFinalized && hasActive" 
                        class="auction-countdown-premium text-white rounded-2xl shadow-lg border bg-[#0f172a] w-full sm:w-auto"
                        :class="[{ 'is-urgent': urgent }, 'px-2 py-1 md:px-3 md:py-2 border-white/10']"
                    >
                        <div class="flex items-center justify-center text-[9px] md:text-[10px] font-bold uppercase tracking-[0.2em] text-gray-300 mb-0.5 md:mb-1">
                            <span>Encerrando em</span>
                        </div>
                        <div class="timer-strip relative flex items-baseline justify-center gap-1 md:gap-1.5 rounded-xl">
                            <div class="timer-strip-glow"></div>
                            <CountDown :end-date="activeLoteEnd" :server-now="serverNow" status="aberto" class="!text-white !leading-none" />
                        </div>
                        <div class="text-[9px] md:text-[10px] font-semibold uppercase tracking-wider text-[#fca311] mt-1 leading-none text-center">
                            Faça seu lance
                        </div>
                    </div>
                    <div v-else-if="isFinalized" class="flex gap-2 items-center bg-gray-200 text-gray-600 px-4 py-2 rounded-lg">
                        <AlertCircle class="w-4 h-4" />
                        <span class="font-bold uppercase text-sm">Leilão Finalizado</span>
                    </div>

                    <Link 
                        :href="route('leiloes.show', leilao.id)" 
                        class="w-full sm:w-auto px-6 py-3 font-bold rounded-lg shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2 uppercase tracking-wide text-sm"
                        :class="isFinalized ? 'bg-gray-500 hover:bg-gray-600 text-white shadow-gray-500/20' : 'bg-[#00a550] hover:bg-green-700 text-white shadow-green-500/20'"
                    >
                        <span>{{ isFinalized ? 'Ver Resultados' : `Ver ${leilao.lotes_count} Lotes` }}</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>

            <!-- Lots Grid -->
            <div v-if="leilao.lotes && leilao.lotes.length > 0" class="relative group bg-[#fafafa] px-6 md:px-8 py-6 md:py-8">
                
                <!-- Left Button -->
                <button 
                    @click="scrollLeft"
                    class="absolute top-1/2 -translate-y-1/2 z-30 left-3 md:left-5 bg-white shadow-md border border-gray-200 p-2 rounded-full text-gray-700 hover:text-[#00a550] hover:scale-110 transition-all opacity-80 hover:opacity-100 disabled:opacity-40"
                    aria-label="Scroll Left"
                >
                    <ChevronLeft class="w-6 h-6" />
                </button>

                <!-- Carousel Container -->
                <div 
                    ref="scrollContainer"
                    class="flex overflow-x-auto gap-4 scrollbar-hide snap-x px-5 py-2 -mx-2 no-scrollbar"
                >
                    <div 
                        v-for="lote in leilao.lotes" 
                        :key="lote.id" 
                        class="min-w-[200px] w-[85vw] sm:w-[45vw] md:w-[calc(33.33%-0.75rem)] lg:w-[calc(25%-0.75rem)] lg:min-w-0 snap-center flex-shrink-0"
                    >
                        <LotCard :lote="lote" />
                    </div>
                </div>

                <!-- Right Button -->
                <button 
                    @click="scrollRight"
                    class="absolute top-1/2 -translate-y-1/2 z-30 right-3 md:right-5 bg-white shadow-md border border-gray-200 p-2 rounded-full text-gray-700 hover:text-[#00a550] hover:scale-110 transition-all opacity-80 hover:opacity-100 disabled:opacity-40"
                    aria-label="Scroll Right"
                >
                    <ChevronRight class="w-6 h-6" />
                </button>

            </div>
            <div v-else class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                <p class="text-gray-500">Nenhum lote disponível para visualização neste momento.</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.auction-countdown-premium :deep(.inline-flex) {
    gap: 0.125rem;
}
.auction-countdown-premium :deep(.bg-gray-100) {
    background: transparent !important;
    padding: 0 !important;
    min-width: 0 !important;
}
.auction-countdown-premium :deep(.bg-gray-100 > .text-\[10px\]) {
    display: none !important;
}
.auction-countdown-premium :deep(.font-mono) {
    font-size: 1.125rem !important;
    line-height: 1 !important;
    font-weight: 900 !important;
    color: #ffffff !important;
    letter-spacing: 0.04em;
    transition: transform 120ms ease, opacity 120ms ease;
}
@media (min-width: 768px) {
  .auction-countdown-premium :deep(.font-mono) {
    font-size: 1.375rem !important;
  }
}
.auction-countdown-premium :deep(.text-gray-300.font-bold) {
    color: rgba(255,255,255,0.28) !important;
    font-weight: 900 !important;
    margin: 0 0.1rem;
}
@keyframes pulseSoft {
  0%, 100% { transform: translateY(0); opacity: 1; }
  50% { transform: translateY(-1px); opacity: 0.9; }
}
.auction-countdown-premium :deep(.bg-gray-100:last-of-type .font-mono) {
    color: #ef4444 !important;
    animation: pulseSoft 1s ease-in-out infinite;
}
.auction-countdown-premium {
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.05), 0 8px 20px rgba(0,0,0,0.15);
    backdrop-filter: saturate(1.05);
    background-image:
      radial-gradient(600px 60px at 50% 0%, rgba(59,130,246,0.15), transparent 60%),
      radial-gradient(400px 50px at 60% 115%, rgba(249,115,22,0.15), transparent 60%),
      linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0));
}
.timer-strip {
    position: relative;
    padding: 0.25rem 0.5rem;
    border-radius: 0.875rem;
    background: linear-gradient(180deg, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.03) 100%);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.10),
      inset 0 -1px 0 rgba(0,0,0,0.25);
}
.timer-strip-glow {
    content: "";
    position: absolute;
    inset: -2px;
    border-radius: 1rem;
    pointer-events: none;
    background:
      radial-gradient(300px 30px at 50% -10%, rgba(59,130,246,0.18), transparent 60%),
      radial-gradient(200px 30px at 70% 120%, rgba(249,115,22,0.18), transparent 60%);
    filter: blur(6px);
    opacity: 0.7;
}
.auction-countdown-premium.is-urgent {
    border-color: rgba(239, 68, 68, 0.35) !important;
    box-shadow: inset 0 0 0 1px rgba(239, 68, 68, 0.25), 0 10px 24px rgba(239, 68, 68, 0.12), 0 8px 20px rgba(0,0,0,0.15);
}
.auction-countdown-premium.is-urgent :deep(.bg-gray-100:last-of-type .font-mono) {
    color: #f97316 !important;
    animation-duration: 0.6s;
}
</style>
