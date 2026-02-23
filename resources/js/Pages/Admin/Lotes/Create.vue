<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    leilao: Object,
    categories: Array,
    nextOrder: Number
});

const form = useForm({
    titulo: '',
    // Subtitulo is derived from leilao.titulo
    descricao: '',
    descricao_detalhada: '',
    valor_inicial: 0,
    valor_minimo_incremento: 10,
    valor_mercado: 0,
    ordem: props.nextOrder || 1,
    status: 'aberto',
    ano: '',
    quilometragem: 0,
    combustivel: '',
    cor: '',
    possui_chave: false,
    category_id: '',
    tipo_retomada: '',
    localizacao: '',
    prazo_documentacao: '',
    fotos: [],
    capa_index: 0,
});

const handleFileChange = (e) => {
    const files = Array.from(e.target.files);
    const validFiles = [];
    
    files.forEach(file => {
        if (file.type === 'image/png') {
            alert(`A imagem ${file.name} é PNG e não é permitida. Use JPG ou WEBP.`);
            return;
        }
        validFiles.push(file);
    });

    form.fotos = [...form.fotos, ...validFiles];
};

const removeFoto = (index) => {
    form.fotos = form.fotos.filter((_, i) => i !== index);
    if (form.capa_index === index) {
        form.capa_index = 0;
    } else if (form.capa_index > index) {
        form.capa_index--;
    }
};

const getPreviewUrl = (file) => {
    return URL.createObjectURL(file);
};

const submit = () => {
    form.post(route('admin.leiloes.lotes.store', props.leilao.id));
};
</script>

