<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Users, 
  Gavel, 
  FileText, 
  AlertCircle 
} from 'lucide-vue-next';

const props = defineProps({
    stats: Object
});

const showTrueBids = ref(false);
const showPendingDocs = ref(false);

function buildPendingMessage(missing = []) {
  const header = `🔔 Envio de documentos pendente

Olá! Notamos que seu cadastro ainda possui documentos pendentes de envio.`;
  const missingLine = (missing && missing.length)
    ? `\n\n*Documentos faltantes:* ${missing.join(', ')}`
    : '';
  const rest = `\n\nPara garantir sua participação nos leilões e evitar bloqueios na hora de ofertar lances, é necessário concluir essa etapa o quanto antes.
👉 Acesse sua conta, envie os documentos solicitados e aguarde a validação da nossa equipe.
Caso precise de ajuda, nosso suporte está disponível para te auxiliar.
Aguardamos sua regularização para que você possa participar normalmente dos leilões 🚀`;
  return header + missingLine + rest;
}

function whatsappUrlForUser(user) {
  const phone = user?.telefone;
  if (!phone) return null;
  const digits = String(phone).replace(/\D/g, '');
  let p = digits;
  if (!p.startsWith('55')) {
    if (p.length === 11) {
      p = '55' + p;
    } else if (p.length >= 12 && p.length <= 13) {
      p = p;
    } else {
      p = '55' + p;
    }
  }
  const text = encodeURIComponent(buildPendingMessage(user?.faltantes || []));
  return `https://api.whatsapp.com/send?phone=${p}&text=${text}`;
}
</script>

<template>
    <Head title="Dashboard Admin" />

    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800">Dashboard Administrativo</h1>
            <p class="text-slate-500">Bem-vindo ao painel de controle.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Card Leilões Ativos -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <Gavel class="h-6 w-6 text-slate-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-slate-500">Leilões Ativos</dt>
                                <dd>
                                    <div class="text-lg font-medium text-slate-900">{{ props.stats?.leiloes_ativos ?? 0 }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="#" class="font-medium text-emerald-600 hover:text-emerald-500">Ver todos</a>
                    </div>
                </div>
            </div>

            <!-- Card Usuários -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <Users class="h-6 w-6 text-slate-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-slate-500">Total Usuários</dt>
                                <dd>
                                    <div class="text-lg font-medium text-slate-900">{{ props.stats?.total_usuarios ?? 0 }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="#" class="font-medium text-emerald-600 hover:text-emerald-500">Gerenciar</a>
                    </div>
                </div>
            </div>

            <!-- Card Cadastros Hoje -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <Users class="h-6 w-6 text-emerald-500" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-slate-500">Cadastros Hoje</dt>
                                <dd>
                                    <div class="text-lg font-medium text-slate-900">{{ props.stats?.today_users ?? 0 }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-5 py-3">
                    <div class="text-sm text-slate-500">Atualizado diariamente</div>
                </div>
            </div>

            <!-- Card Leilões com Lances Verdadeiros -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <AlertCircle class="h-6 w-6 text-indigo-500" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-slate-500">Leilões com Lances Verdadeiros</dt>
                                <dd>
                                    <div class="text-lg font-medium text-slate-900">{{ (props.stats?.true_bid_auctions || []).length }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-5 py-3">
                    <div class="text-sm">
                        <button @click="showTrueBids = true" :disabled="(props.stats?.true_bid_auctions || []).length === 0" class="font-medium text-emerald-600 hover:text-emerald-500 disabled:text-slate-400 disabled:cursor-not-allowed">
                            Ver detalhes
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card Documentos Pendentes -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <FileText class="h-6 w-6 text-amber-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-slate-500">Docs Pendentes</dt>
                                <dd>
                                    <div class="text-lg font-medium text-slate-900">{{ props.stats?.pending_docs_count ?? 0 }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-5 py-3">
                    <div class="text-sm">
                        <button @click="showPendingDocs = true" :disabled="(props.stats?.pending_docs_count ?? 0) === 0" class="font-medium text-emerald-600 hover:text-emerald-500 disabled:text-slate-400 disabled:cursor-not-allowed">
                            Ver pendências
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal True Bids -->
        <div v-if="showTrueBids" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" @click="showTrueBids = false"></div>
            <div class="relative w-full max-w-3xl bg-white rounded-xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Lotes com Lances Verdadeiros</h3>
                    <button @click="showTrueBids = false" class="px-2 py-1 rounded hover:bg-gray-100 text-gray-500">Fechar</button>
                </div>
                <div class="max-h-[70vh] overflow-y-auto divide-y divide-gray-100">
                    <div v-if="(props.stats?.true_bid_auctions || []).length === 0" class="p-6 text-sm text-gray-500">
                        Nenhum leilão com lances verdadeiros encontrado.
                    </div>
                    <div v-for="item in (props.stats?.true_bid_auctions || [])" :key="item.leilao_id" class="px-6 py-4 flex items-center justify-between">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-gray-900">
                                {{ item.leilao_titulo }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ item.lotes_com_lances }} lotes com lances • {{ item.total_true_bids }} lances verdadeiros • Maior: {{ new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.maior_valor || 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Docs Pendentes -->
        <div v-if="showPendingDocs" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" @click="showPendingDocs = false"></div>
            <div class="relative w-full max-w-3xl bg-white rounded-xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Usuários com documentos pendentes</h3>
                    <button @click="showPendingDocs = false" class="px-2 py-1 rounded hover:bg-gray-100 text-gray-500">Fechar</button>
                </div>
                <div class="max-h-[70vh] overflow-y-auto divide-y divide-gray-100">
                    <div v-if="(props.stats?.pending_docs || []).length === 0" class="p-6 text-sm text-gray-500">
                        Nenhum usuário com pendências encontrado.
                    </div>
                    <div v-for="u in (props.stats?.pending_docs || [])" :key="u.id" class="px-6 py-4 flex items-center justify-between">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-gray-900">
                                {{ u.nome }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Faltando: {{ (u.faltantes || []).join(', ') }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a v-if="whatsappUrlForUser(u)" :href="whatsappUrlForUser(u)" target="_blank" rel="noopener" class="inline-flex items-center px-3 py-1.5 rounded bg-emerald-600 text-white hover:bg-emerald-700">
                                Enviar Mensagem para o WhatsApp
                            </a>
                            <span v-else class="text-xs text-gray-400">Sem telefone</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
