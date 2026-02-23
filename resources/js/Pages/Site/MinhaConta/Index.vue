<script setup>
import { ref, onMounted, onUnmounted, defineAsyncComponent } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
const ProfileForm = defineAsyncComponent({
    loader: () => import('@/Components/User/ProfileForm.vue'),
    delay: 200,
    timeout: 20000,
});
import { 
    LayoutDashboard, 
    Gavel, 
    ShoppingBag, 
    User, 
    LogOut, 
    Bell, 
    Search,
    ChevronRight,
    TrendingUp,
    Award,
    Clock,
    FileText
} from 'lucide-vue-next';

const props = defineProps({
    user: Object,
    lances: Object,
    compras: Object,
    stats: Object,
    pending_terms: Number
});

const activeTab = ref('dashboard');
const mobileMenuOpen = ref(false);

const tabs = [
    { id: 'dashboard', label: 'Dashboard', icon: LayoutDashboard },
    { id: 'lances', label: 'Meus Lances', icon: Gavel },
    { id: 'compras', label: 'Minhas Compras', icon: ShoppingBag },
    { id: 'perfil', label: 'Perfil', icon: User },
];

const formatDate = (date) => {
    return new Intl.DateTimeFormat('pt-BR', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(date));
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

// Simple polling for real-time updates (every 15s)
let pollInterval;
onMounted(() => {
    pollInterval = setInterval(() => {
        router.reload({ only: ['lances', 'compras', 'stats'] });
    }, 15000);
    // Garantia de aba inicial visível
    activeTab.value = 'dashboard';
});

onUnmounted(() => {
    clearInterval(pollInterval);
});

const getStatusColor = (status) => {
    switch(status) {
        case 'ativo': return 'bg-green-100 text-green-800';
        case 'vendido': return 'bg-blue-100 text-blue-800';
        case 'finalizado': return 'bg-gray-100 text-gray-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head title="Minha Conta" />

    <SiteLayout>
        <div class="min-h-screen bg-gray-50">
            <div class="max-w-[1400px] mx-auto">
                <div class="flex flex-col lg:flex-row min-h-[calc(100vh-80px)]">
                    
                    <!-- Sidebar Navigation -->
                    <aside class="w-full lg:w-72 bg-white border-r border-gray-200 flex-shrink-0">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-[#002f6c] text-white flex items-center justify-center font-bold text-xl">
                                    {{ (user.nome || 'U').charAt(0).toUpperCase() }}
                                </div>
                                <div class="overflow-hidden">
                                    <h2 class="font-bold text-[#002f6c] truncate">{{ user.nome }}</h2>
                                    <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                                </div>
                            </div>
                        </div>

                        <nav class="p-4 space-y-1">
                            <button 
                                v-for="tab in tabs" 
                                :key="tab.id"
                                @click="activeTab = tab.id"
                                :class="[
                                    'w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all',
                                    activeTab === tab.id 
                                        ? 'bg-[#002f6c] text-white shadow-md' 
                                        : 'text-gray-600 hover:bg-gray-100 hover:text-[#002f6c]'
                                ]"
                            >
                                <component :is="tab.icon" class="w-5 h-5" />
                                {{ tab.label }}
                            </button>

                            <div class="pt-4 mt-4 border-t border-gray-100">
                                <div class="space-y-1 mb-4">
                                    <Link 
                                        :href="route('minha-conta.documentos')" 
                                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                                    >
                                        <FileText class="w-5 h-5" />
                                        Verificação de Conta
                                    </Link>
                                    <Link 
                                        :href="route('minha-conta.meus-documentos')" 
                                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                                    >
                                        <FileText class="w-5 h-5" />
                                        Meus Documentos
                                    </Link>
                                </div>
                                <Link 
                                    :href="route('logout')" 
                                    method="post" 
                                    as="button" 
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                >
                                    <LogOut class="w-5 h-5" />
                                    Sair da Conta
                                </Link>
                            </div>
                        </nav>
                    </aside>

                    <!-- Main Content -->
                    <main class="flex-1 p-4 lg:p-8 overflow-y-auto">
                        
                        <!-- Header Mobile (Only Title) -->
                        <div class="lg:hidden mb-6 flex items-center justify-between">
                            <h1 class="text-2xl font-bold text-[#002f6c]">{{ tabs.find(t => t.id === activeTab)?.label }}</h1>
                        </div>

                        <!-- Dashboard View -->
                        <div v-if="activeTab === 'dashboard'" class="space-y-6">
                            <!-- Welcome Banner -->
                            <div class="bg-gradient-to-r from-[#002f6c] to-[#004090] rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
                                <div class="relative z-10">
                                    <h1 class="text-2xl md:text-3xl font-bold mb-2">Olá, {{ (user.nome || '').split(' ')[0] || 'Usuário' }}! 👋</h1>
                                    <p class="text-blue-100">Bem-vindo ao seu painel de leilões. Acompanhe seus lances e oportunidades em tempo real.</p>
                                </div>
                                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/5 skew-x-12 transform translate-x-12"></div>
                            </div>

                            <!-- Alert: Pending Terms -->
                            <div v-if="pending_terms > 0" class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <Bell class="w-5 h-5" />
                                    <div>
                                        <p class="font-medium">Você possui {{ pending_terms }} termo(s) pendente(s) de aceite.</p>
                                        <p class="text-sm">Abra o documento, leia atentamente e aceite para liberar o download.</p>
                                    </div>
                                </div>
                                <Link :href="route('cliente.termos.index')" class="px-4 py-2 rounded bg-amber-600 text-white hover:bg-amber-700">
                                    Ver Termos
                                </Link>
                            </div>

                            

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                                    <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                                        <TrendingUp class="w-8 h-8" />
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium">Lances em Leilões Ativos</p>
                                        <h3 class="text-2xl font-bold text-[#002f6c]">{{ (stats && stats.active_bids) || 0 }}</h3>
                                    </div>
                                </div>

                                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                                    <div class="p-3 bg-green-50 rounded-lg text-green-600">
                                        <Award class="w-8 h-8" />
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium">Lotes Arrematados</p>
                                        <h3 class="text-2xl font-bold text-[#002f6c]">{{ (stats && stats.won_lots) || 0 }}</h3>
                                    </div>
                                </div>

                                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                                    <div class="p-3 bg-purple-50 rounded-lg text-purple-600">
                                        <Gavel class="w-8 h-8" />
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium">Total de Lances</p>
                                        <h3 class="text-2xl font-bold text-[#002f6c]">{{ (stats && stats.total_bids) || 0 }}</h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                                    <h3 class="font-bold text-lg text-[#002f6c]">Últimos Lances</h3>
                                    <button @click="activeTab = 'lances'" class="text-sm text-[#00a550] font-medium hover:underline">Ver todos</button>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-gray-50 text-gray-500 font-medium">
                                            <tr>
                                                <th class="px-6 py-3">Lote</th>
                                                <th class="px-6 py-3">Leilão</th>
                                                <th class="px-6 py-3">Seu Lance</th>
                                                <th class="px-6 py-3">Maior Lance Atual</th>
                                                <th class="px-6 py-3">Data</th>
                                                <th class="px-6 py-3">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <tr v-for="lance in (lances && lances.data ? lances.data.slice(0, 5) : [])" :key="lance.id" class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 font-medium text-[#002f6c]">
                                                    <Link :href="route('lotes.show', lance.lote.id)">
                                                        {{ lance.lote.titulo }}
                                                    </Link>
                                                </td>
                                                <td class="px-6 py-4 text-gray-600">{{ lance.lote.leilao.titulo }}</td>
                                                <td class="px-6 py-4 font-bold text-[#00a550]">{{ formatCurrency(lance.valor) }}</td>
                                                <td class="px-6 py-4 font-bold text-gray-700">
                                                    {{ lance.lote.maior_lance ? formatCurrency(lance.lote.maior_lance.valor) : '-' }}
                                                </td>
                                                <td class="px-6 py-4 text-gray-500">{{ formatDate(lance.created_at) }}</td>
                                                <td class="px-6 py-4">
                                                    <span :class="['px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusColor(lance.lote.status)]">
                                                        {{ lance.lote.status.toUpperCase() }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr v-if="(lances && lances.data ? lances.data.length : 0) === 0">
                                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                                    Nenhum lance registrado recentemente.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Meus Lances View -->
                        <div v-if="activeTab === 'lances'" class="space-y-6">
                            <h2 class="text-2xl font-bold text-[#002f6c]">Histórico de Lances</h2>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-gray-50 text-gray-500 font-medium">
                                            <tr>
                                                <th class="px-6 py-3">Lote</th>
                                                <th class="px-6 py-3">Leilão</th>
                                                <th class="px-6 py-3">Seu Lance</th>
                                                <th class="px-6 py-3">Maior Lance Atual</th>
                                                <th class="px-6 py-3">Data</th>
                                                <th class="px-6 py-3">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <tr v-for="lance in (lances && lances.data ? lances.data : [])" :key="lance.id" class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 font-medium text-[#002f6c]">
                                                    {{ lance.lote.titulo }}
                                                </td>
                                                <td class="px-6 py-4 text-gray-600">{{ lance.lote.leilao.titulo }}</td>
                                                <td class="px-6 py-4 font-bold text-[#00a550]">{{ formatCurrency(lance.valor) }}</td>
                                                <td class="px-6 py-4 font-bold text-gray-700">
                                                    {{ lance.lote.maior_lance ? formatCurrency(lance.lote.maior_lance.valor) : '-' }}
                                                </td>
                                                <td class="px-6 py-4 text-gray-500">{{ formatDate(lance.created_at) }}</td>
                                                <td class="px-6 py-4">
                                                    <Link :href="route('lotes.show', lance.lote.id)" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                        Ver Lote
                                                    </Link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Simple Pagination (Next/Prev) if needed, using Links from Inertia -->
                                <div v-if="lances && lances.links && lances.links.length > 3" class="p-4 border-t border-gray-100 flex justify-center">
                                    <!-- Simplified for now -->
                                    <span class="text-sm text-gray-500">Mostrando {{ lances && lances.data ? lances.data.length : 0 }} registros</span>
                                </div>
                            </div>
                        </div>

                        <!-- Minhas Compras View -->
                        <div v-if="activeTab === 'compras'" class="space-y-6">
                            <h2 class="text-2xl font-bold text-[#002f6c]">Minhas Compras</h2>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-gray-50 text-gray-500 font-medium">
                                            <tr>
                                                <th class="px-6 py-3">Lote</th>
                                                <th class="px-6 py-3">Modalidade</th>
                                                <th class="px-6 py-3">Valor Pago</th>
                                                <th class="px-6 py-3">Data</th>
                                                <th class="px-6 py-3">Status</th>
                                                <th class="px-6 py-3">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <tr v-for="item in (compras && compras.data ? compras.data : [])" :key="item.id" class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <img v-if="item.foto_capa || item.foto" :src="item.foto_capa || item.foto" alt="" class="w-12 h-12 object-cover rounded" />
                                                        <div>
                                                            <p class="font-medium text-[#002f6c] truncate">{{ item.titulo }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-gray-600">Venda Direta</td>
                                                <td class="px-6 py-4 font-bold text-[#00a550]">{{ formatCurrency(item.valor_compra || item.valor) }}</td>
                                                <td class="px-6 py-4 text-gray-500">{{ item.comprado_em || '-' }}</td>
                                                <td class="px-6 py-4">
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">COMPRADO</span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <Link :href="route('lotes.show', item.id)" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                        Ver Lote
                                                    </Link>
                                                </td>
                                            </tr>
                                            <tr v-if="!compras.data || compras.data.length === 0">
                                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                                    Você ainda não realizou compras.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-if="compras.links && compras.links.length > 3" class="p-4 border-t border-gray-100 flex justify-center">
                                    <span class="text-sm text-gray-500">Mostrando {{ compras.data.length }} registros</span>
                                </div>
                            </div>
                        </div>

                        <!-- Perfil View -->
                        <div v-if="activeTab === 'perfil'" class="space-y-6">
                            <h2 class="text-2xl font-bold text-[#002f6c]">Meu Perfil</h2>
                            <ProfileForm 
                                mode="user"
                                :user="user"
                                :submit-url="route('minha-conta.perfil.update')"
                                :password-url="route('minha-conta.senha.update')"
                            />
                        </div>

                    </main>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>
