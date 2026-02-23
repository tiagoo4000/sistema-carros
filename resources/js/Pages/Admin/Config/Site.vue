<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Save, Building, MapPin, Phone, Mail, Globe, Image } from 'lucide-vue-next';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps({
    configs: Object
});

const form = useForm({
    site_name: props.configs.site_name || '',
    site_logo: null, // File upload
    site_favicon: null, // File upload
    company_location: props.configs.company_location || '',
    patio_location: props.configs.patio_location || '',
    cnpj: props.configs.cnpj || '',
    whatsapp: props.configs.whatsapp || '',
    phone: props.configs.phone || '',
    email: props.configs.email || '',
    textmebot_enabled: (props.configs.textmebot_enabled || '0') === '1',
    textmebot_api_key: props.configs.textmebot_api_key || '',
});

const testTo = ref('');
const testText = ref('Mensagem de teste via TextMeBot');
const testing = ref(false);
const testMessage = ref('');

const submit = () => {
    form.post(route('admin.layout.config.update'), {
        preserveScroll: true,
        forceFormData: true, // Important for file upload
    });
};

const submitWhatsAppTest = async () => {
    testMessage.value = '';
    if (!form.textmebot_enabled) {
        testMessage.value = 'Integração desativada.';
        return;
    }
    if (!form.textmebot_api_key) {
        testMessage.value = 'API Key não configurada.';
        return;
    }
    if (!testTo.value || !testText.value) {
        testMessage.value = 'Preencha telefone e mensagem.';
        return;
    }
    testing.value = true;
    try {
        const { data } = await axios.post(route('admin.api.whatsapp.send'), {
            recipient: testTo.value,
            text: testText.value,
        });
        if (data && data.ok) {
            testMessage.value = 'Mensagem enviada.';
        } else {
            testMessage.value = data?.error || 'Falha ao enviar.';
        }
    } catch (e) {
        testMessage.value = e?.response?.data?.error || e.message || 'Erro de envio.';
    } finally {
        testing.value = false;
    }
};
</script>

<template>
    <Head title="Configuração do Site" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto py-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Configuração Geral</h1>
                <p class="text-gray-600 mt-1">Gerencie as informações principais exibidas no site.</p>
            </div>

            <form @submit.prevent="submit" class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="p-6 space-y-6">
                    
                    <!-- Identidade -->
                    <div class="border-b border-gray-100 pb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <Globe class="w-5 h-5 text-indigo-500" />
                            Identidade Visual
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Site</label>
                                <input 
                                    v-model="form.site_name"
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Ex: Leilo Master"
                                >
                                <div v-if="form.errors.site_name" class="text-red-500 text-sm mt-1">{{ form.errors.site_name }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Logo do Site</label>
                                <div class="flex items-center gap-4">
                                    <div v-if="configs.site_logo" class="h-12 w-auto border rounded p-1">
                                        <img :src="configs.site_logo" alt="Logo Atual" class="h-full w-auto object-contain">
                                    </div>
                                    <input 
                                        type="file" 
                                        @input="form.site_logo = $event.target.files[0]"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                        accept="image/*"
                                    >
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Recomendado: PNG transparente, máx 2MB.</p>
                                <div v-if="form.errors.site_logo" class="text-red-500 text-sm mt-1">{{ form.errors.site_logo }}</div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Favicon do Site</label>
                                <div class="flex items-center gap-4">
                                    <div v-if="configs.site_favicon" class="h-10 w-10 border rounded p-1">
                                        <img :src="configs.site_favicon" alt="Favicon Atual" class="h-full w-full object-contain">
                                    </div>
                                    <input 
                                        type="file" 
                                        @input="form.site_favicon = $event.target.files[0]"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                        accept="image/*, .ico"
                                    >
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Recomendado: PNG ou ICO, 32x32px ou 64x64px.</p>
                                <div v-if="form.errors.site_favicon" class="text-red-500 text-sm mt-1">{{ form.errors.site_favicon }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Localização -->
                    <div class="border-b border-gray-100 pb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <MapPin class="w-5 h-5 text-indigo-500" />
                            Localização
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço da Empresa</label>
                                <input 
                                    v-model="form.company_location"
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Endereço completo da sede"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço do Pátio</label>
                                <input 
                                    v-model="form.patio_location"
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Endereço onde os bens estão localizados"
                                >
                            </div>
                            <div class="max-w-sm">
                                <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                                <input 
                                    v-model="form.cnpj"
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="00.000.000/0000-00"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Contato -->
                    <div class="border-b border-gray-100 pb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <Phone class="w-5 h-5 text-indigo-500" />
                            Contato
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                                <input 
                                    v-model="form.whatsapp"
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="(00) 00000-0000"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone Fixo</label>
                                <input 
                                    v-model="form.phone"
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="(00) 0000-0000"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                                <input 
                                    v-model="form.email"
                                    type="email" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="contato@exemplo.com"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp API (TextMeBot) -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <Image class="w-5 h-5 text-indigo-500" />
                            WhatsApp API (TextMeBot)
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                            <div class="md:col-span-1">
                                <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                                    <input type="checkbox" v-model="form.textmebot_enabled" class="rounded border-gray-300" />
                                    Ativar integração
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Habilita envio via TextMeBot a partir do painel.</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                                <input 
                                    v-model="form.textmebot_api_key"
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Insira sua chave da API TextMeBot"
                                >
                                <div v-if="form.errors.textmebot_api_key" class="text-red-500 text-sm mt-1">{{ form.errors.textmebot_api_key }}</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone para teste</label>
                                <input 
                                    v-model="testTo"
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="(00) 00000-0000"
                                >
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem de teste</label>
                                <input 
                                    v-model="testText"
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Mensagem de teste via TextMeBot"
                                >
                            </div>
                            <div class="md:col-span-3 flex items-center gap-3">
                                <button 
                                    type="button"
                                    :disabled="testing || !testTo || !testText"
                                    @click="submitWhatsAppTest"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50"
                                >
                                    Enviar mensagem de teste
                                </button>
                                <span class="text-sm" :class="testMessage ? 'text-gray-700' : 'text-gray-400'">{{ testMessage || 'Preencha e clique para testar' }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-colors"
                    >
                        <Save class="w-4 h-4 mr-2" />
                        Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
