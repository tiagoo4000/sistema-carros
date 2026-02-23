<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { 
  Lock, Mail, User, Phone, FileText, Building2, MapPin, CheckCircle, XCircle 
} from 'lucide-vue-next';

const estados = [
  'AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'
];

const form = useForm({
  tipo_pessoa: 'pf',
  // PF
  nome: '', apelido: '', cpf: '', rg: '', orgao_expedidor: '', data_nascimento: '', renda_mensal: '',
  telefone: '', celular2: '', telefone_fixo: '', ocupacao: '',
  // PJ
  cnpj: '', razao_social: '', nome_fantasia: '', inscricao_estadual: '', data_abertura: '', faturamento_mensal: '',
  telefone_comercial: '', responsavel_nome: '', responsavel_cpf: '',
  // Comuns
  email: '',
  senha: '', senha_confirmation: '',
  // Endereço
  cep: '', endereco: '', numero: '', bairro: '', estado: '', cidade: '', complemento: '',
  // Preferências
  interesses: [],
  como_conheceu: '',
  aceite_termos: false,
});

const interessesOptions = ['veiculos','motos','onibus','caminhoes','imoveis','equipamentos'];
const comoConheceuOptions = [
  { value: 'google', label: 'Google' },
  { value: 'facebook', label: 'Facebook' },
  { value: 'instagram', label: 'Instagram' },
  { value: 'radio', label: 'Rádio' },
  { value: 'internet', label: 'Internet' },
  { value: 'indicacao', label: 'Indicação' },
  { value: 'outros', label: 'Outros' },
];

const checklist = computed(() => {
  const v = form.senha || '';
  return {
    len: v.length >= 8,
    up: /[A-Z]/.test(v),
    low: /[a-z]/.test(v),
    num: /[0-9]/.test(v),
  };
});

const maskCPF = (val) => val.replace(/\D/g,'').slice(0,11).replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2');
const maskCNPJ = (val) => val.replace(/\D/g,'').slice(0,14)
  .replace(/^(\d{2})(\d)/,'$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/,'$1.$2.$3')
  .replace(/\.(\d{3})(\d)/,'$1/$2').replace(/(\d{4})(\d{2})$/,'$1-$2');
const maskPhone = (val) => val.replace(/\D/g,'').slice(0,11).replace(/^(\d{2})(\d)/,'($1) $2').replace(/(\d{5})(\d{4})$/,'$1-$2');
const maskCEP = (val) => val.replace(/\D/g,'').slice(0,8).replace(/(\d{5})(\d{3})/,'$1-$2');
const lookupCEP = async (cep) => {
  const digits = (cep || '').replace(/\D/g, '');
  if (digits.length !== 8) return { erro: true };
  try {
    if (cepCache.has(digits)) return cepCache.get(digits);
    if (cepAbortCtrl) try { cepAbortCtrl.abort() } catch {}
    cepAbortCtrl = new AbortController();
    const resp = await fetch(`https://viacep.com.br/ws/${digits}/json/`, { signal: cepAbortCtrl.signal });
    if (!resp.ok) return { erro: true };
    const data = await resp.json();
    if (data.erro) return { erro: true };
    const out = {
      erro: false,
      logradouro: data.logradouro || '',
      bairro: data.bairro || '',
      localidade: data.localidade || '',
      uf: data.uf || '',
    };
    cepCache.set(digits, out);
    return out;
  } catch {
    return { erro: true };
  }
};
const cepCache = new Map();
let cepAbortCtrl = null;
const onCPFInput = (e) => form.cpf = maskCPF(e.target.value);
const onCNPJInput = (e) => form.cnpj = maskCNPJ(e.target.value);
const onRespCPFInput = (e) => form.responsavel_cpf = maskCPF(e.target.value);
const onPhoneInput = (e, field) => form[field] = maskPhone(e.target.value);
const onCEPInput = async (e) => {
  form.cep = maskCEP(e.target.value);
  const digits = (form.cep || '').replace(/\D/g, '');
  if (digits.length !== 8) return;
  if (cepCache.has(digits)) {
    const dataCached = cepCache.get(digits);
    if (!dataCached.erro) {
      form.endereco = dataCached.logradouro || '';
      form.bairro = dataCached.bairro || '';
      form.estado = dataCached.uf || '';
      form.cidade = dataCached.localidade || '';
      return;
    }
  }
  const data = await lookupCEP(form.cep);
  if (!data.erro) {
    form.endereco = data.logradouro || '';
    form.bairro = data.bairro || '';
    form.estado = data.uf || '';
    form.cidade = data.localidade || '';
  }
};
const onCEPBlur = async () => {
  const digits = (form.cep || '').replace(/\D/g, '');
  if (digits.length !== 8) return;
  if (cepCache.has(digits)) {
    const dataCached = cepCache.get(digits);
    if (!dataCached.erro) {
      form.endereco = dataCached.logradouro || '';
      form.bairro = dataCached.bairro || '';
      form.estado = dataCached.uf || '';
      form.cidade = dataCached.localidade || '';
      return;
    }
  }
  const data = await lookupCEP(form.cep);
  if (!data.erro) {
    form.endereco = data.logradouro || '';
    form.bairro = data.bairro || '';
    form.estado = data.uf || '';
    form.cidade = data.localidade || '';
  }
};

watch(() => form.tipo_pessoa, (t) => {
  if (t === 'pf') {
    form.cnpj = ''; form.razao_social=''; form.nome_fantasia=''; form.inscricao_estadual='';
    form.data_abertura=''; form.faturamento_mensal=''; form.telefone_comercial='';
    form.responsavel_nome=''; form.responsavel_cpf='';
  } else {
    form.nome=''; form.apelido=''; form.cpf=''; form.rg=''; form.orgao_expedidor='';
    form.data_nascimento=''; form.renda_mensal=''; form.telefone=''; form.celular2=''; form.telefone_fixo=''; form.ocupacao='';
  }
});

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('senha','senha_confirmation'),
  });
};
</script>

