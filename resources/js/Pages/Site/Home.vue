<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { 
    Search, 
    MapPin, 
    Home, 
    Car, 
    Smartphone, 
    Tag, 
    ArrowRight, 
    Play,
    Heart,
    ChevronLeft,
    ChevronRight,
    Map
} from 'lucide-vue-next';

defineProps({
    leiloesAtivos: Array,
    proximosLeiloes: Array
});

const categories = [
    { name: 'Retomada de bancos', active: true },
    { name: 'Imóveis de alto valor', active: true },
    { name: 'Oportunidades de leilão', active: true },
    { name: 'Imóveis ocupados', active: true },
    { name: 'Terrenos em oferta', active: true },
    { name: 'Imóveis comerciais', active: true },
    { name: 'Descontos acima de 50%', active: true },
    { name: 'Até 200 mil de avaliação', active: true },
    { name: 'Até 40% abaixo da avaliação', active: true },
    { name: 'Até 50% abaixo da avaliação', active: true },
    { name: 'Até 60% abaixo da avaliação', active: true },
    { name: 'Até 70% abaixo da avaliação', active: true },
];

const partners = [
    { name: 'Santander', logo: 'https://logo.clearbit.com/santander.com.br' },
    { name: 'Itaú', logo: 'https://logo.clearbit.com/itau.com.br' },
    { name: 'Bradesco', logo: 'https://logo.clearbit.com/bradesco.com.br' },
    { name: 'Safra', logo: 'https://logo.clearbit.com/safra.com.br' },
];