<template>
    <Head title="Novo Lote" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto">
            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Novo Lote para: {{ leilao.titulo }}
                    </h2>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Subtítulo Automático -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Subtítulo / Chamada (Automático)</label>
                            <div class="mt-1">
                                <input type="text" :value="leilao.titulo" disabled class="block w-full rounded-md border-slate-300 bg-slate-100 shadow-sm text-slate-500 sm:text-sm cursor-not-allowed" />
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Gerado automaticamente a partir do nome do leilão.</p>
                        </div>

                        <!-- Título e Tipo -->
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="titulo" class="block text-sm font-medium text-slate-700">Título do Lote</label>
                                <div class="mt-1">
                                    <input v-model="form.titulo" type="text" name="titulo" id="titulo" placeholder="Ex: FORD/KA HATCH SE 1.0 C (4P)" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                                </div>
                                <div v-if="form.errors.titulo" class="mt-1 text-sm text-red-600">{{ form.errors.titulo }}</div>
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-slate-700">Categoria</label>
                                <div class="mt-1">
                                    <select v-model="form.category_id" id="category_id" name="category_id" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                                        <option value="">Selecione...</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">{{ form.errors.category_id }}</div>
                            </div>

                            <div>
                                <label for="ano" class="block text-sm font-medium text-slate-700">Ano</label>
                                <div class="mt-1">
                                    <input v-model="form.ano" type="text" name="ano" id="ano" placeholder="Ex: 2019/2019" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                                </div>
                                <div v-if="form.errors.ano" class="mt-1 text-sm text-red-600">{{ form.errors.ano }}</div>
                            </div>
                        </div>

                        <!-- Detalhes do Veículo -->
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                            <div>
                                <label for="quilometragem" class="block text-sm font-medium text-slate-700">Quilometragem (Km)</label>
                                <div class="mt-1">
                                    <input v-model="form.quilometragem" type="number" name="quilometragem" id="quilometragem" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                                </div>
                            </div>

                            <div>
                                <label for="combustivel" class="block text-sm font-medium text-slate-700">Combustível</label>
                                <div class="mt-1">
                                    <select v-model="form.combustivel" id="combustivel" name="combustivel" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                                        <option value="">Selecione...</option>
                                        <option value="Flex">Flex</option>
                                        <option value="Gasolina">Gasolina</option>
                                        <option value="Diesel">Diesel</option>
                                        <option value="Híbrido">Híbrido</option>
                                        <option value="Elétrico">Elétrico</option>
                                        <option value="Álcool">Álcool</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="cor" class="block text-sm font-medium text-slate-700">Cor</label>
                                <div class="mt-1">
                                    <input v-model="form.cor" type="text" name="cor" id="cor" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                             <div>
                                <label for="possui_chave" class="block text-sm font-medium text-slate-700">Possui Chave?</label>
                                <div class="mt-1">
                                    <select v-model="form.possui_chave" id="possui_chave" name="possui_chave" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                                        <option :value="true">Sim</option>
                                        <option :value="false">Não</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="tipo_retomada" class="block text-sm font-medium text-slate-700">Tipo de Retomada</label>
                                <div class="mt-1">
                                    <select v-model="form.tipo_retomada" id="tipo_retomada" name="tipo_retomada" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required>
                                        <option value="">Selecione...</option>
                                        <option value="Recuperado de Financiamento">Recuperado de Financiamento</option>
                                        <option value="Seguradora">Seguradora</option>
                                        <option value="Frota">Frota</option>
                                        <option value="Execução Judicial">Execução Judicial</option>
                                        <option value="Outros">Outros</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="valor_mercado" class="block text-sm font-medium text-slate-700">Valor de Mercado (R$)</label>
                                <div class="mt-1">
                                    <input v-model="form.valor_mercado" type="number" step="0.01" name="valor_mercado" id="valor_mercado" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                                </div>
                            </div>
                        </div>

                        <!-- Local e Documentação -->
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="localizacao" class="block text-sm font-medium text-slate-700">Localização</label>
                                <div class="mt-1">
                                    <input v-model="form.localizacao" type="text" name="localizacao" id="localizacao" placeholder="Ex: PATIO MANAUS (MANAUS/AM)" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                                </div>
                            </div>

                            <div>
                                <label for="prazo_documentacao" class="block text-sm font-medium text-slate-700">Prazo Documentação</label>
                                <div class="mt-1">
                                    <input v-model="form.prazo_documentacao" type="text" name="prazo_documentacao" id="prazo_documentacao" placeholder="Ex: 45 dias úteis" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                                </div>
                            </div>
                        </div>

                        <!-- Valores e Status -->
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3 bg-slate-50 p-4 rounded-lg">
                            <div>
                                <label for="valor_inicial" class="block text-sm font-medium text-slate-700">Valor Inicial (R$)</label>
                                <div class="mt-1">
                                    <input v-model="form.valor_inicial" type="number" step="0.01" name="valor_inicial" id="valor_inicial" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                                </div>
                            </div>

                            <div>
                                <label for="valor_minimo_incremento" class="block text-sm font-medium text-slate-700">Incremento Mínimo (R$)</label>
                                <div class="mt-1">
                                    <input v-model="form.valor_minimo_incremento" type="number" step="0.01" name="valor_minimo_incremento" id="valor_minimo_incremento" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                                </div>
                            </div>

                            <div>
                                <label for="ordem" class="block text-sm font-medium text-slate-700">Ordem</label>
                                <div class="mt-1">
                                    <input v-model="form.ordem" type="number" name="ordem" id="ordem" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required />
                                </div>
                            </div>
                            
                            <div>
                                <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                                <div class="mt-1">
                                    <select v-model="form.status" id="status" name="status" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                        <option value="aberto">Aberto</option>
                                        <option value="arrematado">Arrematado</option>
                                        <option value="sem_lances">Sem Lances</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Descrições -->
                        <div>
                            <label for="descricao" class="block text-sm font-medium text-slate-700">Descrição Curta (Opcional)</label>
                            <div class="mt-1">
                                <textarea v-model="form.descricao" id="descricao" name="descricao" rows="2" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"></textarea>
                            </div>
                        </div>

                        <div>
                            <label for="descricao_detalhada" class="block text-sm font-medium text-slate-700">Descrição Detalhada</label>
                            <div class="mt-1">
                                <textarea v-model="form.descricao_detalhada" id="descricao_detalhada" name="descricao_detalhada" rows="6" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" required></textarea>
                            </div>
                        </div>

                        <!-- Upload de Fotos -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium leading-6 text-slate-900 mb-4">Fotos do Lote</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Adicionar Fotos (JPG, JPEG, WEBP)</label>
                                <input 
                                    type="file" 
                                    multiple 
                                    accept=".jpg,.jpeg,.webp" 
                                    @change="handleFileChange"
                                    class="block w-full text-sm text-slate-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-emerald-50 file:text-emerald-700
                                    hover:file:bg-emerald-100"
                                />
                                <p class="mt-1 text-xs text-red-500">Imagens PNG não são permitidas.</p>
                            </div>

                            <div v-if="form.fotos.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mt-4">
                                <div v-for="(file, index) in form.fotos" :key="index" class="relative group border rounded-lg p-2 bg-gray-50">
                                    <div class="aspect-w-16 aspect-h-12 w-full overflow-hidden rounded-md bg-gray-200 group-hover:opacity-75 h-32 flex items-center justify-center">
                                        <img :src="getPreviewUrl(file)" class="h-full w-full object-cover object-center" />
                                    </div>
                                    
                                    <button @click.prevent="removeFoto(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none z-10">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <div class="mt-2 flex items-center justify-center">
                                        <input 
                                            type="radio" 
                                            :id="`capa-${index}`" 
                                            :value="index" 
                                            v-model="form.capa_index"
                                            class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300"
                                        >
                                        <label :for="`capa-${index}`" class="ml-2 block text-sm font-medium text-gray-700">
                                            Capa
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div v-if="form.errors['fotos']" class="mt-1 text-sm text-red-600">{{ form.errors['fotos'] }}</div>
                        </div>

                        <div class="flex justify-end">
                            <Link :href="route('admin.lotes.index', { leilao_id: leilao.id })" class="rounded-md border border-slate-300 bg-white py-2 px-4 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">Cancelar</Link>
                            <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">Salvar Lote</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
