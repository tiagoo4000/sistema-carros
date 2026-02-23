<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DropdownFilter from '@/Components/Mosaic/Components/DropdownFilter.vue';
import { Edit, Trash2, Calendar, Box, Gavel, Eraser, X, Power } from 'lucide-vue-next';
import ActionButton from '@/Components/Admin/ActionButton.vue';
import CardActionGroup from '@/Components/Admin/CardActionGroup.vue';

const props = defineProps({
    leiloes: Object
});

const modalOpen = ref(false);
const modalType = ref('');
const selectedLeilao = ref(null);

const actionForm = useForm({
    quantidade: 10,
    distribuicao: 'igual'
});

const openActionModal = (type, leilao) => {
    if (!leilao.lotes_count || leilao.lotes_count === 0) {
        alert('Este leilão não possui lotes.');
        return;
    }
    
    selectedLeilao.value = leilao;
    modalType.value = type;
    modalOpen.value = true;
    
    if (type === 'lances') {
        actionForm.quantidade = 10;
        actionForm.distribuicao = 'igual';
    }
};

const closeModal = () => {
    modalOpen.value = false;
    selectedLeilao.value = null;
    actionForm.reset();
};

const submitAction = () => {
    if (!selectedLeilao.value) return;

    actionForm.post(route('admin.leiloes.gerar-lances', selectedLeilao.value.id), {
        onSuccess: () => closeModal(),
        preserveScroll: true
    });
};

const clearBids = (leilao) => {
    if (!leilao.lotes_count || leilao.lotes_count === 0) return;
    
    if (confirm('ATENÇÃO: Isso removerá TODOS os lances de todos os lotes deste leilão. Deseja continuar?')) {
        router.post(route('admin.leiloes.limpar-lances', leilao.id), {}, {
            preserveScroll: true
        });
    }
};

const formatDate = (date) => {
    if (!date) return '';
    return new Intl.DateTimeFormat('pt-BR').format(new Date(date));
};

