<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { 
  User, Phone, MapPin, Shield, SlidersHorizontal, Save, AlertCircle, Loader2, Eye, EyeOff 
} from 'lucide-vue-next';
import Swal from 'sweetalert2';

const props = defineProps({
  mode: { type: String, default: 'user' }, // 'user' | 'admin'
  user: { type: Object, required: true },
  submitUrl: { type: String, required: true },
  passwordUrl: { type: String, default: null }
});

const page = usePage();
const tabs = [
  { id: 'dados', label: 'Dados pessoais', icon: User },
  { id: 'contato', label: 'Contato', icon: Phone },
  { id: 'endereco', label: 'Endereço', icon: MapPin },
  { id: 'seguranca', label: 'Segurança', icon: Shield },
  { id: 'preferencias', label: 'Preferências', icon: SlidersHorizontal },
];
const activeTab = ref('dados');

const initial = ref(JSON.parse(JSON.stringify(props.user || {})));
const form = useForm({
  nome: props.user.nome || '',
  apelido: props.user.apelido || '',
  email: props.user.email || '',
  cpf: props.user.cpf || '',
  rg: props.user.rg || '',
  orgao_expedidor: props.user.orgao_expedidor || '',
  data_nascimento: props.user.data_nascimento || '',
  renda_mensal: props.user.renda_mensal || '',
  telefone: props.user.telefone || '',
  telefone_fixo: props.user.telefone_fixo || '',
  celular2: props.user.celular2 || '',
  ocupacao: props.user.ocupacao || '',
  // Endereço
  cep: props.user.cep || '',
  endereco: props.user.endereco || '',
  numero: props.user.numero || '',
  bairro: props.user.bairro || '',
  cidade: props.user.cidade || '',
  estado: props.user.estado || '',
  complemento: props.user.complemento || '',
  // Preferências
  interesses: Array.isArray(props.user.interesses) ? props.user.interesses.join(', ') : (props.user.interesses || ''),
  como_conheceu: props.user.como_conheceu || '',
  // Admin-only flags
  bloqueado: props.user.bloqueado || false,
  documentos_validos: props.user.documentos_validos || false,
  // Admin password (optional in same submit)
  senha: '',
  senha_confirmation: ''
});

const isChanged = (key) => {
  const current = form[key];
  const orig = initial.value?.[key] ?? '';
  return String(current ?? '') !== String(orig ?? '');
};

const disabledNameCpf = computed(() => props.mode === 'user');
const infoBlocked = computed(() => props.mode === 'user');

const submitting = computed(() => form.processing);
const save = () => {
  const payload = { ...form };
  if (props.mode === 'user') {
    delete payload.nome;
    delete payload.cpf;
  }
  form.transform((data) => {
    const out = { ...data };
    if (typeof out.interesses === 'string') {
      out.interesses = out.interesses
        .split(',')
        .map(s => s.trim())
        .filter(Boolean);
    }
    return out;
  }).put(props.submitUrl, { preserveScroll: true });
};

