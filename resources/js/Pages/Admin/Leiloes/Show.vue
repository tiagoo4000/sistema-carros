<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Plus, Edit, Trash2, Share2, List } from 'lucide-vue-next';
import CopyLink from '@/Components/Common/CopyLink.vue';
import ActionButton from '@/Components/Admin/ActionButton.vue';
import CardActionGroup from '@/Components/Admin/CardActionGroup.vue';
import axios from 'axios';

const props = defineProps({
    leilao: Object
});

const formatDate = (date) => {
    return new Intl.DateTimeFormat('pt-BR').format(new Date(date));
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

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
        const url = route('admin.leiloes.lotes.lances.index', [props.leilao.id, lote.id]);
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
        const url = route('admin.leiloes.lotes.lances.destroy', [props.leilao.id, selectedLote.value.id, lanceId]);
        await axios.delete(url);
        // Atualiza lista local
        bids.value = bids.value.filter(b => b.id !== lanceId);
    } catch (e) {
        alert('Não foi possível excluir o lance.');
    }
};
</script>

<template>
    <Head :title="leilao.titulo" />

    <AdminLayout>
        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ leilao.titulo }}</h1>
                <p class="mt-2 text-sm text-slate-700">
                    {{ leilao.status.toUpperCase() }} • {{ formatDate(leilao.data_inicio) }} - {{ formatDate(leilao.data_fim) }}
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <Link :href="route('admin.leiloes.edit', leilao.id)" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 sm:w-auto">
                    <Edit class="mr-2 h-4 w-4" />
                    Editar Leilão
                </Link>
                <Link :href="route('admin.leiloes.lotes.create', leilao.id)" class="inline-flex items-center justify-center rounded-md border border-transparent bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 sm:w-auto">
                    <Plus class="mr-2 h-4 w-4" />
                    Adicionar Lote
                </Link>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-slate-900">Detalhes do Leilão</h3>
                <p class="mt-1 max-w-2xl text-sm text-slate-500">Informações gerais e configurações.</p>
            </div>
            <div class="border-t border-slate-200">
                <dl>
                    <div class="bg-slate-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-slate-500">Local</dt>
                        <dd class="mt-1 text-sm text-slate-900 sm:mt-0 sm:col-span-2">{{ leilao.local }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-slate-500">Descrição</dt>
                        <dd class="mt-1 text-sm text-slate-900 sm:mt-0 sm:col-span-2">{{ leilao.descricao }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-xl font-bold text-slate-900 mb-4">Lotes ({{ leilao.lotes.length }})</h2>
            
            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                <ul role="list" class="divide-y divide-slate-200">
                    <li v-for="lote in leilao.lotes" :key="lote.id" class="px-4 py-4 sm:px-6 hover:bg-slate-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center min-w-0">
                                <div class="flex-shrink-0 h-12 w-12 bg-slate-200 rounded-md flex items-center justify-center">
                                    <span class="text-xs font-bold text-slate-500">#{{ lote.ordem }}</span>
                                </div>
                                <div class="ml-4 truncate">
                                    <div class="flex text-sm">
                                        <p class="font-medium text-indigo-600 truncate">{{ lote.titulo }}</p>
                                        <p class="ml-1 flex-shrink-0 font-normal text-slate-500">
                                            ({{ formatCurrency(lote.valor_inicial) }})
                                        </p>
                                    </div>
                                    <div class="mt-2 flex">
                                        <div class="flex items-center text-sm text-slate-500">
                                            <p class="truncate">{{ lote.descricao }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex flex-col sm:flex-row gap-2 items-stretch justify-end w-full sm:w-auto">
                                <span :class="{
                                    'bg-green-100 text-green-800': lote.status === 'aberto',
                                    'bg-red-100 text-red-800': lote.status === 'arrematado',
                                    'bg-gray-100 text-gray-800': lote.status === 'sem_lances'
                                }" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                                    {{ lote.status.toUpperCase() }}
                                </span>

                                <CardActionGroup>
                                  <template #main>
                                    <ActionButton :to="route('admin.leiloes.lotes.edit', [leilao.id, lote.id])" label="Editar" variant="primary" size="sm">
                                      <Edit class="w-4 h-4" />
                                    </ActionButton>
                                  </template>
                                  <template #neutral>
                                    <CopyLink :url="route('lotes.show', lote.id)" label="Copiar link" :showLabel="true" variant="neutral" btnSize="sm" />
                                    <ActionButton label="Ver lances" variant="neutral" size="sm" @click="openBids(lote)">
                                      <List class="w-4 h-4" />
                                    </ActionButton>
                                  </template>
                                  <template #danger>
                                    <ActionButton label="Excluir" variant="danger" size="sm">
                                      <Trash2 class="w-4 h-4" />
                                    </ActionButton>
                                  </template>
                                </CardActionGroup>
                            </div>
                        </div>
                    </li>
                    <li v-if="leilao.lotes.length === 0" class="px-4 py-8 text-center text-slate-500">
                        Nenhum lote cadastrado neste leilão.
                    </li>
                </ul>
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
        </div>
    </AdminLayout>
</template>
