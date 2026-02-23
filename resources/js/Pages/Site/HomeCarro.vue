<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import CarLayout from '@/Layouts/CarLayout.vue';
import { 
    Search, 
    MapPin, 
    Calendar,
    Tag, 
    ArrowRight, 
    Heart,
    Clock,
    Car,
    Home,
    Truck,
    Bike,
    Smartphone,
    Monitor,
    Gavel,
    ChevronRight,
    ChevronLeft,
    Map
} from 'lucide-vue-next';

import { computed, ref, onMounted, onUnmounted, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import axios from 'axios';
import AuctionSection from '@/Components/Site/AuctionSection.vue';

const props = defineProps({
    leiloesAtivos: Array, // Now unused/null initially
    proximosLeiloes: Array, // Now unused/null initially
    banners: {
        type: Array,
        default: () => []
    },
    categories: {
        type: Array,
        default: () => []
    }
});

// Async Data State
const activeAuctions = ref([]);
const upcomingAuctions = ref([]);
const isLoading = ref(true);

const fetchHomeData = async () => {
    try {
        const response = await axios.get(route('api.home.data'));
        activeAuctions.value = response.data.leiloesAtivos || [];
        upcomingAuctions.value = response.data.proximosLeiloes || [];
    } catch (error) {
        console.error('Error fetching home data:', error);
    } finally {
        isLoading.value = false;
    }
};

// Search State
const searchQuery = ref('');
const suggestions = ref([]);
const showSuggestions = ref(false);
const selectedEstado = ref('Todos');
const selectedCondicao = ref('Todos');
const searchCount = ref(0);

// Carousel State
const currentHeroIndex = ref(0);

const heroBanners = computed(() => props.banners.filter(b => b.type === 'hero'));
const middleBanners = computed(() => props.banners.filter(b => b.type === 'middle'));
const footerBanners = computed(() => props.banners.filter(b => b.type === 'footer'));

// Carousel Logic
const nextSlide = () => {
    currentHeroIndex.value = (currentHeroIndex.value + 1) % heroBanners.value.length;
};

const prevSlide = () => {
    currentHeroIndex.value = (currentHeroIndex.value - 1 + heroBanners.value.length) % heroBanners.value.length;
};

const setHeroIndex = (index) => {
    currentHeroIndex.value = index;
};

// Search Logic
const fetchSuggestions = useDebounceFn(async () => {
    if (searchQuery.value.length < 2) {
        suggestions.value = [];
        showSuggestions.value = false;
        return;
    }
    try {
        const res = await axios.get(route('api.search.autocomplete'), { params: { query: searchQuery.value } });
        suggestions.value = res.data;
        showSuggestions.value = res.data.length > 0;
    } catch (e) {
        console.error(e);
    }
}, 300);

const fetchCount = useDebounceFn(async () => {
    try {
        const res = await axios.get(route('api.search.count'), { 
            params: { 
                query: searchQuery.value,
                estado: selectedEstado.value,
                condicao: selectedCondicao.value
            } 
        });
        searchCount.value = res.data.count;
    } catch (e) {
        console.error(e);
    }
}, 300);

const selectSuggestion = (item) => {
    searchQuery.value = item.value;
    showSuggestions.value = false;
    // Optional: Trigger search immediately? No, let user click button.
};

const handleSearch = () => {
    router.get(route('leiloes.index'), {
        q: searchQuery.value,
        estado: selectedEstado.value !== 'Todos' ? selectedEstado.value : null,
        condicao: selectedCondicao.value !== 'Todos' ? selectedCondicao.value : null,
    });
};

watch([searchQuery, selectedEstado, selectedCondicao], () => {
    fetchCount();
});

watch(searchQuery, () => {
    fetchSuggestions();
});

onMounted(async () => {
    // Detect State (Mock/Simple)
    // Try simple IP API or default to Todos
    // Skipping complex geo-ip to keep it simple/fast. 
    // If needed, use https://ipapi.co/json/
    
    fetchCount();
    fetchHomeData();
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }).format(date);
};