const cepLoading = ref(false);
const cepError = ref('');
const onCepBlur = async () => {
  cepError.value = '';
  const cep = (form.cep || '').replace(/\D/g, '');
  if (!cep || cep.length !== 8) return;
  try {
    cepLoading.value = true;
    if (cepCache.has(cep)) {
      const data = cepCache.get(cep);
      if (!data.erro) {
        form.endereco = data.logradouro || form.endereco;
        form.bairro = data.bairro || form.bairro;
        form.cidade = data.localidade || form.cidade;
        form.estado = data.uf || form.estado;
        return;
      }
    }
    if (cepAbortCtrl) try { cepAbortCtrl.abort() } catch {}
    cepAbortCtrl = new AbortController();
    const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`, { signal: cepAbortCtrl.signal });
    const data = await res.json();
    if (data.erro) {
      cepError.value = 'CEP não encontrado';
    } else {
      form.endereco = data.logradouro || form.endereco;
      form.bairro = data.bairro || form.bairro;
      form.cidade = data.localidade || form.cidade;
      form.estado = data.uf || form.estado;
      cepCache.set(cep, {
        erro: false,
        logradouro: data.logradouro || '',
        bairro: data.bairro || '',
        localidade: data.localidade || '',
        uf: data.uf || '',
      });
    }
  } catch (e) {
    cepError.value = 'Erro ao buscar CEP';
  } finally {
    cepLoading.value = false;
  }
};

const maskCpf = () => {
  let v = (form.cpf || '').replace(/\D/g, '').slice(0, 11);
  v = v.replace(/(\d{3})(\d)/, '$1.$2');
  v = v.replace(/(\d{3})(\d)/, '$1.$2');
  v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
  form.cpf = v;
};
const maskTel = (field) => {
  let v = (form[field] || '').replace(/\D/g, '').slice(0, 11);
  if (v.length <= 10) {
    v = v.replace(/(\d{2})(\d)/, '($1) $2');
    v = v.replace(/(\d{4})(\d)/, '$1-$2');
  } else {
    v = v.replace(/(\d{2})(\d)/, '($1) $2');
    v = v.replace(/(\d{5})(\d)/, '$1-$2');
  }
  form[field] = v;
};
const maskCep = () => {
  let v = (form.cep || '').replace(/\D/g, '').slice(0, 8);
  v = v.replace(/(\d{5})(\d)/, '$1-$2');
  form.cep = v;
};
const cepCache = new Map();
let cepAbortCtrl = null;

const pwd = useForm({ current_password: '', new_password: '', new_password_confirmation: '' });
const pwdShow = ref({ current: false, pass: false, confirm: false });
const pwdStrength = computed(() => {
  const p = pwd.new_password || '';
  let s = 0;
  if (p.length >= 8) s++;
  if (/[A-Z]/.test(p)) s++;
  if (/[0-9]/.test(p)) s++;
  if (/[^A-Za-z0-9]/.test(p)) s++;
  return s;
});
const pwdSaving = computed(() => pwd.processing);
const updatePassword = () => {
  if (!props.passwordUrl) return;
  pwd.put(props.passwordUrl, { 
    preserveScroll: true, 
    onSuccess: () => {
      pwd.current_password = '';
      pwd.new_password = '';
      pwd.new_password_confirmation = '';
      const msg = page.props?.flash?.success || 'Senha atualizada com sucesso.';
      Swal.fire({
        icon: 'success',
        title: 'Sucesso',
        text: msg,
        confirmButtonColor: '#00a550'
      });
    },
    onError: (errors) => {
      const msgs = [];
      if (errors?.current_password) msgs.push(errors.current_password);
      if (errors?.new_password) msgs.push(errors.new_password);
      if (errors?.new_password_confirmation) msgs.push(errors.new_password_confirmation);
      const flashErr = page.props?.flash?.error;
      if (flashErr) msgs.push(flashErr);
      Swal.fire({
        icon: 'error',
        title: 'Não foi possível atualizar a senha',
        html: msgs.length ? `<ul style="text-align:left; margin:0; padding-left:18px;">${msgs.map(m=>`<li>${m}</li>`).join('')}</ul>` : 'Verifique os campos e tente novamente.',
        confirmButtonColor: '#d33'
      });
    }
  });
};
</script>

<template>
  <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <User class="w-5 h-5 text-[#002f6c]" />
        <h3 class="font-bold text-lg text-[#002f6c]">Editar Perfil</h3>
      </div>
      <div v-if="page.props.flash?.success" class="inline-flex items-center gap-2 text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded">
        <span>Dados atualizados</span>
      </div>
    </div>

    <div class="px-6 pt-4">
      <div class="flex gap-2 overflow-x-auto pb-2">
        <button
          v-for="t in tabs"
          :key="t.id"
          @click="activeTab = t.id"
          :class="[
            'inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg border',
            activeTab === t.id ? 'bg-[#002f6c] text-white border-[#002f6c]' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'
          ]"
        >
          <component :is="t.icon" class="w-4 h-4" /> {{ t.label }}
        </button>
      </div>
    </div>

    <div class="p-6 space-y-8">
      <div v-show="activeTab==='dados'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div :class="isChanged('nome') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
          <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
          <input v-model="form.nome" :disabled="disabledNameCpf" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div :class="isChanged('email') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
          <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
          <input v-model="form.email" type="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div :class="isChanged('cpf') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
          <label class="block text-sm font-medium text-gray-700 mb-1">CPF</label>
          <input v-model="form.cpf" @input="maskCpf" :disabled="disabledNameCpf" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="000.000.000-00">
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div :class="isChanged('rg') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
            <label class="block text-sm font-medium text-gray-700 mb-1">RG</label>
            <input v-model="form.rg" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          <div :class="isChanged('orgao_expedidor') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
            <label class="block text-sm font-medium text-gray-700 mb-1">Órgão Expedidor</label>
            <input v-model="form.orgao_expedidor" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div :class="isChanged('data_nascimento') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
            <label class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
            <input v-model="form.data_nascimento" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          <div :class="isChanged('renda_mensal') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
            <label class="block text-sm font-medium text-gray-700 mb-1">Renda mensal</label>
            <input v-model="form.renda_mensal" type="number" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          </div>
        </div>
        <div v-if="infoBlocked" class="md:col-span-2 flex items-center gap-2 text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2 rounded">
          <AlertCircle class="w-4 h-4" />
          <span>Nome e CPF não podem ser alterados após o cadastro por segurança.</span>
        </div>
        <div v-if="props.mode==='admin'" class="md:col-span-2 flex items-center gap-6">
          <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" v-model="form.bloqueado" class="rounded border-gray-300"> Bloquear edição pelo usuário
          </label>
          <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" v-model="form.documentos_validos" class="rounded border-gray-300"> Documentos verificados
          </label>
        </div>
      </div>

      <div v-show="activeTab==='contato'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div :class="isChanged('telefone') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
          <label class="block text-sm font-medium text-gray-700 mb-1">Celular principal</label>
          <input v-model="form.telefone" @input="maskTel('telefone')" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="(00) 00000-0000">
        </div>
        <div :class="isChanged('telefone_fixo') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
          <label class="block text-sm font-medium text-gray-700 mb-1">Telefone fixo</label>
          <input v-model="form.telefone_fixo" @input="maskTel('telefone_fixo')" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="(00) 0000-0000">
        </div>
        <div :class="isChanged('celular2') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
          <label class="block text-sm font-medium text-gray-700 mb-1">Celular alternativo</label>
          <input v-model="form.celular2" @input="maskTel('celular2')" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="(00) 00000-0000">
        </div>
        <div :class="isChanged('ocupacao') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''" class="md:col-span-3">
          <label class="block text-sm font-medium text-gray-700 mb-1">Ocupação</label>
          <input v-model="form.ocupacao" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
      </div>

      <div v-show="activeTab==='endereco'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div :class="isChanged('cep') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
          <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
          <div class="relative">
            <input v-model="form.cep" @input="maskCep" @blur="onCepBlur" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10" placeholder="00000-000">
            <Loader2 v-if="cepLoading" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 animate-spin text-gray-500" />
          </div>
          <p v-if="cepError" class="text-xs text-red-600 mt-1">{{ cepError }}</p>
        </div>
        <div :class="isChanged('endereco') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''" class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
          <input v-model="form.endereco" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div :class="isChanged('numero') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
          <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
          <input v-model="form.numero" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div :class="isChanged('bairro') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
          <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
          <input v-model="form.bairro" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div class="grid grid-cols-3 gap-3">
          <div :class="isChanged('cidade') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''" class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
            <input v-model="form.cidade" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          <div :class="isChanged('estado') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
            <label class="block text-sm font-medium text-gray-700 mb-1">UF</label>
            <input v-model="form.estado" maxlength="2" class="uppercase w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          </div>
        </div>
        <div :class="isChanged('complemento') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''" class="md:col-span-3">
          <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
          <input v-model="form.complemento" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
      </div>

      <div v-show="activeTab==='seguranca'" class="space-y-6">
        <div v-if="props.mode==='user' && passwordUrl" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
          <h4 class="font-semibold text-gray-800 mb-3">Alterar senha</h4>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Senha atual</label>
              <div class="relative">
                <input :type="pwdShow.current ? 'text':'password'" v-model="pwd.current_password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500" @click="pwdShow.current=!pwdShow.current">
                  <component :is="pwdShow.current?EyeOff:Eye" class="w-4 h-4"/>
                </button>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nova senha</label>
              <div class="relative">
                <input :type="pwdShow.pass ? 'text':'password'" v-model="pwd.new_password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500" @click="pwdShow.pass=!pwdShow.pass">
                  <component :is="pwdShow.pass?EyeOff:Eye" class="w-4 h-4"/>
                </button>
              </div>
              <div class="mt-2 h-2 bg-gray-200 rounded">
                <div :class="[
                  'h-2 rounded',
                  pwdStrength<=1 ? 'bg-red-500 w-1/4' : pwdStrength===2 ? 'bg-amber-500 w-2/4' : pwdStrength===3 ? 'bg-blue-500 w-3/4' : 'bg-emerald-500 w-full'
                ]"></div>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirmação</label>
              <div class="relative">
                <input :type="pwdShow.confirm ? 'text':'password'" v-model="pwd.new_password_confirmation" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500" @click="pwdShow.confirm=!pwdShow.confirm">
                  <component :is="pwdShow.confirm?EyeOff:Eye" class="w-4 h-4"/>
                </button>
              </div>
            </div>
          </div>
          <div class="flex justify-end mt-4">
            <button type="button" :disabled="pwdSaving" @click="updatePassword" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-[#002f6c] text-white hover:bg-blue-800 disabled:opacity-60">
              <Loader2 v-if="pwdSaving" class="w-4 h-4 animate-spin" />
              <span>Atualizar senha</span>
            </button>
          </div>
        </div>

        <div v-if="props.mode==='admin'">
          <h4 class="font-semibold text-gray-800 mb-3">Senha (opcional)</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nova senha</label>
              <input v-model="form.senha" type="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirmação</label>
              <input v-model="form.senha_confirmation" type="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
          </div>
        </div>
      </div>

      <div v-show="activeTab==='preferencias'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div :class="isChanged('interesses') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
          <label class="block text-sm font-medium text-gray-700 mb-1">Interesses (separe por vírgulas)</label>
          <input v-model="form.interesses" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="ex.: carros, imóveis">
        </div>
        <div :class="isChanged('como_conheceu') ? 'ring-1 ring-blue-200 rounded-lg p-2 -m-2' : ''">
          <label class="block text-sm font-medium text-gray-700 mb-1">Como nos conheceu</label>
          <input v-model="form.como_conheceu" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="ex.: Google, Instagram, Indicação">
        </div>
      </div>
    </div>

    <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
      <button type="button" @click="$emit('cancel')" class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</button>
      <button type="button" :disabled="submitting" @click="save" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-[#00a550] text-white hover:bg-green-700 disabled:opacity-60">
        <Loader2 v-if="submitting" class="w-4 h-4 animate-spin" />
        <Save v-else class="w-4 h-4" />
        <span>Salvar alterações</span>
      </button>
    </div>
  </div>
</template>
