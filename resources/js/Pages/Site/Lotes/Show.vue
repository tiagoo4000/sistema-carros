<script setup>
import { ref, watch, onMounted, onUnmounted, computed } from 'vue';
import { Head, useForm, usePage, Link, router } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
import ImageGallery from '@/Components/Site/ImageGallery.vue';
import BidHistoryModal from '@/Components/Site/BidHistoryModal.vue';
import LanceModal from '@/Components/Site/LanceModal.vue';
import CountDown from '@/Components/Site/CountDown.vue';
import PreStartMask from '@/Components/Site/PreStartMask.vue';
import Swal from 'sweetalert2';
import { useToast } from "vue-toastification";
import { 
    MapPin, Calendar, Gauge, Fuel, Info, FileText, CheckCircle, 
    AlertTriangle, Clock, Trophy, ArrowLeft, ArrowRight 
} from 'lucide-vue-next';

const props = defineProps({
    lote: Object,
    leilao: Object,
    previousLote: Object,
    nextLote: Object
});

// Reactive State
const loteData = ref({ ...props.lote });
const status = ref(props.lote.status);
const currentBid = ref(Number(props.lote.maior_lance?.valor || props.lote.valor_inicial || 0));
const pageCtx = usePage();
const initialWinner = pageCtx.props.vencedorNome || props.lote.maior_lance?.nome_exibicao || props.lote.maior_lance?.usuario?.nome || null;
const winnerName = ref(initialWinner);
const showHistoryModal = ref(false);
const showLanceModal = ref(false);

const user = pageCtx.props.auth.user;
const serverNow = ref(pageCtx.props.serverNow || null);
const nowTs = ref(Date.now());
let preStartTick = null;
const userVerification = computed(() => pageCtx.props.userVerification || null);
const isUserVerified = computed(() => userVerification.value?.status === 'approved');
const form = useForm({ valor: 0 });
const toast = useToast();

// Sync with Props
watch(() => props.lote, (newVal) => {
    loteData.value = { ...newVal };
    status.value = newVal.status;
    currentBid.value = Number(newVal.maior_lance?.valor || newVal.valor_inicial || 0);
    const fromPage = pageCtx.props.vencedorNome || null;
    winnerName.value = fromPage || newVal.maior_lance?.nome_exibicao || newVal.maior_lance?.usuario?.nome || null;
}, { deep: true });

// Real-time Updates
let channel = null;
onMounted(() => {
    if (window.Echo) {
        channel = window.Echo.channel(`lote.${props.lote.id}`)
            .listen('.lote.status.updated', (e) => {
                if (e.status) status.value = e.status;
                if (e.ends_at && e.ends_at !== loteData.value.ends_at) {
                    loteData.value.ends_at = e.ends_at;
                }
                if (e.server_now) {
                    serverNow.value = e.server_now;
                }
                if (e.valor_final && Number(e.valor_final) > currentBid.value) {
                    currentBid.value = Number(e.valor_final);
                }
                if (e.vencedor) {
                    winnerName.value = e.vencedor;
                } else if (e.status === 'vendido' && !winnerName.value) {
                     // Fallback if not sent in event but available logic
                }
            });
    }
});

onUnmounted(() => {
    if (channel) channel.stopListening('.lote.status.updated');
});

// Helpers
const formatCurrency = (value) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);

const formatDate = (date) => {
    return new Intl.DateTimeFormat('pt-BR', { 
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    }).format(new Date(date));
};

