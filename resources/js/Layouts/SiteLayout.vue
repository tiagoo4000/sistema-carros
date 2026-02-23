<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import ClientHeaderMenu from '@/Components/Site/ClientHeaderMenu.vue';
import { 
  Search,
  HelpCircle,
  LogIn,
  Facebook,
  Instagram,
  Youtube,
  Phone,
  MessageCircle,
  Mail,
  Menu,
  X,
  LayoutDashboard,
  Gavel,
  ShoppingBag,
  User,
  LogOut,
  FileText
} from 'lucide-vue-next';

const page = usePage();
const mobileMenuOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-[#f5f5f5] font-sans text-[#333]">
        <!-- Top Bar (Leilo Master Style) -->
        <header class="bg-white shadow-sm sticky top-0 z-50 h-20">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 h-full">
                <div class="flex justify-between items-center h-full gap-4">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <Link :href="route('home')" class="flex items-center gap-1 group">
                            <img 
                                v-if="page.props.site_config?.site_logo" 
                                :src="page.props.site_config.site_logo" 
                                :alt="page.props.site_config.site_name || 'Logo'"
                                class="h-12 w-auto object-contain"
                            />
                            <div v-else class="flex items-center gap-1">
                                <div class="text-[#cc0000] transform -scale-x-100 font-bold text-3xl leading-none" style="font-family: Arial, sans-serif;">&gt;</div>
                                <div class="text-3xl font-black text-[#002f6c] tracking-tighter italic lowercase" style="font-family: 'Arial Black', sans-serif;">
                                    leilo
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Search Bar (Center) - Hidden on mobile, visible on desktop -->
                    <div class="hidden md:flex flex-1 max-w-xl mx-8">
                        <div class="relative w-full group">
                            <input 
                                type="text" 
                                placeholder="Buscar..." 
                                class="w-full pl-4 pr-10 py-2.5 bg-gray-100 border-none rounded-md text-sm text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-[#00a550] focus:bg-white transition-all"
                            >
                            <Search class="absolute right-3 top-2.5 w-5 h-5 text-gray-400 group-focus-within:text-[#00a550]" />
                        </div>
                    </div>

                    <!-- Right Actions (Desktop) -->
                    <div class="hidden lg:flex items-center space-x-6 text-[13px] font-bold text-gray-600 uppercase tracking-wide">
                        <a href="#" class="hover:text-[#00a550] transition-colors">Home</a>
                        <a href="#" class="hover:text-[#00a550] transition-colors">Imóveis</a>
                        <a href="#" class="hover:text-[#00a550] transition-colors">Veículos</a>
                        <a href="#" class="hover:text-[#00a550] transition-colors">Quero Vender</a>
                        <a href="#" class="hover:text-[#00a550] transition-colors flex items-center gap-1">
                            <HelpCircle class="w-4 h-4" /> Ajuda
                        </a>

                        <div class="flex items-center gap-4 pl-4 border-l border-gray-200">
                            <!-- Logged In State -->
                            <div v-if="$page.props.auth.user">
                                <ClientHeaderMenu />
                            </div>
                            
                            <!-- Guest State -->
                            <template v-else>
                                <Link :href="route('login')" class="flex items-center gap-1 text-[#002f6c] hover:text-[#00a550]">
                                    <LogIn class="w-4 h-4" /> Entrar
                                </Link>
                                <Link :href="route('register')" class="bg-[#00a550] text-white px-4 py-2 rounded font-bold hover:bg-green-700 transition-colors">
                                    CADASTRE-SE
                                </Link>
                            </template>
                        </div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="flex lg:hidden items-center">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 hover:text-[#002f6c] p-2">
                            <Menu class="w-6 h-6" />
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Mobile Menu Overlay -->
        <div v-if="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden">
            <div class="fixed inset-0 bg-black/50" @click="mobileMenuOpen = false"></div>
            <div class="fixed inset-y-0 right-0 w-64 bg-white shadow-xl transform transition-transform duration-300 ease-in-out">
                <div class="flex flex-col h-full">
                    <div class="p-4 flex justify-between items-center border-b">
                        <span class="font-bold text-lg text-[#002f6c]">Menu</span>
                        <button @click="mobileMenuOpen = false" class="text-gray-500 hover:text-red-500">
                            <X class="w-6 h-6" />
                        </button>
                    </div>
                    
                    <div class="p-4 flex-1 overflow-y-auto">
                        <!-- User Section Mobile -->
                        <div v-if="$page.props.auth.user" class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="h-10 w-10 rounded-full bg-[#002f6c] text-white flex items-center justify-center font-bold">
                                    {{ ($page.props.auth.user.nome || 'U').charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <div class="font-bold text-[#002f6c]">{{ $page.props.auth.user.nome }}</div>
                                    <div class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</div>
                                </div>
                            </div>
                            <nav class="space-y-1">
                                <Link :href="route('minha-conta')" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-white rounded" @click="mobileMenuOpen = false">
                                    <LayoutDashboard class="w-4 h-4" /> Dashboard
                                </Link>
                                <Link :href="route('minha-conta')" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-white rounded" @click="mobileMenuOpen = false">
                                    <Gavel class="w-4 h-4" /> Meus Lances
                                </Link>
                                <Link :href="route('minha-conta.documentos')" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-white rounded" @click="mobileMenuOpen = false">
                                    <FileText class="w-4 h-4" /> Verificação de Conta
                                </Link>
                                <Link :href="route('minha-conta.meus-documentos')" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-white rounded" @click="mobileMenuOpen = false">
                                    <FileText class="w-4 h-4" /> Meus Documentos
                                </Link>
                                <Link :href="route('logout')" method="post" as="button" class="flex w-full items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded" @click="mobileMenuOpen = false">
                                    <LogOut class="w-4 h-4" /> Sair
                                </Link>
                            </nav>
                        </div>

                        <!-- Public Links -->
                        <div class="space-y-2">
                            <a href="#" class="block px-3 py-2 text-gray-700 font-bold hover:bg-gray-50 rounded">Home</a>
                            <a href="#" class="block px-3 py-2 text-gray-700 font-bold hover:bg-gray-50 rounded">Imóveis</a>
                            <a href="#" class="block px-3 py-2 text-gray-700 font-bold hover:bg-gray-50 rounded">Veículos</a>
                            <a href="#" class="block px-3 py-2 text-gray-700 font-bold hover:bg-gray-50 rounded">Quero Vender</a>
                        </div>

                        <div v-if="!$page.props.auth.user" class="mt-6 space-y-3">
                             <Link :href="route('login')" class="flex items-center justify-center gap-2 w-full border border-[#002f6c] text-[#002f6c] px-4 py-2 rounded font-bold hover:bg-blue-50 transition-colors">
                                <LogIn class="w-4 h-4" /> Entrar
                            </Link>
                            <Link :href="route('register')" class="flex items-center justify-center w-full bg-[#00a550] text-white px-4 py-2 rounded font-bold hover:bg-green-700 transition-colors">
                                CADASTRE-SE
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
        
        <!-- Footer (Dark Blue Style) -->
        <footer class="bg-[#002f6c] text-white pt-16 pb-8">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16 text-sm">
                    <div>
                        <div class="flex items-center gap-1 mb-6">
                            <div class="text-[#cc0000] transform -scale-x-100 font-bold text-2xl leading-none">&gt;</div>
                            <div class="text-2xl font-black text-white tracking-tighter italic lowercase" style="font-family: 'Arial Black', sans-serif;">leilo</div>
                        </div>
                        <p class="text-gray-400 mb-6 leading-relaxed">
                            A maior plataforma de leilões do Brasil. Segurança e transparência para você arrematar seu veículo ou imóvel.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="bg-white/10 p-2 rounded hover:bg-[#00a550] transition-colors"><Facebook class="w-5 h-5"/></a>
                            <a href="#" class="bg-white/10 p-2 rounded hover:bg-[#00a550] transition-colors"><Instagram class="w-5 h-5"/></a>
                            <a href="#" class="bg-white/10 p-2 rounded hover:bg-[#00a550] transition-colors"><Youtube class="w-5 h-5"/></a>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-bold mb-6 text-lg">Institucional</h4>
                        <ul class="space-y-3 text-gray-300">
                            <li><a href="#" class="hover:text-white hover:underline decoration-[#00a550] underline-offset-4">Quem somos</a></li>
                            <li><a href="#" class="hover:text-white hover:underline decoration-[#00a550] underline-offset-4">Segurança</a></li>
                            <li><a href="#" class="hover:text-white hover:underline decoration-[#00a550] underline-offset-4">Política de Privacidade</a></li>
                            <li><a href="#" class="hover:text-white hover:underline decoration-[#00a550] underline-offset-4">Termos de Uso</a></li>
                            <li><a href="#" class="hover:text-white hover:underline decoration-[#00a550] underline-offset-4">Trabalhe Conosco</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold mb-6 text-lg">Leilões</h4>
                        <ul class="space-y-3 text-gray-300">
                            <li><a href="#" class="hover:text-white hover:underline decoration-[#00a550] underline-offset-4">Leilão de Imóveis</a></li>
                            <li><a href="#" class="hover:text-white hover:underline decoration-[#00a550] underline-offset-4">Leilão de Veículos</a></li>
                            <li><a href="#" class="hover:text-white hover:underline decoration-[#00a550] underline-offset-4">Leilão de Caminhões</a></li>
                            <li><a href="#" class="hover:text-white hover:underline decoration-[#00a550] underline-offset-4">Judiciais</a></li>
                            <li><a href="#" class="hover:text-white hover:underline decoration-[#00a550] underline-offset-4">Extrajudiciais</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold mb-6 text-lg">Atendimento</h4>
                        <ul class="space-y-4 text-gray-300">
                            <li v-if="page.props.site_config?.phone" class="flex items-start gap-3">
                                <Phone class="w-5 h-5 text-[#00a550] mt-0.5"/> 
                                <div>
                                    <span class="block font-bold text-white">{{ page.props.site_config.phone }}</span>
                                    <span class="text-xs">Telefone</span>
                                </div>
                            </li>
                            <li v-if="page.props.site_config?.whatsapp" class="flex items-start gap-3">
                                <MessageCircle class="w-5 h-5 text-[#00a550] mt-0.5"/> 
                                <div>
                                    <a :href="`https://wa.me/${(page.props.site_config.whatsapp || '').replace(/\\D/g,'')}`" target="_blank" rel="noopener" class="block font-bold text-white hover:underline">
                                        {{ page.props.site_config.whatsapp }}
                                    </a>
                                    <span class="text-xs">WhatsApp</span>
                                </div>
                            </li>
                            <li v-if="page.props.site_config?.email" class="flex items-start gap-3">
                                <Mail class="w-5 h-5 text-[#00a550] mt-0.5" />
                                <div>
                                    <a :href="`mailto:${page.props.site_config.email}`" class="block font-bold text-white hover:underline">
                                        {{ page.props.site_config.email }}
                                    </a>
                                    <span class="text-xs">E-mail</span>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="inline-block border border-white/20 px-6 py-2 rounded text-sm hover:bg-white hover:text-[#002f6c] transition-colors font-bold">
                                    Fale Conosco
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-500">
                    <p class="text-center md:text-left">
                        &copy; 2026 Leilo Master. Todos os direitos reservados.
                        <span v-if="page.props.site_config?.cnpj" class="ml-2 inline-block">CNPJ: {{ page.props.site_config.cnpj }}</span>
                    </p>
                    <div class="flex items-center gap-4">
                        <span>Desenvolvido por</span>
                        <div class="font-bold text-gray-400">Trae AI</div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