const zukPlay = [
    { title: 'Como funcionam os leilões?', image: 'https://images.unsplash.com/photo-1577563908411-5077b6dc7624?w=400&h=600&fit=crop' },
    { title: 'O processo é seguro. Qual a diferença?', image: 'https://images.unsplash.com/photo-1556742049-0cfed4f7a07d?w=400&h=600&fit=crop' },
    { title: 'Segredo de Justiça. Onde ficam?', image: 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=400&h=600&fit=crop' },
    { title: 'Por que o arrematante paga?', image: 'https://images.unsplash.com/photo-1560520653-9e0e4c89eb11?w=400&h=600&fit=crop' },
];

const blogPosts = [
    { 
        title: 'É mais Zuk', 
        desc: 'Tecnologia para você aproveitar as principais oportunidades em leilões de imóveis.',
        image: 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=500&h=300&fit=crop',
        color: 'bg-green-50'
    },
    { 
        title: 'Nossos Tutoriais', 
        desc: 'Você sabe o que é Venda Direta? E quais as vantagens de comprar um imóvel dessa forma?',
        image: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500&h=300&fit=crop',
        color: 'bg-blue-50'
    },
    { 
        title: 'Como funciona', 
        desc: 'Um passo a passo de tudo que precisa de saber na hora de dar o seu lance e arrematar.',
        image: 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=500&h=300&fit=crop',
        color: 'bg-orange-50'
    },
];

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const placeholderImages = [
    'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=500&h=400&fit=crop',
    'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=500&h=400&fit=crop',
    'https://images.unsplash.com/photo-1600596542815-e32cb5ec5442?w=500&h=400&fit=crop',
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=500&h=400&fit=crop'
];

const getPrimeiroLote = (leilao) => {
    return leilao.lotes && leilao.lotes.length > 0 ? leilao.lotes[0] : null;
};
</script>

<template>
    <Head title="Home" />
    <SiteLayout>
        <!-- Hero Section -->
        <div class="relative bg-[#fcfaf7] pt-12 pb-24">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="z-10">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif text-[#333333] mb-8 leading-tight">
                            A maior plataforma de<br>
                            <span class="text-[#00a550]">leilões de imóveis</span> do Brasil
                        </h1>
                        <p class="text-gray-600 mb-8 text-lg">Encontre a oportunidade ideal Buscar por</p>
                        
                        <!-- Search Box -->
                        <div class="bg-white rounded-xl shadow-xl p-2 max-w-2xl">
                            <div class="flex space-x-1 mb-4 border-b border-gray-100 pb-2 px-2 overflow-x-auto">
                                <button class="flex items-center space-x-2 px-4 py-2 bg-gray-100 rounded-full text-sm font-medium text-gray-700 hover:bg-gray-200 whitespace-nowrap">
                                    <Home class="w-4 h-4" /> <span>Imóveis</span>
                                </button>
                                <button class="flex items-center space-x-2 px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-full whitespace-nowrap">
                                    <Car class="w-4 h-4" /> <span>Veículos</span>
                                </button>
                                <button class="flex items-center space-x-2 px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-full whitespace-nowrap">
                                    <Smartphone class="w-4 h-4" /> <span>Produtos</span>
                                </button>
                                <button class="flex items-center space-x-2 px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-full whitespace-nowrap">
                                    <Tag class="w-4 h-4" /> <span>Venda Direta</span>
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 p-2">
                                <div class="md:col-span-3 relative">
                                    <MapPin class="absolute left-3 top-3 w-5 h-5 text-gray-400" />
                                    <input type="text" placeholder="Todas as cidades" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-green-500" />
                                </div>
                                <div class="md:col-span-3">
                                    <select class="w-full py-2.5 px-4 bg-gray-50 border-none rounded-lg text-sm text-gray-500 focus:ring-2 focus:ring-green-500">
                                        <option>Todos os tipos</option>
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <select class="w-full py-2.5 px-4 bg-gray-50 border-none rounded-lg text-sm text-gray-500 focus:ring-2 focus:ring-green-500">
                                        <option>Todos os bairros</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <button class="w-full py-2.5 bg-[#004529] text-white rounded-lg font-bold text-sm hover:bg-green-900 transition-colors">
                                        Buscar
                                    </button>
                                </div>
                                <div class="md:col-span-1">
                                    <button class="w-full py-2.5 border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-50">
                                        <MapIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative h-[400px] rounded-[2rem] overflow-hidden hidden lg:block">
                        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Interior Moderno" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-r from-[#fcfaf7] via-transparent to-transparent w-1/4"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Banner Section -->
        <div class="bg-[#cc0000] text-white py-4 overflow-hidden relative">
            <div class="max-w-[1400px] mx-auto px-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img src="https://logo.clearbit.com/santander.com.br" class="h-8 bg-white p-1 rounded" />
                    <div class="font-bold text-xl italic">Leilão Arrematou = Levou!</div>
                </div>
                <div class="flex items-center space-x-8">
                    <div class="text-sm font-medium bg-red-800 px-3 py-1 rounded">250 Imóveis</div>
                    <div class="text-sm">Até 73% de desconto</div>
                    <button class="bg-white text-[#cc0000] px-6 py-2 rounded-full font-bold text-sm hover:bg-gray-100">
                        Ver Ofertas
                    </button>
                </div>
            </div>
        </div>

        <!-- Destaques Section -->
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex justify-between items-end mb-8">
                <h2 class="text-3xl font-serif text-[#333333]">Destaques</h2>
                <div class="flex space-x-2">
                    <button class="p-2 rounded-full border border-gray-200 hover:bg-gray-50"><ChevronLeft class="w-5 h-5" /></button>
                    <button class="p-2 rounded-full bg-[#004529] text-white hover:bg-green-900"><ChevronRight class="w-5 h-5" /></button>
                </div>
            </div>

            <div v-if="leiloesAtivos && leiloesAtivos.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Loop through active auctions -->
                <div v-for="(leilao, index) in leiloesAtivos" :key="leilao.id" 
                     class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col relative">
                    
                    <!-- Link absoluto cobrindo todo o card -->
                    <Link :href="route('leiloes.show', leilao.id)" class="absolute inset-0 z-10"></Link>

                    <!-- Card Header / Tag -->
                    <div class="h-10 flex items-center px-4 text-xs font-bold text-white uppercase tracking-wide relative z-0" 
                         :class="index % 2 === 0 ? 'bg-[#cc0000]' : 'bg-[#ec7000]'">
                        <Tag class="w-3 h-3 mr-2" />
                        {{ leilao.titulo }}
                    </div>

                    <!-- Image -->
                    <div class="relative aspect-[4/3] overflow-hidden">
                        <img :src="getPrimeiroLote(leilao)?.foto_capa ? `/storage/${getPrimeiroLote(leilao).foto_capa}` : placeholderImages[index % placeholderImages.length]" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                        <div class="absolute bottom-2 left-2 bg-white/90 backdrop-blur px-2 py-1 rounded text-xs font-bold text-gray-700">
                            1ª Praça
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4 flex flex-col flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg line-clamp-1" :title="leilao.titulo">
                                    {{ leilao.titulo }}
                                </h3>
                                <p class="text-xs text-gray-500">{{ leilao.local }}</p>
                            </div>
                            <!-- Botão de favorito precisa de z-index maior -->
                            <Heart class="w-5 h-5 text-gray-300 hover:text-red-500 cursor-pointer relative z-20" />
                        </div>
                        
                        <div class="mt-auto space-y-1">
                            <div class="text-xs text-gray-400">Lance Inicial a partir de</div>
                            <div class="text-xl font-bold text-[#00a550]">
                                {{ getPrimeiroLote(leilao) ? formatCurrency(getPrimeiroLote(leilao).valor_inicial) : 'Sob Consulta' }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ leilao.lotes_count }} lote(s) disponível(is)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-12 bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <Home class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-900">Nenhum leilão ativo no momento</h3>
                <p class="mt-2 text-sm text-gray-500">Volte mais tarde para conferir novas oportunidades.</p>
            </div>
            
            <div class="mt-12 text-center">
                <button class="px-8 py-3 bg-[#004529] text-white rounded-lg font-bold hover:bg-green-900 transition-colors">
                    Ver todos
                </button>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="bg-[#fcfaf7] py-16">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-serif text-[#333333] mb-2 text-center">Encontre o leilão de imóvel ideal para você</h2>
                <p class="text-center text-gray-500 mb-10">Explore por categorias</p>
                
                <div class="flex flex-wrap justify-center gap-3 max-w-5xl mx-auto">
                    <button v-for="cat in categories" :key="cat.name" 
                            class="px-6 py-3 bg-[#004529] text-white text-sm font-medium rounded hover:bg-green-800 transition-colors">
                        {{ cat.name }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Partners Section -->
        <div class="py-16 bg-white">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-serif text-[#333333] mb-12">Imóveis por vendedor</h2>
                <div class="flex justify-between items-center overflow-x-auto pb-4 gap-8">
                    <div v-for="partner in partners" :key="partner.name" class="flex-shrink-0 grayscale hover:grayscale-0 transition-all opacity-60 hover:opacity-100">
                        <img :src="partner.logo" class="h-12 object-contain" :alt="partner.name">
                    </div>
                </div>
            </div>
        </div>

        <!-- Zuk Play Section -->
        <div class="py-16 bg-[#fcfaf7]">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-serif text-[#333333]">Zuk Play</h2>
                    <div class="flex space-x-2">
                        <button class="p-2 rounded-full border border-gray-200 hover:bg-gray-50"><ChevronLeft class="w-5 h-5" /></button>
                        <button class="p-2 rounded-full bg-[#004529] text-white hover:bg-green-900"><ChevronRight class="w-5 h-5" /></button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="(item, i) in zukPlay" :key="i" class="relative rounded-2xl overflow-hidden aspect-[9/16] group cursor-pointer shadow-lg">
                        <img :src="item.image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-full flex items-center justify-center">
                                <Play class="w-8 h-8 text-white fill-current" />
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 p-6">
                            <h3 class="text-white font-bold text-lg leading-tight">{{ item.title }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entenda o mundo dos leilões -->
        <div class="py-16 bg-white">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-12">
                    <h2 class="text-3xl font-serif text-[#333333]">Entenda o mundo dos leilões de imóveis</h2>
                    <div class="flex space-x-2">
                        <button class="p-2 rounded-full border border-gray-200 hover:bg-gray-50"><ChevronLeft class="w-5 h-5" /></button>
                        <button class="p-2 rounded-full bg-[#004529] text-white hover:bg-green-900"><ChevronRight class="w-5 h-5" /></button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div v-for="(post, i) in blogPosts" :key="i" class="flex flex-col group cursor-pointer">
                        <div :class="`rounded-2xl overflow-hidden mb-6 aspect-video ${post.color} p-8 flex items-center justify-center`">
                            <img :src="post.image" class="w-full h-full object-cover rounded-lg shadow-sm group-hover:scale-105 transition-transform" />
                        </div>
                        <h3 class="font-bold text-xl text-gray-900 mb-2">{{ post.title }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ post.desc }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Community Section -->
        <div class="bg-[#004529] py-20 relative overflow-hidden">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-4xl font-serif text-white mb-6">Faça parte da nossa<br>comunidade</h2>
                        <p class="text-green-100 mb-8 max-w-md">Fique por dentro das principais notícias e oportunidades!</p>
                        <div class="flex space-x-4">
                            <button class="bg-white text-[#004529] px-6 py-3 rounded-lg font-bold flex items-center hover:bg-gray-100">
                                <span class="mr-2">📷</span> Siga-nos no Instagram
                            </button>
                            <button class="bg-transparent border border-white text-white px-6 py-3 rounded-lg font-bold flex items-center hover:bg-white/10">
                                <span class="mr-2">💬</span> Entre no canal
                            </button>
                        </div>
                    </div>
                    <!-- Mockup placeholders -->
                    <div class="flex space-x-4">
                        <div class="w-48 h-80 bg-white rounded-xl rotate-[-6deg] shadow-2xl overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400" class="w-full h-full object-cover" />
                        </div>
                        <div class="w-48 h-80 bg-white rounded-xl rotate-[6deg] translate-y-8 shadow-2xl overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=400" class="w-full h-full object-cover" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Guide -->
        <div class="py-16 bg-[#fcfaf7]">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row gap-12">
                    <div class="md:w-1/3">
                        <h2 class="text-3xl font-serif text-[#333333] mb-4">Guia de Lugares</h2>
                        <p class="text-gray-500 mb-8">Saiba tudo sobre bairros, cidades e diversas outras curiosidades nas regiões do seu interesse no Blog da Zuk!</p>
                        <button class="px-6 py-2 border border-[#004529] text-[#004529] font-bold rounded hover:bg-green-50">
                            Ver todos
                        </button>
                    </div>
                    <div class="md:w-2/3 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="aspect-video rounded-lg overflow-hidden mb-4">
                                <img src="https://images.unsplash.com/photo-1590523277543-a94d2e4eb00b?w=600" class="w-full h-full object-cover" />
                            </div>
                            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">Lugares</span>
                            <h3 class="font-bold text-lg mt-2">Os 10 melhores bairros para morar em Fortaleza</h3>
                        </div>
                        <div class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="aspect-video rounded-lg overflow-hidden mb-4">
                                <img src="https://images.unsplash.com/photo-1572021335469-31706a17aaef?w=600" class="w-full h-full object-cover" />
                            </div>
                            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">Lugares</span>
                            <h3 class="font-bold text-lg mt-2">Invista em imóveis com descontos de até 60% em Sergipe</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="py-16 bg-white border-t border-gray-100">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                    <div>
                        <h2 class="text-2xl font-serif text-[#333333] mb-2">Cadastre-se na nossa Newsletter</h2>
                        <p class="text-gray-500 text-sm max-w-lg">Inscreva-se e fique por dentro das oportunidades nos leilões de imóveis, judiciais e extrajudiciais para você fechar um bom negócio com a Zuk.</p>
                    </div>
                    <div class="flex w-full md:w-auto max-w-md">
                        <input type="email" placeholder="Digite seu e-mail" class="flex-grow px-4 py-3 bg-gray-50 border border-gray-200 rounded-l-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none" />
                        <button class="bg-[#004529] text-white px-6 py-3 rounded-r-lg font-bold hover:bg-green-900">
                            Inscrever
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </SiteLayout>
</template>