<template>
  <Head title="Criar Conta" />
  <div class="min-h-screen bg-gray-50 py-10">
    <div class="max-w-5xl mx-auto px-4">
      <div class="mb-8 text-center">
        <Link :href="route('home')" class="inline-flex items-center justify-center text-3xl font-extrabold tracking-tight text-[#002f6c]">
          LEILÃO PRO
        </Link>
        <p class="mt-2 text-gray-500">Crie sua conta para participar de leilões com segurança.</p>
      </div>

      <div class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
          <h2 class="text-lg font-bold text-[#002f6c]">Cadastro</h2>
          <div class="inline-flex bg-white border border-gray-200 rounded-lg p-1">
            <button type="button"
              @click="form.tipo_pessoa='pf'"
              :class="['px-4 py-1.5 text-sm font-medium rounded-md', form.tipo_pessoa==='pf' ? 'bg-[#00a550] text-white' : 'text-gray-600 hover:bg-gray-100']">
              Pessoa Física
            </button>
            <button type="button"
              @click="form.tipo_pessoa='pj'"
              :class="['px-4 py-1.5 text-sm font-medium rounded-md', form.tipo_pessoa==='pj' ? 'bg-[#00a550] text-white' : 'text-gray-600 hover:bg-gray-100']">
              Pessoa Jurídica
            </button>
          </div>
        </div>

        <form @submit.prevent="submit" class="p-6 space-y-8">
          <!-- Dados Cadastrais -->
          <div>
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">Dados Cadastrais</h3>
            <!-- Pessoa Física -->
            <div v-if="form.tipo_pessoa==='pf'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-700">Nome Completo</label>
                <div class="mt-1">
                  <input v-model="form.nome" type="text" class="input" />
                </div>
                <p v-if="form.errors.nome" class="error">{{ form.errors.nome }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Apelido (exibido nos lances)</label>
                <input v-model="form.apelido" type="text" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">CPF</label>
                <input :value="form.cpf" @input="onCPFInput" type="text" placeholder="000.000.000-00" class="input mt-1" />
                <p v-if="form.errors.cpf" class="error">{{ form.errors.cpf }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">RG</label>
                <input v-model="form.rg" type="text" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Órgão Expedidor</label>
                <input v-model="form.orgao_expedidor" type="text" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Data de Nascimento</label>
                <input v-model="form.data_nascimento" type="date" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Renda Mensal</label>
                <input v-model="form.renda_mensal" type="number" step="0.01" min="0" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Celular</label>
                <input :value="form.telefone" @input="(e)=>onPhoneInput(e,'telefone')" type="text" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Celular 2 (opcional)</label>
                <input :value="form.celular2" @input="(e)=>onPhoneInput(e,'celular2')" type="text" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Telefone Fixo (opcional)</label>
                <input :value="form.telefone_fixo" @input="(e)=>onPhoneInput(e,'telefone_fixo')" type="text" class="input mt-1" />
              </div>
              <div class="md:col-span-3">
                <label class="text-sm font-medium text-gray-700">Ocupação Profissional</label>
                <input v-model="form.ocupacao" type="text" class="input mt-1" />
              </div>
            </div>

            <!-- Pessoa Jurídica -->
            <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="text-sm font-medium text-gray-700">CNPJ</label>
                <input :value="form.cnpj" @input="onCNPJInput" placeholder="00.000.000/0000-00" type="text" class="input mt-1" />
                <p v-if="form.errors.cnpj" class="error">{{ form.errors.cnpj }}</p>
              </div>
              <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-700">Razão Social</label>
                <input v-model="form.razao_social" type="text" class="input mt-1" />
              </div>
              <div class="md:col-span-3">
                <label class="text-sm font-medium text-gray-700">Nome Fantasia</label>
                <input v-model="form.nome_fantasia" type="text" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Inscrição Estadual</label>
                <input v-model="form.inscricao_estadual" type="text" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Data de Abertura</label>
                <input v-model="form.data_abertura" type="date" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Faturamento Mensal</label>
                <input v-model="form.faturamento_mensal" type="number" step="0.01" min="0" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Telefone Comercial</label>
                <input :value="form.telefone_comercial" @input="(e)=>onPhoneInput(e,'telefone_comercial')" type="text" class="input mt-1" />
              </div>
              <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-700">Nome do Responsável</label>
                <input v-model="form.responsavel_nome" type="text" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">CPF do Responsável</label>
                <input :value="form.responsavel_cpf" @input="onRespCPFInput" placeholder="000.000.000-00" type="text" class="input mt-1" />
              </div>
              <div class="md:col-span-3">
                <label class="text-sm font-medium text-gray-700">Email</label>
                <input v-model="form.email" type="email" class="input mt-1" />
                <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
              </div>
            </div>
          </div>

          <!-- Senha -->
          <div>
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">Senha</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="text-sm font-medium text-gray-700">Senha</label>
                <input v-model="form.senha" type="password" class="input mt-1" />
                <p v-if="form.errors.senha" class="error">{{ form.errors.senha }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Confirmar Senha</label>
                <input v-model="form.senha_confirmation" type="password" class="input mt-1" />
              </div>
            </div>
            <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
              <div :class="['check', checklist.len ? 'ok' : 'no']">
                <component :is="checklist.len ? CheckCircle : XCircle" class="icon" />
                Mín. 8 caracteres
              </div>
              <div :class="['check', checklist.up ? 'ok' : 'no']">
                <component :is="checklist.up ? CheckCircle : XCircle" class="icon" />
                1 maiúscula
              </div>
              <div :class="['check', checklist.low ? 'ok' : 'no']">
                <component :is="checklist.low ? CheckCircle : XCircle" class="icon" />
                1 minúscula
              </div>
              <div :class="['check', checklist.num ? 'ok' : 'no']">
                <component :is="checklist.num ? CheckCircle : XCircle" class="icon" />
                1 número
              </div>
            </div>
          </div>

          <!-- Endereço -->
          <div>
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">Endereço</h3>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
              <div>
                <label class="text-sm font-medium text-gray-700">CEP</label>
                <input :value="form.cep" @input="onCEPInput" @blur="onCEPBlur" placeholder="00000-000" type="text" class="input mt-1" />
              </div>
              <div class="md:col-span-3">
                <label class="text-sm font-medium text-gray-700">Endereço</label>
                <input v-model="form.endereco" type="text" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Número</label>
                <input v-model="form.numero" type="text" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Bairro</label>
                <input v-model="form.bairro" type="text" class="input mt-1" />
              </div>
              <div>
                <label class="text-sm font-medium text-gray-700">Estado</label>
                <select v-model="form.estado" class="input mt-1">
                  <option value="">Selecione</option>
                  <option v-for="uf in estados" :key="uf" :value="uf">{{ uf }}</option>
                </select>
              </div>
              <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-700">Cidade</label>
                <input v-model="form.cidade" type="text" class="input mt-1" />
              </div>
              <div class="md:col-span-3">
                <label class="text-sm font-medium text-gray-700">Complemento (opcional)</label>
                <input v-model="form.complemento" type="text" class="input mt-1" />
              </div>
            </div>
          </div>

          <!-- Interesses -->
          <div>
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">Interesses</h3>
            <div class="flex flex-wrap gap-2">
              <button type="button"
                v-for="opt in interessesOptions" :key="opt"
                @click="form.interesses = form.interesses.includes(opt) ? form.interesses.filter(i=>i!==opt) : [...form.interesses, opt]"
                :class="['px-3 py-1.5 rounded-full text-sm border', form.interesses.includes(opt) ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50']">
                {{ opt.charAt(0).toUpperCase() + opt.slice(1) }}
              </button>
            </div>
          </div>

          <!-- Como conheceu -->
          <div>
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">Como nos conheceu?</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
              <label v-for="opt in comoConheceuOptions" :key="opt.value" class="flex items-center gap-2">
                <input type="radio" :value="opt.value" v-model="form.como_conheceu" />
                <span class="text-sm text-gray-700">{{ opt.label }}</span>
              </label>
            </div>
          </div>

          <!-- Email -->
          <div>
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">Contato</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="text-sm font-medium text-gray-700">Email</label>
                <input v-model="form.email" type="email" class="input mt-1" />
                <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
              </div>
            </div>
          </div>

          <!-- Aceite -->
          <div class="flex items-start gap-2">
            <input type="checkbox" v-model="form.aceite_termos" class="mt-1" />
            <span class="text-sm text-gray-700">
              Declaro que li e concordo com os Termos de Uso, Regulamento e Política de Privacidade.
            </span>
          </div>
          <p v-if="form.errors.aceite_termos" class="error">{{ form.errors.aceite_termos }}</p>

          <div class="pt-2">
            <button type="submit" :disabled="form.processing"
              class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 rounded-xl bg-[#00a550] text-white font-semibold hover:bg-[#02964b] transition disabled:opacity-50">
              <Lock class="w-5 h-5" />
              Salvar Cadastro
            </button>
          </div>
        </form>
      </div>

      <div class="text-center mt-4 text-sm">
        Já tem uma conta?
        <Link :href="route('login')" class="text-[#00a550] font-medium hover:underline">Entrar</Link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.input { @apply w-full rounded-lg border border-gray-200 focus:ring-emerald-500 focus:border-emerald-500 px-3 py-2; }
.error { @apply mt-1 text-xs text-red-600; }
.check { @apply inline-flex items-center gap-1 px-2 py-1 rounded border text-gray-600; }
.check.ok { @apply bg-green-50 border-green-200 text-green-700; }
.check.no { @apply bg-red-50 border-red-200 text-red-700; }
.icon { @apply w-4 h-4; }
</style>