const getPrimeiroLote = (leilao) => {
    return leilao.lotes && leilao.lotes.length > 0 ? leilao.lotes[0] : null;
};
</script>

<template>
    <Head title="Home" />
    <CarLayout>
        <!-- Hero Section -->
        <div class="relative bg-gray-900 grid grid-cols-1">
            <!-- Carousel Container -->
            <template v-if="heroBanners.length > 0">
                <div v-for="(banner, index) in heroBanners" :key="banner.id" 
                     class="col-start-1 row-start-1 w-full transition-opacity duration-500 ease-in-out"
                     :class="{ 'opacity-100 z-10': index === currentHeroIndex, 'opacity-0 z-0': index !== currentHeroIndex }">
                    
                    <!-- Background Image -->
                    <img 
                        :src="banner.image_path" 
                        alt="Hero Background" 
                        class="w-full h-auto block"
                    />
                    
                    <!-- Hero Content (Text Over Image) -->
                    <!-- Removed gradient overlay as requested -->
                    <div class="absolute inset-0 flex flex-col justify-center pb-20 max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="max-w-2xl" v-if="banner.title || banner.link">
                             <span v-if="banner.title" class="inline-block py-1 px-3 rounded bg-[#cc0000] text-white text-xs font-bold uppercase mb-4 tracking-wider shadow-sm">
                                Destaque
                            </span>
                            <h1 v-if="banner.title" class="text-4xl md:text-6xl font-black text-white leading-tight uppercase italic transform -skew-x-6 mb-2 drop-shadow-lg" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.7);">
                                {{ banner.title }}
                            </h1>
                            <a v-if="banner.link" :href="banner.link" class="inline-block mt-4 px-6 py-3 bg-[#00a550] text-white font-bold uppercase tracking-wide rounded hover:bg-green-700 transition-colors shadow-lg">
                                Saiba Mais
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Carousel Indicators -->
                <div v-if="heroBanners.length > 1" class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-2 z-20">
                    <button 
                        v-for="(banner, index) in heroBanners" 
                        :key="index"
                        @click="setHeroIndex(index)"
                        class="w-3 h-3 rounded-full transition-colors duration-300 shadow-sm border border-white/30"
                        :class="index === currentHeroIndex ? 'bg-[#00a550]' : 'bg-white/50 hover:bg-white'"
                        :aria-label="'Slide ' + (index + 1)"
                    ></button>
                </div>

                <!-- Carousel Arrows -->
                <button 
                    v-if="heroBanners.length > 1"
                    @click="prevSlide"
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full transition-all z-20 backdrop-blur-sm"
                    aria-label="Previous slide"
                >
                    <ChevronLeft class="w-8 h-8" />
                </button>
                <button 
                    v-if="heroBanners.length > 1"
                    @click="nextSlide"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full transition-all z-20 backdrop-blur-sm"
                    aria-label="Next slide"
                >
                    <ChevronRight class="w-8 h-8" />
                </button>
            </template>

            <!-- Fallback Static Hero (if no banners) -->
            <div v-else class="col-start-1 row-start-1 w-full h-[300px] md:h-[400px] lg:h-[500px] relative">
                <img 
                    src="https://images.unsplash.com/photo-1519003722824-194d4455a60c?q=80&w=2000&auto=format&fit=crop" 
                    alt="Hero Background" 
                    class="w-full h-full object-cover opacity-60"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-transparent"></div>
                <div class="relative max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 h-full flex flex-col justify-center pb-20">
                    <div class="max-w-2xl">
                        <span class="inline-block py-1 px-3 rounded bg-[#cc0000] text-white text-xs font-bold uppercase mb-4 tracking-wider">
                            Oportunidade Única
                        </span>
                        <h1 class="text-4xl md:text-6xl font-black text-white leading-tight uppercase italic transform -skew-x-6 mb-2">
                            O MAIOR LEILÃO DE <br>
                            <span class="text-[#fca311]">PESADOS</span> DO BRASIL
                        </h1>
                        <p class="text-gray-300 text-lg md:text-xl mb-8 font-light">
                            Caminhões, máquinas e utilitários com até <strong class="text-white">60% de desconto</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Box (Below Banner) -->
        <div class="relative z-20 px-4 -mt-6">
            <div class="max-w-[1100px] mx-auto bg-white rounded-lg shadow-2xl overflow-visible border border-gray-100">
                <!-- Search Inputs -->
                <div class="p-4 md:p-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-end border-b border-gray-100 relative z-30">
                    <div class="md:col-span-5 relative">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">O que você procura?</label>
                        <div class="relative">
                            <Search class="absolute left-3 top-3 w-5 h-5 text-gray-400" />
                            <input 
                                type="text" 
                                v-model="searchQuery"
                                @input="showSuggestions = true"
                                placeholder="Digite o que você procura (carro, moto, casa, modelo...)" 
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded text-sm focus:outline-none focus:border-[#00a550] focus:ring-1 focus:ring-[#00a550] font-medium text-gray-700" 
                            />
                            <!-- Autocomplete Suggestions -->
                            <div v-if="showSuggestions && suggestions.length > 0" class="absolute top-full left-0 w-full bg-white border border-gray-100 rounded-b-lg shadow-xl z-50 mt-1 max-h-60 overflow-y-auto">
                                <ul>
                                    <li 
                                        v-for="(item, index) in suggestions" 
                                        :key="index"
                                        @click="selectSuggestion(item)"
                                        class="px-4 py-3 hover:bg-gray-50 cursor-pointer flex items-center justify-between group border-b border-gray-50 last:border-0"
                                    >
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-[#00a550]">
                                            {{ item.label }}
                                        </span>
                                        <ArrowRight class="w-3 h-3 text-gray-300 group-hover:text-[#00a550]" />
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Estado</label>
                        <div class="relative">
                            <MapPin class="absolute left-3 top-3 w-5 h-5 text-gray-400" />
                            <select v-model="selectedEstado" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded text-sm focus:outline-none focus:border-[#00a550] focus:ring-1 focus:ring-[#00a550] font-medium text-gray-700 appearance-none">
                                <option value="Todos">Todos</option>
                                <option value="SP">SP (Detectado)</option>
                                <option value="RJ">RJ</option>
                                <option value="MG">MG</option>
                                <option value="RS">RS</option>
                                <option value="PR">PR</option>
                            </select>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Condição</label>
                        <select v-model="selectedCondicao" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded text-sm focus:outline-none focus:border-[#00a550] focus:ring-1 focus:ring-[#00a550] font-medium text-gray-700 appearance-none">
                            <option value="Todos">Todos</option>
                            <option value="Novo">Novo</option>
                            <option value="Usado">Usado</option>
                            <option value="Recuperado">Recuperado</option>
                            <option value="Sucata">Sucata</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <button 
                            @click="handleSearch"
                            class="w-full py-3 bg-[#00a550] text-white font-bold rounded shadow-lg shadow-green-500/30 hover:bg-green-700 transition-all transform hover:-translate-y-0.5 text-sm uppercase tracking-wide flex items-center justify-center gap-2"
                        >
                            <Search class="w-4 h-4" />
                            <span>Buscar {{ searchCount > 0 ? searchCount : '' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Categories List (Moved from Quick Filters) -->
                <div class="flex flex-nowrap md:flex-wrap overflow-x-auto md:overflow-visible justify-start md:justify-center gap-4 md:gap-6 p-4 bg-gray-50 pb-6 md:pb-4 scrollbar-hide">
                    <button 
                        v-for="category in categories" 
                        :key="category.id"
                        @click="selectCategory(category.name)"
                        :disabled="!category.lotes_count"
                        :class="[
                            'bg-white rounded-xl border px-4 py-2 transition-all group flex flex-col items-start min-w-[150px] duration-300',
                            category.lotes_count 
                                ? 'border-gray-100 hover:shadow-lg hover:-translate-y-1 cursor-pointer hover:border-[#00a550]/30' 
                                : 'border-gray-100 opacity-60 cursor-not-allowed grayscale'
                        ]"
                        :title="category.lotes_count ? category.name : 'Nenhum anúncio disponível'"
                    >
                        <div class="w-[40px] h-[50px] flex items-center justify-center mb-2">
                            <img 
                                v-if="category.image_path" 
                                :src="`/storage/${category.image_path}`" 
                                :alt="category.name"
                                loading="lazy"
                                class="w-full h-full object-contain"
                            />
                            <component v-else :is="Car" class="w-8 h-8 text-gray-300 group-hover:text-[#00a550] transition-colors" />
                        </div>
                        
                        <div class="flex items-center justify-between w-full gap-2">
                            <span class="text-sm font-bold text-gray-600 group-hover:text-[#00a550] uppercase tracking-wide transition-colors text-left">{{ category.name }}</span>
                            <span 
                                :class="[
                                    'text-xs font-bold px-2 py-1 rounded-md transition-colors min-w-[1.5rem] text-center',
                                    category.lotes_count 
                                        ? 'bg-gray-100 text-gray-500 group-hover:bg-[#e6f6ee] group-hover:text-[#00a550]' 
                                        : 'bg-gray-50 text-gray-300'
                                ]"
                            >
                                {{ category.lotes_count || 0 }}
                            </span>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Spacer for Floating Search -->
        <div class="h-12 bg-[#f5f5f5]"></div>

        <!-- Promotional Banners -->
        <div class="bg-white py-8 border-y border-gray-100" v-if="middleBanners.length > 0">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a :href="banner.link || '#'" v-for="(banner, i) in middleBanners" :key="i" class="relative rounded-xl overflow-hidden shadow-sm h-40 group cursor-pointer block">
                        <img :src="banner.image_path" loading="lazy" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
                        <div class="absolute inset-0 flex flex-col justify-center px-8">
                            <span v-if="banner.title" class="text-[#fca311] font-bold text-xs uppercase tracking-wider mb-1">Destaque</span>
                            <h3 v-if="banner.title" class="text-white font-black text-2xl md:text-3xl italic uppercase leading-none mb-4">{{ banner.title }}</h3>
                            <button class="w-fit px-4 py-2 bg-white text-[#333] text-xs font-bold uppercase rounded hover:bg-gray-100 transition-colors">
                                Conferir Agora
                            </button>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Leilões em Destaque -->
        <div class="py-12 bg-[#f5f5f5]" v-if="activeAuctions && activeAuctions.length > 0">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-8 bg-[#cc0000]"></div>
                        <h2 class="text-2xl font-black text-[#333] uppercase italic tracking-tight">
                            Leilões em <span class="text-[#cc0000]">Andamento</span>
                        </h2>
                    </div>
                </div>

                <!-- Active Auctions List -->
                <div class="flex flex-col gap-8">
                    <AuctionSection 
                        v-for="leilao in activeAuctions" 
                        :key="leilao.id" 
                        :leilao="leilao" 
                    />
                </div>

            </div>
        </div>

        <!-- Skeleton Loader (When Loading) -->
        <div class="py-12 bg-[#f5f5f5]" v-if="isLoading">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                 <div class="flex items-center gap-3 mb-8">
                    <div class="w-1 h-8 bg-gray-300 animate-pulse"></div>
                    <div class="h-8 w-64 bg-gray-300 rounded animate-pulse"></div>
                </div>
                
                <div class="flex flex-col gap-8">
                    <div v-for="i in 2" :key="i" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 animate-pulse">
                        <!-- Header Skeleton -->
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 mb-8 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                             <div class="flex items-center gap-6 w-full">
                                <div class="w-20 h-20 bg-gray-200 rounded-xl shrink-0"></div>
                                <div class="flex-1 space-y-3">
                                    <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                                    <div class="h-6 bg-gray-200 rounded w-3/4"></div>
                                </div>
                             </div>
                        </div>
                        <!-- Grid Skeleton -->
                        <div class="flex gap-4 overflow-hidden">
                            <div v-for="j in 4" :key="j" class="min-w-[200px] w-full md:w-1/4 h-64 bg-gray-200 rounded-xl shrink-0"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Banners -->
        <div class="py-8 bg-[#f5f5f5]" v-if="footerBanners.length > 0">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                <a :href="footerBanners[0].link || '#'" class="block rounded-xl overflow-hidden shadow-md relative h-[200px] group">
                    <img :src="footerBanners[0].image_path" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gradient-to-r from-[#cc0000] to-transparent opacity-90"></div>
                    <div class="absolute inset-0 flex items-center px-8 md:px-16">
                        <div class="text-white max-w-lg">
                            <h2 v-if="footerBanners[0].title" class="text-3xl font-black italic uppercase mb-2">{{ footerBanners[0].title }}</h2>
                            <button class="bg-white text-[#cc0000] px-8 py-3 rounded font-bold uppercase tracking-wide hover:bg-gray-100 transition-colors shadow-lg mt-4">
                                Ver Ofertas
                            </button>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Banner "Leilo Shop" (Fallback if no footer banner) -->
        <div class="py-8 bg-[#f5f5f5]" v-else>
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-xl overflow-hidden shadow-md relative h-[200px]">
                    <img src="https://images.unsplash.com/photo-1550505393-2598857b282d?w=1200&auto=format&fit=crop&q=80" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-r from-[#cc0000] to-transparent opacity-90"></div>
                    <div class="absolute inset-0 flex items-center px-8 md:px-16">
                        <div class="text-white max-w-lg">
                            <h2 class="text-3xl font-black italic uppercase mb-2">Venda Direta</h2>
                            <p class="text-lg mb-6 opacity-90">Não quer esperar o leilão? Compre agora com preços fixos e descontos incríveis.</p>
                            <button class="bg-white text-[#cc0000] px-8 py-3 rounded font-bold uppercase tracking-wide hover:bg-gray-100 transition-colors shadow-lg">
                                Ver Ofertas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximos Leilões (List Style) -->
        <div class="py-12 bg-white border-t border-gray-100" v-if="!isLoading && upcomingAuctions.length > 0">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-8 bg-[#002f6c]"></div>
                        <h2 class="text-2xl font-black text-[#333] uppercase italic tracking-tight">
                            Próximos <span class="text-[#002f6c]">Eventos</span>
                        </h2>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="leilao in upcomingAuctions" :key="leilao.id" class="flex bg-white border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow group cursor-pointer relative">
                        <Link :href="route('leiloes.show', leilao.id)" class="absolute inset-0 z-10"></Link>
                        <div class="w-32 h-24 bg-gray-100 rounded flex-shrink-0 overflow-hidden relative">
                             <img 
                                :src="getPrimeiroLote(leilao)?.foto_capa ? `/storage/${getPrimeiroLote(leilao).foto_capa}` : 'https://via.placeholder.com/150'" 
                                loading="lazy"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute top-0 left-0 bg-[#002f6c] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-br">
                                Breve
                            </div>
                        </div>
                        <div class="ml-4 flex-1 flex flex-col justify-center">
                            <h3 class="font-bold text-gray-900 line-clamp-1 mb-1 group-hover:text-[#002f6c] transition-colors">{{ leilao.titulo }}</h3>
                            <div class="flex items-center gap-4 text-xs text-gray-500 mb-2">
                                <span class="flex items-center gap-1"><Calendar class="w-3 h-3" /> {{ formatDate(leilao.data_inicio) }}</span>
                                <span class="flex items-center gap-1"><MapPin class="w-3 h-3" /> {{ leilao.local || 'Online' }}</span>
                            </div>
                            <span class="text-xs font-bold text-[#00a550] uppercase group-hover:underline">
                                Ver detalhes
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CarLayout>
</template>