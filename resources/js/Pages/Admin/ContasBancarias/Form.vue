<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
  initial: Object
});

const form = useForm({
  tipo_documento: props.initial?.tipo_documento ?? 'cpf',
  nome_completo: props.initial?.nome_completo ?? '',
  cpf: props.initial?.cpf ?? '',
  razao_social: props.initial?.razao_social ?? '',
  cnpj: props.initial?.cnpj ?? '',
  banco: props.initial?.banco ?? '',
  agencia: props.initial?.agencia ?? '',
  conta: props.initial?.conta ?? '',
  tipo_conta: props.initial?.tipo_conta ?? 'corrente',
  chave_pix: props.initial?.chave_pix ?? '',
  qr_code_pix: null,
  ativa: props.initial?.ativa ?? true,
  padrao: props.initial?.padrao ?? false,
});

const tipo = ref(form.tipo_documento);
watch(tipo, (v) => form.tipo_documento = v);
</script>

<template>
  <div class="grid grid-cols-1 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
      <select v-model="tipo" class="w-full rounded border-gray-300">
        <option value="cpf">Pessoa Física (CPF)</option>
        <option value="cnpj">Pessoa Jurídica (CNPJ)</option>
      </select>
    </div>

    <div v-if="tipo === 'cpf'">
      <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
      <input v-model="form.nome_completo" class="w-full rounded border-gray-300" />
      <label class="block text-sm font-medium text-gray-700 mb-1 mt-3">CPF</label>
      <input v-model="form.cpf" class="w-full rounded border-gray-300" />
    </div>
    <div v-else>
      <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
      <input v-model="form.razao_social" class="w-full rounded border-gray-300" />
      <label class="block text-sm font-medium text-gray-700 mb-1 mt-3">CNPJ</label>
      <input v-model="form.cnpj" class="w-full rounded border-gray-300" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Banco</label>
        <input v-model="form.banco" class="w-full rounded border-gray-300" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Agência</label>
        <input v-model="form.agencia" class="w-full rounded border-gray-300" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Conta</label>
        <input v-model="form.conta" class="w-full rounded border-gray-300" />
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Conta</label>
        <select v-model="form.tipo_conta" class="w-full rounded border-gray-300">
          <option value="corrente">Corrente</option>
          <option value="poupanca">Poupança</option>
        </select>
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Chave PIX</label>
        <input v-model="form.chave_pix" class="w-full rounded border-gray-300" />
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">QR Code PIX (imagem)</label>
      <input type="file" accept="image/*" @change="e => form.qr_code_pix = e.target.files[0]" class="w-full rounded border-gray-300" />
      <div v-if="initial?.qr_code_pix" class="text-xs text-gray-500 mt-1">Arquivo atual cadastrado.</div>
    </div>

    <div class="flex items-center gap-6">
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" v-model="form.ativa" />
        <span>Ativa</span>
      </label>
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" v-model="form.padrao" />
        <span>Definir como padrão</span>
      </label>
    </div>

    <slot :form="form" />
  </div>
</template>