const openLanceModal = () => {
    if (!user) {
        Swal.fire({
            icon: 'warning',
            title: 'Login Necessário',
            text: 'Você precisa estar logado para dar um lance.',
            confirmButtonColor: '#00a550',
            confirmButtonText: 'Fazer Login'
        });
        return;
    }

    if (!isUserVerified.value) {
        Swal.fire({
            icon: 'info',
            title: 'Verificação Necessária',
            text: 'Para participar dos leilões é necessário enviar e aguardar aprovação dos seus documentos.',
            showCancelButton: true,
            confirmButtonText: 'Enviar Documentos',
            cancelButtonText: 'Fechar',
            confirmButtonColor: '#00a550'
        }).then((r) => {
            if (r.isConfirmed) {
                window.location.href = route('minha-conta.documentos');
            }
        });
        return;
    }

    // Check if auction is active
    const now = new Date();
    const start = new Date(props.leilao.data_inicio);
    const end = new Date(props.lote.ends_at || props.leilao.data_fim);
    
    if (now < start || now >= end) {
         Swal.fire({
            icon: 'error',
            title: 'Ops!',
            text: 'O leilão não está acontecendo no momento. Tente novamente quando estiver ativo.',
            confirmButtonColor: '#d33'
        });
        return;
    }

    showLanceModal.value = true;
};

const handleLanceSubmit = (valor) => {
    form.valor = valor;
    form.post(route('lotes.lance', props.lote.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Lance registrado com sucesso!', {
                icon: "🔨"
            });
            showLanceModal.value = false;
        },
        onError: (errors) => {
            toast.error(errors.valor || 'Erro ao registrar lance.');
        }
    });
};

const preStartActive = computed(() => {
    const now = new Date(nowTs.value);
    return now < new Date(props.leilao.data_inicio);
});
const isFinished = computed(() => ['vendido', 'finalizado', 'arrematado', 'sem_lances'].includes(status.value));

const statusBadge = computed(() => {
    if (preStartActive.value) {
        return { label: 'Loteando', class: 'bg-amber-100 text-amber-800 border-amber-200 animate-pulse' };
    }
    const s = status.value;
    switch(s) {
        case 'aberto': return { label: 'Ativo', class: 'bg-green-100 text-green-800 border-green-200' };
        case 'dou_lhe_1': return { label: 'Dou-lhe 1', class: 'bg-yellow-100 text-yellow-800 border-yellow-200 animate-pulse' };
        case 'dou_lhe_2': return { label: 'Dou-lhe 2', class: 'bg-orange-100 text-orange-800 border-orange-200 animate-pulse' };
        case 'vendido': return { label: 'Vendido', class: 'bg-red-100 text-red-800 border-red-200' };
        case 'finalizado': return { label: 'Finalizado', class: 'bg-gray-100 text-gray-800 border-gray-200' };
        case 'sem_lances': return { label: 'Sem Lances', class: 'bg-gray-100 text-gray-600 border-gray-200' };
        default: return { label: s.replace('_', ' '), class: 'bg-gray-100 text-gray-800 border-gray-200' };
    }
});

const isVendaDireta = computed(() => props.leilao?.tipo === 'venda_direta');
const winnerLabel = computed(() => isVendaDireta.value ? 'Comprador' : 'Vencedor');
const precoVendaDireta = computed(() => Number(loteData.value.valor_inicial || 0));
const showConfetti = ref(false);

const purchaseForm = useForm({});

