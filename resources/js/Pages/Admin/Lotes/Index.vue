<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DropdownFilter from '@/Components/Mosaic/Components/DropdownFilter.vue';
import { Edit, Trash2, Eye, Box, DollarSign, Image as ImageIcon, ArrowLeft } from 'lucide-vue-next';
import CopyLink from '@/Components/Common/CopyLink.vue';
import ActionButton from '@/Components/Admin/ActionButton.vue';
import CardActionGroup from '@/Components/Admin/CardActionGroup.vue';
import axios from 'axios';

const props = defineProps({
    lotes: Object,
    leilao: Object, // Optional, if filtered
    filters: Object
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const nowTs = ref(Date.now());
let tickId;
onMounted(() => { tickId = setInterval(() => nowTs.value = Date.now(), 1000); });
onUnmounted(() => { if (tickId) clearInterval(tickId); });

const lotePhase = (lote) => {
    const t = new Date(nowTs.value);
    const start = lote.leilao?.data_inicio ? new Date(lote.leilao.data_inicio) : null;
    const end = lote.ends_at ? new Date(lote.ends_at) : (lote.leilao?.data_fim ? new Date(lote.leilao.data_fim) : null);
    if (start && t < start) return 'Loteando';
    if (end && t < end) return 'Aberto';
    return 'Encerrado';
};
const phaseClass = (phase) => {
    switch (phase) {
        case 'Aberto': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-400/10 dark:text-emerald-400';
        case 'Loteando': return 'bg-amber-100 text-amber-800 dark:bg-amber-400/10 dark:text-amber-400';
        case 'Encerrado': return 'bg-slate-100 text-slate-800 dark:bg-slate-400/10 dark:text-slate-400';
        default: return 'bg-gray-100 text-gray-800 dark:bg-gray-400/10 dark:text-gray-400';
    }
};

const deleteLote = (leilaoId, loteId) => {
    if (confirm('Tem certeza que deseja excluir este lote?')) {
        router.delete(route('admin.leiloes.lotes.destroy', { leilao: leilaoId, lote: loteId }));
    }
};

// Lances (modal)
const bidsModalOpen = ref(false);
const bidsLoading = ref(false);
const bidsError = ref('');
const bids = ref([]);
const selectedLote = ref(null);

const openBids = async (lote) => {
    selectedLote.value = lote;
    bidsModalOpen.value = true;
    bidsLoading.value = true;
    bidsError.value = '';
    try {
        const leilaoId = (typeof props.leilao?.id !== 'undefined' && props.leilao?.id) ? props.leilao.id : lote.leilao_id;
        const url = route('admin.leiloes.lotes.lances.index', [leilaoId, lote.id]);
        const { data } = await axios.get(url);
        const arr = data.lances || data || [];
        const ts = (d) => {
            const t = new Date(d).getTime();
            return isNaN(t) ? 0 : t;
        };
        bids.value = [...arr].sort((a, b) => {
            const v = Number(b.valor) - Number(a.valor);
            if (v !== 0) return v;
            return ts(b.created_at) - ts(a.created_at);
        });
    } catch (e) {
        bidsError.value = 'Falha ao carregar lances.';
    } finally {
        bidsLoading.value = false;
    }
};

const closeBids = () => {
    bidsModalOpen.value = false;
    bids.value = [];
    selectedLote.value = null;
};

const deleteBid = async (lanceId) => {
    if (!selectedLote.value) return;
    const confirmed = window.confirm('Tem certeza que deseja excluir este lance? Esta ação não pode ser desfeita.');
    if (!confirmed) return;
    try {
        const leilaoId = (typeof props.leilao?.id !== 'undefined' && props.leilao?.id) ? props.leilao.id : selectedLote.value.leilao_id;
        const url = route('admin.leiloes.lotes.lances.destroy', [leilaoId, selectedLote.value.id, lanceId]);
        await axios.delete(url);
        bids.value = bids.value.filter(b => b.id !== lanceId);
    } catch (e) {
        alert('Não foi possível excluir o lance.');
    }
};
</script>

<template>
    <Head title="Gerenciar Lotes" />

    <AdminLayout>
        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <div class="flex items-center gap-4">
                    <!-- Back button if filtered -->
                    <Link v-if="leilao" :href="route('admin.leiloes.index')" class="btn-sm bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">
                        <ArrowLeft class="w-4 h-4 mr-1" />
                        Voltar
                    </Link>
                    
                    <div>
                        <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                            {{ leilao?.titulo ? `Lotes: ${leilao.titulo}` : 'Todos os Lotes' }}
                        </h1>
                        <p v-if="leilao" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Gerenciando lotes deste leilão específico
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <!-- Delete button -->
                <button class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-red-500">
                    <Trash2 class="w-4 h-4 shrink-0" />
                    <span class="hidden xs:block ml-2">Apagar</span>
                </button>

                <!-- Filter button -->
                <DropdownFilter align="right" />

                <!-- Add view button (Only if filtered by Leilao) -->
                <Link v-if="leilao" :href="route('admin.leiloes.lotes.create', leilao.id)" class="btn bg-violet-500 hover:bg-violet-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    <span class="hidden xs:block ml-2">Novo Lote</span>
                </Link>
            </div>
        </div>

        <!-- Cards -->
        <div class="flex flex-col space-y-4">
            <div v-for="lote in lotes.data" :key="lote.id" class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700/60 p-5 transition-shadow hover:shadow-md">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    
                    <!-- Left: Info -->
                    <div class="flex items-start gap-4 grow">
                        <!-- Checkbox -->
                        <div class="flex items-center h-full mt-3 sm:mt-0">
                            <input type="checkbox" class="form-checkbox" />
                        </div>

                        <!-- Icon / Visual Indicator -->
                        <div class="shrink-0 mt-1">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 overflow-hidden">
                                <!-- Image or Placeholder -->
                                <img v-if="lote.foto_capa" :src="`/storage/${lote.foto_capa}`" class="w-full h-full object-cover" :alt="lote.titulo" />
                                <ImageIcon v-else class="w-6 h-6" />
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="grow">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2">
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                    <span class="mr-2 text-gray-400">#{{ lote.ordem }}</span>
                                    {{ lote.titulo }}
                                </h2>
                                <span :class="phaseClass(lotePhase(lote))" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize mt-2 sm:mt-0 self-start sm:self-auto">
                                    {{ lotePhase(lote) }}
                                </span>
                            </div>

                            <div class="flex flex-wrap gap-y-2 gap-x-6 text-sm text-gray-500 dark:text-gray-400">
                                <!-- Leilao Name (if listing all) -->
                                <div v-if="!leilao && lote.leilao" class="flex items-center">
                                    <Box class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" />
                                    <span class="font-medium">{{ lote.leilao?.titulo || 'Leilão Indefinido' }}</span>
                                </div>

                                <!-- Value -->
                                <div class="flex items-center">
                                    <DollarSign class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" />
                                    <span>Inicial: {{ formatCurrency(lote.valor_inicial) }}</span>
                                </div>

                                <!-- New Fields -->
                                <div v-if="lote.ano" class="flex items-center px-2 py-1 bg-gray-50 dark:bg-gray-700/50 rounded text-xs">
                                    <span class="font-medium text-gray-500">Ano: {{ lote.ano }}</span>
                                </div>
                                <div v-if="lote.combustivel" class="flex items-center px-2 py-1 bg-gray-50 dark:bg-gray-700/50 rounded text-xs">
                                    <span class="font-medium text-gray-500">{{ lote.combustivel }}</span>
                                </div>
                                <div v-if="lote.quilometragem" class="flex items-center px-2 py-1 bg-gray-50 dark:bg-gray-700/50 rounded text-xs">
                                    <span class="font-medium text-gray-500">{{ lote.quilometragem }} km</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Actions -->
                    <CardActionGroup>
                        <template #main>
                            <ActionButton :to="route('admin.leiloes.lotes.edit', { leilao: lote.leilao_id, lote: lote.id })" label="Editar" variant="primary" size="sm">
                                <Edit class="w-4 h-4" />
                            </ActionButton>
                        </template>
                        <template #neutral>
                            <CopyLink :url="route('lotes.show', lote.id)" label="Copiar link" :showLabel="true" variant="neutral" btnSize="sm" />
                            <ActionButton label="Ver lances" variant="neutral" size="sm" @click="openBids(lote)">
                                <Eye class="w-4 h-4" />
                            </ActionButton>
                        </template>
                        <template #danger>
                            <ActionButton label="Excluir" variant="danger" size="sm" @click="deleteLote(lote.leilao_id, lote.id)">
                                <Trash2 class="w-4 h-4" />
                            </ActionButton>
                        </template>
                    </CardActionGroup>

            </div>
                </div>

            <!-- Modal de Lances -->
            <transition enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0">
              <div v-if="bidsModalOpen" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6">
                <div class="fixed inset-0 bg-black/50" @click="closeBids"></div>
                <div class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden">
                  <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                      <h3 class="text-lg font-bold text-gray-900">Lances do Lote</h3>
                      <p class="text-sm text-gray-500 mt-0.5" v-if="selectedLote">#{{ selectedLote.ordem }} • {{ selectedLote.titulo }}</p>
                    </div>
                    <button @click="closeBids" class="px-2 py-1 rounded hover:bg-gray-100 text-gray-500">Fechar</button>
                  </div>
                  <div class="max-h-[70vh] overflow-y-auto divide-y divide-gray-100">
                    <div v-if="bidsLoading" class="p-6 text-sm text-gray-500">Carregando lances…</div>
                    <div v-else-if="bidsError" class="p-6 text-sm text-red-600">{{ bidsError }}</div>
                    <div v-else>
                      <div v-if="bids.length === 0" class="p-6 text-sm text-gray-500">Nenhum lance cadastrado para este lote.</div>
                      <div v-for="(b, i) in bids" :key="b.id" class="px-5 py-4 flex items-center justify-between">
                        <div class="min-w-0">
                          <div class="text-sm font-medium text-gray-900 flex items-center gap-2">
                            {{ formatCurrency(b.valor) }}
                            <span v-if="i === 0" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium border bg-emerald-100 text-emerald-700 border-emerald-200">
                              Líder
                            </span>
                          </div>
                          <div class="text-xs text-gray-500 flex items-center gap-1.5">
                            <span
                              class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium border"
                              :class="b.is_fake ? 'bg-red-100 text-red-700 border-red-200' : 'bg-green-100 text-green-700 border-green-200'"
                            >
                              {{ b.is_fake ? 'FALSO' : 'VERDADEIRO' }}
                            </span>
                            <span v-if="b.usuario" class="mx-1">•</span>
                            <span v-if="b.usuario">{{ b.usuario }}</span>
                            <span v-if="b.created_at" class="mx-1">•</span>
                            <span v-if="b.created_at">{{ new Date(b.created_at).toLocaleString('pt-BR') }}</span>
                          </div>
                        </div>
                        <button @click="deleteBid(b.id)" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-md font-medium shadow-sm transition-colors border bg-red-600 hover:bg-red-700 text-white border-red-600">
                          <Trash2 class="w-4 h-4" />
                          <span>Excluir</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </transition>

            <!-- Empty State -->
            <div v-if="lotes.data.length === 0" class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                    <Box class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">Nenhum lote encontrado</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    {{ leilao ? 'Este leilão ainda não possui lotes cadastrados.' : 'Não existem lotes cadastrados.' }}
                </p>
                <Link v-if="leilao" :href="route('admin.leiloes.lotes.create', leilao.id)" class="btn bg-violet-500 hover:bg-violet-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0 mr-2" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    Novo Lote
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="lotes.data.length > 0" class="mt-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <nav class="mb-4 sm:mb-0 sm:order-1" role="navigation" aria-label="Navigation">
                    <ul class="flex justify-center">
                        <li class="ml-3 first:ml-0">
                            <Link v-if="lotes.prev_page_url" :href="lotes.prev_page_url" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-violet-500">
                                &lt;- Anterior
                            </Link>
                            <span v-else class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 text-gray-300 cursor-not-allowed">
                                &lt;- Anterior
                            </span>
                        </li>
                        <li class="ml-3 first:ml-0">
                            <Link v-if="lotes.next_page_url" :href="lotes.next_page_url" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-violet-500">
                                Próximo -&gt;
                            </Link>
                            <span v-else class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 text-gray-300 cursor-not-allowed">
                                Próximo -&gt;
                            </span>
                        </li>
                    </ul>
                </nav>
                <div class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
                    Mostrando <span class="font-medium text-gray-800 dark:text-gray-100">{{ lotes.from }}</span> a <span class="font-medium text-gray-800 dark:text-gray-100">{{ lotes.to }}</span> de <span class="font-medium text-gray-800 dark:text-gray-100">{{ lotes.total }}</span> resultados
                </div>
            </div>
        </div>

    </AdminLayout>
</template>