const leilaoPhase = (leilao) => {
    const t = new Date(nowTs.value);
    const start = leilao.data_inicio ? new Date(leilao.data_inicio) : null;
    const end = leilao.data_fim ? new Date(leilao.data_fim) : null;
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

// tick para reatividade temporal (atualiza chips/contadores sem refresh)
const nowTs = ref(Date.now());
let tickId;
onMounted(() => {
    tickId = setInterval(() => { nowTs.value = Date.now(); }, 1000);
});
onUnmounted(() => {
    if (tickId) clearInterval(tickId);
});


const deleteLeilao = (id) => {
    if (confirm('Tem certeza que deseja excluir este leilão?')) {
        router.delete(route('admin.leiloes.destroy', id));
    }
};

const toggleStatus = (leilao) => {
    if (confirm(`Deseja alterar o status do leilão "${leilao.titulo}"?`)) {
        router.put(route('admin.leiloes.toggle-status', leilao.id), {}, {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Gerenciar Leilões" />

    <AdminLayout>
        <!-- Page header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Leilões</h1>
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

                <!-- Add view button -->
                <Link :href="route('admin.leiloes.create')" class="btn bg-violet-500 hover:bg-violet-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    <span class="hidden xs:block ml-2">Novo Leilão</span>
                </Link>
            </div>
        </div>

        <!-- Cards -->
        <div class="flex flex-col space-y-4">
            <div v-for="leilao in leiloes.data" :key="leilao.id" class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700/60 p-5 transition-shadow hover:shadow-md">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    
                    <!-- Left: Info -->
                    <div class="flex items-start gap-4 grow">
                        <!-- Checkbox -->
                        <div class="flex items-center h-full mt-3 sm:mt-0">
                            <input type="checkbox" class="form-checkbox" />
                        </div>

                        <!-- Icon / Visual Indicator -->
                        <div class="shrink-0 mt-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                <span class="font-bold text-sm">#{{ leilao.id }}</span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="grow">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2">
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                    <Link :href="route('admin.leiloes.show', leilao.id)" class="hover:text-violet-500 transition-colors">
                                        {{ leilao.titulo }}
                                    </Link>
                                </h2>
                                <span :class="phaseClass(leilaoPhase(leilao))" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize mt-2 sm:mt-0 self-start sm:self-auto">
                                    {{ leilaoPhase(leilao) }}
                                </span>
                            </div>

                            <div class="flex flex-wrap gap-y-2 gap-x-6 text-sm text-gray-500 dark:text-gray-400">
                                <!-- Dates -->
                                <div class="flex items-center">
                                    <Calendar class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" />
                                    <span>{{ formatDate(leilao.data_inicio) }} - {{ formatDate(leilao.data_fim) }}</span>
                                </div>
                                
                                <!-- Lotes Count -->
                                <div class="flex items-center">
                                    <Box class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" />
                                    <span>{{ leilao.lotes_count || 0 }} Lotes</span>
                                </div>

                                <!-- Local -->
                                <div class="flex items-center" v-if="leilao.local">
                                    <span class="text-gray-400 dark:text-gray-500 mr-2">Local:</span>
                                    <span>{{ leilao.local }}</span>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Right: Actions -->
                    <CardActionGroup>
                      <template #main>
                        <ActionButton :to="route('admin.leiloes.edit', leilao.id)" size="sm" variant="primary" label="Editar">
                          <Edit class="w-4 h-4" />
                        </ActionButton>
                      </template>
                      <template #neutral>
                        <ActionButton size="sm" variant="neutral" label="Gerar lances" :disabled="!leilao.lotes_count" @click="openActionModal('lances', leilao)">
                          <Gavel class="w-4 h-4" />
                        </ActionButton>
                        <ActionButton size="sm" variant="neutral" :label="leilao.status === 'ativo' ? 'Encerrar' : 'Reabrir'" @click="toggleStatus(leilao)">
                          <Power class="w-4 h-4" />
                        </ActionButton>
                        <Link :href="route('admin.lotes.index', { leilao_id: leilao.id })" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs rounded-md bg-white hover:bg-gray-50 text-gray-700 border border-gray-300">
                          <Box class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                          Lotes
                        </Link>
                      </template>
                      <template #danger>
                        <ActionButton size="sm" variant="danger" label="Limpar lances" :disabled="!leilao.lotes_count" @click="clearBids(leilao)">
                          <Eraser class="w-4 h-4" />
                        </ActionButton>
                        <ActionButton size="sm" variant="danger" label="Excluir" @click="deleteLeilao(leilao.id)">
                          <Trash2 class="w-4 h-4" />
                        </ActionButton>
                      </template>
                    </CardActionGroup>

                </div>
            </div>

            <!-- Empty State -->
            <div v-if="leiloes.data.length === 0" class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                    <Box class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">Nenhum leilão encontrado</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Comece criando um novo leilão para gerenciar seus lotes.</p>
                <Link :href="route('admin.leiloes.create')" class="btn bg-violet-500 hover:bg-violet-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0 mr-2" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    Novo Leilão
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="leiloes.data.length > 0" class="mt-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <nav class="mb-4 sm:mb-0 sm:order-1" role="navigation" aria-label="Navigation">
                    <ul class="flex justify-center">
                        <li class="ml-3 first:ml-0">
                            <Link v-if="leiloes.prev_page_url" :href="leiloes.prev_page_url" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-violet-500">
                                &lt;- Anterior
                            </Link>
                            <span v-else class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 text-gray-300 cursor-not-allowed">
                                &lt;- Anterior
                            </span>
                        </li>
                        <li class="ml-3 first:ml-0">
                            <Link v-if="leiloes.next_page_url" :href="leiloes.next_page_url" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-violet-500">
                                Próximo -&gt;
                            </Link>
                            <span v-else class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 text-gray-300 cursor-not-allowed">
                                Próximo -&gt;
                            </span>
                        </li>
                    </ul>
                </nav>
                <div class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
                    Mostrando <span class="font-medium text-gray-800 dark:text-gray-100">{{ leiloes.from }}</span> a <span class="font-medium text-gray-800 dark:text-gray-100">{{ leiloes.to }}</span> de <span class="font-medium text-gray-800 dark:text-gray-100">{{ leiloes.total }}</span> resultados
                </div>
            </div>
        </div>

        <!-- Action Modal -->
        <div v-if="modalOpen" class="fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity" aria-hidden="true" @click="closeModal"></div>
        <div v-if="modalOpen" class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center px-4 sm:px-6">
            <div class="bg-white dark:bg-gray-800 rounded shadow-lg overflow-auto max-w-lg w-full max-h-full" role="dialog" aria-modal="true">
                <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div class="font-semibold text-gray-800 dark:text-gray-100">Gerar Lances Simulados</div>
                        <button class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400" @click="closeModal">
                            <div class="sr-only">Close</div>
                            <X class="w-4 h-4 fill-current" />
                        </button>
                    </div>
                </div>
                <div class="px-5 py-4">
                    <form @submit.prevent="submitAction">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-800 dark:text-gray-100" for="qty">Quantidade</label>
                                <input id="qty" v-model="actionForm.quantidade" class="form-input w-full px-2 py-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded" type="number" min="1" max="50" />
                                <div class="text-xs text-gray-500 mt-1">Máximo: 50 lances por vez.</div>
                            </div>
                            
                        </div>
                        <div class="flex justify-end mt-6 space-x-2">
                            <button type="button" class="btn-sm border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 text-gray-600 dark:text-gray-300" @click="closeModal">Cancelar</button>
                            <button type="submit" class="btn-sm bg-violet-500 hover:bg-violet-600 text-white" :disabled="actionForm.processing">
                                {{ actionForm.processing ? 'Gerando...' : 'Gerar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>