const cleanupOverlayLeaks = () => {
    try {
        const candidates = Array.from(document.querySelectorAll('iframe'));
        candidates.forEach((el) => {
            const hasSrc = el.getAttribute('src');
            if (!hasSrc) {
                el.remove();
            }
        });
        if (window.Swal && window.Swal.close) {
            window.Swal.close();
        }
    } catch (e) {
    }
};
let iframeObserver = null;
onMounted(() => {
    preStartTick = setInterval(() => { nowTs.value = Date.now(); }, 1000);
    if (isVendaDireta.value) {
        cleanupOverlayLeaks();
        iframeObserver = new MutationObserver(() => {
            cleanupOverlayLeaks();
        });
        iframeObserver.observe(document.body, { childList: true, subtree: true });
    }
});
onUnmounted(() => {
    if (preStartTick) {
        clearInterval(preStartTick);
        preStartTick = null;
    }
    if (iframeObserver) {
        iframeObserver.disconnect();
        iframeObserver = null;
    }
});
const comprarAgora = async () => {
    if (!user) {
        Swal.fire({
            icon: 'warning',
            title: 'Login Necessário',
            text: 'Você precisa estar logado para comprar este lote.',
            confirmButtonColor: '#00a550',
            confirmButtonText: 'Fazer Login'
        });
        return;
    }
    const { isConfirmed } = await Swal.fire({
        title: 'Confirmar Compra',
        html: `
            <div style="text-align:left">
              <p>Você deseja realmente comprar este veículo pelo valor de <strong>${formatCurrency(precoVendaDireta.value)}</strong>?</p>
              <p style="font-size:12px;color:#6b7280;margin-top:6px">Ao confirmar, você concorda com os termos e condições da plataforma.</p>
              <label style="display:flex;align-items:center;gap:8px;margin-top:12px;cursor:pointer">
                <input id="vd_check" type="checkbox" />
                <span>Li e concordo com os termos</span>
              </label>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Confirmar Compra',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#00a550',
        cancelButtonColor: '#9ca3af',
        preConfirm: () => {
            const checked = (document.getElementById('vd_check') || {}).checked;
            if (!checked) {
                Swal.showValidationMessage('Você precisa aceitar os termos para continuar.');
            }
            return checked;
        }
    });
    if (!isConfirmed) {
        cleanupOverlayLeaks();
        return;
    }

    purchaseForm.post(route('lotes.comprar', props.lote.id), {
        preserveScroll: true,
        onSuccess: (page) => {
            status.value = 'vendido';
            winnerName.value = usePage().props.auth.user?.nome || 'Você';
            currentBid.value = precoVendaDireta.value;
            toast.success('Parabéns! Você é o comprador oficial deste lote. 🎉', {
                timeout: 4000
            });
            if (!sessionStorage.getItem(`vd_confetti_${props.lote.id}`)) {
                showConfetti.value = true;
                setTimeout(() => { showConfetti.value = false; }, 2500);
                sessionStorage.setItem(`vd_confetti_${props.lote.id}`, '1');
            }
            cleanupOverlayLeaks();
        },
        onError: (errors) => {
            toast.error(errors.compra || errors.message || 'Não foi possível concluir a compra.');
            cleanupOverlayLeaks();
        }
    });
};
</script>

<template>
    <Head :title="loteData.titulo" />

    <SiteLayout>
        <div class="bg-gray-50 min-h-screen pb-12">
            <div class="max-w-[1400px] mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb / Voltar -->
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-sm text-gray-500">
                    <div class="flex items-center">
                        <Link :href="route('leiloes.show', leilao.id)" class="hover:text-[#00a550] flex items-center transition-colors">
                            <ArrowLeft class="h-4 w-4 mr-1" />
                            Voltar para o Leilão
                        </Link>
                        <span class="mx-2">/</span>
                        <span class="font-semibold text-gray-700">Lote {{ loteData.ordem }}</span>
                    </div>

                    <!-- Navigation Between Lots -->
                    <div class="flex items-center gap-2 bg-white rounded-lg p-1 shadow-sm border border-gray-200">
                        <Link 
                            v-if="previousLote" 
                            :href="route('lotes.show', previousLote.id)" 
                            class="flex items-center gap-1 px-3 py-1.5 hover:bg-gray-50 rounded-md text-gray-700 hover:text-[#00a550] transition-colors"
                            title="Lote Anterior"
                        >
                            <ArrowLeft class="w-4 h-4" /> 
                            <span class="hidden sm:inline">Anterior</span>
                        </Link>
                        <span v-else class="flex items-center gap-1 px-3 py-1.5 text-gray-300 cursor-not-allowed">
                            <ArrowLeft class="w-4 h-4" /> 
                            <span class="hidden sm:inline">Anterior</span>
                        </span>

                        <div class="h-4 w-px bg-gray-200"></div>

                        <Link 
                            v-if="nextLote" 
                            :href="route('lotes.show', nextLote.id)" 
                            class="flex items-center gap-1 px-3 py-1.5 hover:bg-gray-50 rounded-md text-gray-700 hover:text-[#00a550] transition-colors"
                            title="Próximo Lote"
                        >
                            <span class="hidden sm:inline">Próximo</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                        <span v-else class="flex items-center gap-1 px-3 py-1.5 text-gray-300 cursor-not-allowed">
                            <span class="hidden sm:inline">Próximo</span>
                            <ArrowRight class="w-4 h-4" />
                        </span>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Coluna Esquerda: Galeria e Detalhes (Scrollável) -->
                    <div class="w-full lg:w-7/12 space-y-8">
                        
                        <!-- Galeria de Imagens -->
                        <div class="bg-white rounded-2xl shadow-sm p-4 border border-gray-100">
                            <ImageGallery 
                                :mainImage="loteData.foto_capa"
                                :images="loteData.fotos"
                                :title="loteData.titulo"
                                :isVendaDireta="isVendaDireta"
                                :status="status"
                            />
                        </div>

                        <!-- Descrição e Detalhes Completos -->
                        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                             <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                                <FileText class="w-5 h-5 text-[#00a550]" />
                                Descrição Detalhada
                            </h3>
                            <div class="prose prose-sm max-w-none text-gray-600">
                                <p class="whitespace-pre-line">{{ loteData.descricao_detalhada || loteData.descricao }}</p>
                            </div>
                            
                            <!-- Avisos / Condições -->
                            <div class="mt-8 bg-amber-50 rounded-lg p-4 border border-amber-100">
                                <h4 class="text-sm font-bold text-amber-800 flex items-center gap-2 mb-2">
                                    <AlertTriangle class="w-4 h-4" />
                                    Importante
                                </h4>
                                <ul class="text-xs text-amber-800/80 list-disc list-inside space-y-1">
                                    <li>Veículo vendido no estado em que se encontra.</li>
                                    <li>Visitação recomendada antes do lance.</li>
                                    <li>Custos de transferência e regularização por conta do arrematante.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Coluna Direita: Informações e Lances (Sticky) -->
                    <div class="w-full lg:w-5/12">
                        <div class="lg:sticky lg:top-24 space-y-6">
                            
                            <!-- Card Principal de Informações -->
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden relative">
                                <PreStartMask 
                                    v-if="!isVendaDireta && preStartActive" 
                                    :start-date="leilao.data_inicio" 
                                    :server-now="serverNow"
                                />
                                <!-- Faixa de Status -->
                                <div class="absolute top-0 left-0 w-full h-1.5"
                                     :class="{
                                        'bg-[#00a550]': status === 'aberto',
                                        'bg-yellow-500': ['dou_lhe_1', 'dou_lhe_2'].includes(status),
                                        'bg-red-600': isFinished,
                                        'bg-gray-400': status === 'sem_lances'
                                     }">
                                </div>

                                <div class="p-6">
                                    <!-- Cabeçalho do Lote -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <span class="inline-block py-1 px-2 rounded bg-gray-100 text-gray-600 text-xs font-bold uppercase tracking-wider mb-2">
                                                Lote #{{ loteData.ordem }}
                                            </span>
                                            <h1 class="text-2xl font-black text-gray-900 leading-tight">
                                                {{ loteData.titulo }}
                                            </h1>
                                            <p v-if="loteData.subtitulo" class="text-sm text-gray-500 mt-1">{{ loteData.subtitulo }}</p>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                             <span :class="statusBadge.class" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border transition-all duration-300">
                                                {{ statusBadge.label }}
                                            </span>
                                            <span v-if="isVendaDireta" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wide border border-rose-200 bg-rose-50 text-rose-700">
                                                VENDA DIRETA
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Countdown Section -->
                                    <div v-if="!isVendaDireta && loteData.ends_at && !preStartActive" class="mb-6 flex justify-center lg:justify-start">
                                        <CountDown :end-date="loteData.ends_at" :status="status" :server-now="serverNow" @expired="() => router.reload({ only: ['lote','leilao'] })" />
                                    </div>

                                    <!-- Grid de Detalhes Rápidos -->
                                    <div class="grid grid-cols-2 gap-3 mb-6">
                                        <div class="bg-gray-50 p-3 rounded-lg flex flex-col justify-center">
                                            <span class="text-[10px] text-gray-400 uppercase font-bold flex items-center gap-1">
                                                <Calendar class="w-3 h-3" /> Ano
                                            </span>
                                            <span class="font-bold text-gray-900">{{ loteData.ano || 'N/D' }}</span>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg flex flex-col justify-center">
                                            <span class="text-[10px] text-gray-400 uppercase font-bold flex items-center gap-1">
                                                <Gauge class="w-3 h-3" /> Km
                                            </span>
                                            <span class="font-bold text-gray-900">{{ loteData.quilometragem ? loteData.quilometragem + ' km' : 'N/D' }}</span>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg flex flex-col justify-center">
                                            <span class="text-[10px] text-gray-400 uppercase font-bold flex items-center gap-1">
                                                <Fuel class="w-3 h-3" /> Combustível
                                            </span>
                                            <span class="font-bold text-gray-900">{{ loteData.combustivel || 'N/D' }}</span>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg flex flex-col justify-center">
                                            <span class="text-[10px] text-gray-400 uppercase font-bold flex items-center gap-1">
                                                <MapPin class="w-3 h-3" /> Local
                                            </span>
                                            <span class="font-bold text-gray-900 truncate" :title="loteData.localizacao">{{ loteData.localizacao || 'N/D' }}</span>
                                        </div>
                                    </div>

                                    <!-- Lista Compacta de Infos -->
                                    <div class="space-y-2 text-sm border-t border-gray-100 pt-4 mb-6">
                                        <div class="flex justify-between" v-if="loteData.cor">
                                            <span class="text-gray-500">Cor:</span>
                                            <span class="font-medium text-gray-900">{{ loteData.cor }}</span>
                                        </div>
                                        <div class="flex justify-between" v-if="loteData.possui_chave !== null">
                                            <span class="text-gray-500">Possui Chave:</span>
                                            <span class="font-medium text-gray-900">{{ loteData.possui_chave ? 'Sim' : 'Não' }}</span>
                                        </div>
                                        <div class="flex justify-between" v-if="loteData.tipo_retomada">
                                            <span class="text-gray-500">Origem:</span>
                                            <span class="font-medium text-gray-900">{{ loteData.tipo_retomada }}</span>
                                        </div>
                                        <div class="flex justify-between" v-if="loteData.prazo_documentacao">
                                            <span class="text-gray-500">Documentação:</span>
                                            <span class="font-medium text-gray-900">{{ loteData.prazo_documentacao }}</span>
                                        </div>
                                    </div>

                                    <!-- RESULTADO (Vendido) -->
                                    <div v-if="status === 'vendido'" class="bg-green-50 -mx-6 -mb-6 p-6 border-t border-green-100 text-center animate-fade-in">
                                        <div class="inline-flex p-3 bg-white text-green-600 rounded-full mb-3 shadow-sm">
                                            <Trophy class="w-8 h-8" />
                                        </div>
                                        <h3 class="text-xl font-black text-green-800 uppercase mb-1">Lote Vendido!</h3>
                                        <p class="text-sm text-green-700 mb-6">Este lote foi arrematado com sucesso.</p>
                                        
                                        <div class="grid grid-cols-2 gap-4 text-left bg-white p-4 rounded-xl shadow-sm border border-green-100">
                                            <div>
                                                <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">{{ winnerLabel }}</p>
                                                <p v-if="winnerName" class="font-bold text-gray-900 truncate" :title="winnerName">{{ winnerName }}</p>
                                                <p v-else class="font-bold text-gray-400 truncate">Vencedor não disponível</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Valor Final</p>
                                                <p class="font-bold text-[#00a550]">{{ formatCurrency(currentBid) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- RESULTADO (Não arrematado) -->
                                    <div v-else-if="['finalizado','sem_lances'].includes(status)" class="bg-gray-50 -mx-6 -mb-6 p-6 border-t border-gray-100 text-center animate-fade-in">
                                        <div class="inline-flex p-3 bg-white text-gray-600 rounded-full mb-3 shadow-sm">
                                            <Gavel class="w-8 h-8" />
                                        </div>
                                        <h3 class="text-xl font-black text-gray-800 uppercase mb-1">Lote Não Arrematado</h3>
                                        <p class="text-sm text-gray-600 mb-1">Sem lances válidos até o encerramento.</p>
                                        <p class="text-sm text-gray-500">Lote sem arrematante.</p>
                                    </div>

                                    <!-- Área de Lances (Se aberto) -->
                                    <div v-else class="bg-[#f8f9fa] -mx-6 -mb-6 p-6 border-t border-gray-200">
                                        <template v-if="isVendaDireta">
                                            <div class="flex justify-between items-end mb-4">
                                                <div>
                                                    <p class="text-xs text-gray-500 font-bold uppercase mb-1">Preço</p>
                                                    <p class="text-3xl font-black text-[#002f6c] transition-all duration-300">
                                                        {{ formatCurrency(precoVendaDireta) }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">Valor fixo. Sem disputa.</p>
                                                </div>
                                            </div>
                                            <button 
                                                v-if="status !== 'vendido' && leilao.status === 'ativo'"
                                                @click="comprarAgora"
                                                class="w-full flex justify-center items-center gap-2 py-4 px-6 rounded-xl shadow-lg shadow-rose-900/20 text-base font-bold text-white bg-rose-600 hover:bg-rose-700 hover:scale-[1.02] active:scale-[0.98] transition-all"
                                            >
                                                COMPRAR AGORA
                                            </button>
                                            <button 
                                                v-else
                                                disabled
                                                class="w-full flex justify-center py-4 px-6 rounded-xl shadow-sm text-base font-medium text-gray-400 bg-gray-200 cursor-not-allowed border border-gray-300"
                                            >
                                                Indisponível
                                            </button>
                                            <p class="mt-3 text-[10px] text-center text-gray-400">
                                                Sujeito à confirmação de disponibilidade.
                                            </p>
                                        </template>
                                        <template v-else>
                                            <div class="flex justify-between items-end mb-4">
                                                <div>
                                                    <p class="text-xs text-gray-500 font-bold uppercase mb-1">Lance Atual</p>
                                                    <p class="text-3xl font-black text-[#002f6c] transition-all duration-300" :key="currentBid">
                                                        {{ formatCurrency(currentBid) }}
                                                    </p>
                                                    <p class="text-xs text-gray-500" v-if="!loteData.maior_lance && currentBid == loteData.valor_inicial">Lance Inicial</p>
                                                    <button 
                                                        @click="showHistoryModal = true" 
                                                        class="text-xs text-blue-600 hover:text-blue-800 hover:underline font-medium flex items-center gap-1 mt-2 transition-colors"
                                                    >
                                                        <Clock class="w-3 h-3" />
                                                        Ver histórico de lances
                                                    </button>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs text-gray-500">Incremento</p>
                                                    <p class="text-sm font-bold text-gray-700">+ {{ formatCurrency(loteData.valor_minimo_incremento) }}</p>
                                                </div>
                                            </div>
                                            <div v-if="user && !isUserVerified" class="mb-4">
                                                <div class="rounded-xl border p-4"
                                                     :class="userVerification?.status === 'rejected' ? 'border-red-200 bg-red-50 text-red-800' : 'border-amber-200 bg-amber-50 text-amber-800'">
                                                    <div class="flex items-start gap-3">
                                                        <div class="mt-0.5">
                                                            <AlertTriangle class="w-5 h-5" />
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="font-semibold" v-if="userVerification?.status === 'pending'">Conta não verificada</div>
                                                            <div class="font-semibold" v-else-if="userVerification?.status === 'rejected'">Documento rejeitado</div>
                                                            <div class="text-sm mt-1" v-if="userVerification?.status === 'pending'">
                                                                ⏳ Seus documentos estão em análise. Conclua a verificação para participar dos leilões.
                                                            </div>
                                                            <div class="text-sm mt-1" v-else-if="userVerification?.status === 'rejected'">
                                                                Motivos:
                                                                <ul class="list-disc ml-5 mt-1">
                                                                    <li v-for="(r, idx) in userVerification?.reasons" :key="idx">
                                                                        {{ r.tipo.toUpperCase() }} — {{ r.motivo }}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <Link :href="route('minha-conta.documentos')" class="ml-auto py-2 px-3 rounded-lg border font-medium"
                                                              :class="userVerification?.status === 'rejected' ? 'border-red-400 text-red-700 hover:bg-red-100' : 'border-amber-400 text-amber-800 hover:bg-amber-100'">
                                                            Concluir Verificação
                                                        </Link>
                                                    </div>
                                                </div>
                                            </div>
                                            <button 
                                                v-if="['aberto', 'dou_lhe_1', 'dou_lhe_2'].includes(status) && leilao.status === 'ativo' && !preStartActive"
                                                :disabled="user && !isUserVerified"
                                                @click="openLanceModal"
                                                class="w-full flex justify-center items-center gap-2 py-4 px-6 rounded-xl shadow-lg shadow-green-900/20 text-base font-bold text-white bg-[#00a550] hover:bg-green-700 hover:scale-[1.02] active:scale-[0.98] transition-all relative overflow-hidden group"
                                            >
                                                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                                                <CheckCircle class="w-5 h-5" />
                                                DAR LANCE AGORA
                                            </button>
                                            <button 
                                                v-else
                                                disabled
                                                class="w-full flex justify-center py-4 px-6 rounded-xl shadow-sm text-base font-medium text-gray-400 bg-gray-200 cursor-not-allowed border border-gray-300"
                                            >
                                                Indisponível para Lances
                                            </button>
                                            <p class="mt-3 text-[10px] text-center text-gray-400">
                                                Ao clicar em "Dar Lance", você concorda com os <a href="#" class="underline hover:text-gray-600">Termos de Uso</a>.
                                            </p>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Ajuda / Contato -->
                             <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 flex items-start gap-3">
                                <div class="bg-blue-50 p-2 rounded-full text-blue-600">
                                    <Info class="w-5 h-5" />
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Dúvidas sobre este lote?</h4>
                                    <p class="text-xs text-gray-500 mt-1">Entre em contato com nossa equipe de suporte informando o Lote #{{ loteData.ordem }}.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modais -->
        <BidHistoryModal 
            v-if="!isVendaDireta"
            :show="showHistoryModal" 
            :loteId="loteData.id" 
            @close="showHistoryModal = false" 
        />

        <LanceModal
            v-if="!isVendaDireta"
            :show="showLanceModal"
            :current-bid="currentBid"
            :increment="loteData.valor_minimo_incremento"
            :lote-number="loteData.ordem || loteData.id"
            @close="showLanceModal = false"
            @submit="handleLanceSubmit"
        />
        
        <div v-if="isVendaDireta && showConfetti" class="pointer-events-none fixed inset-0 z-40 overflow-hidden">
            <div class="absolute w-2 h-2 bg-rose-400 rounded-full animate-fall" style="left:10%"></div>
            <div class="absolute w-2 h-2 bg-green-400 rounded-full animate-fall delay-100" style="left:25%"></div>
            <div class="absolute w-2 h-2 bg-yellow-400 rounded-full animate-fall delay-200" style="left:40%"></div>
            <div class="absolute w-2 h-2 bg-blue-400 rounded-full animate-fall delay-300" style="left:55%"></div>
            <div class="absolute w-2 h-2 bg-purple-400 rounded-full animate-fall delay-400" style="left:70%"></div>
            <div class="absolute w-2 h-2 bg-pink-400 rounded-full animate-fall delay-500" style="left:85%"></div>
        </div>
    </SiteLayout>
</template>

<style scoped>
.animate-fall {
  animation: fall 2.5s ease-in forwards;
}
@keyframes fall {
  0% { transform: translateY(-20vh) rotate(0deg); opacity: 0; }
  10% { opacity: 1; }
  100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
}
.delay-100 { animation-delay: .1s; }
.delay-200 { animation-delay: .2s; }
.delay-300 { animation-delay: .3s; }
.delay-400 { animation-delay: .4s; }
.delay-500 { animation-delay: .5s; }
</style>
