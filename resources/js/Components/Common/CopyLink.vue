<script setup>
import { ref } from 'vue';
import { Share2, Check } from 'lucide-vue-next';

const props = defineProps({
  url: { type: String, required: true },
  size: { type: Number, default: 16 },
  label: { type: String, default: 'Copiar link' },
  showLabel: { type: Boolean, default: true },
  variant: { type: String, default: 'neutral' },
  fullWidth: { type: Boolean, default: false },
  btnSize: { type: String, default: 'sm' },
});

const copied = ref(false);
let timer = null;

const copyToClipboard = async () => {
  const text = props.url;
  try {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      await navigator.clipboard.writeText(text);
    } else {
      const input = document.createElement('input');
      input.type = 'text';
      input.value = text;
      input.style.position = 'fixed';
      input.style.top = '-1000px';
      document.body.appendChild(input);
      input.select();
      document.execCommand('copy');
      document.body.removeChild(input);
    }
    copied.value = true;
    clearTimeout(timer);
    timer = setTimeout(() => (copied.value = false), 1600);
  } catch (e) {
    // fallback visual apenas
    copied.value = false;
  }
};
const classes = () => {
  const sizeMap = {
    sm: 'px-2.5 py-1.5 text-xs',
    md: 'px-3 py-2 text-sm',
    lg: 'px-4 py-2.5 text-sm',
  };
  const base = `inline-flex items-center gap-1.5 rounded-md font-medium shadow-sm transition-colors border ${sizeMap[props.btnSize] || sizeMap.sm}`;
  const map = {
    primary: 'px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600',
    neutral: 'px-3 py-2 bg-white hover:bg-gray-50 text-gray-700 border-gray-300',
    danger: 'px-3 py-2 bg-red-600 hover:bg-red-700 text-white border-red-600',
    icon: 'p-1.5 bg-white/90 hover:bg-white text-gray-400 hover:text-blue-600 border border-transparent',
  };
  return `${base} ${props.showLabel ? (map[props.variant] || map.neutral) : map.icon} ${props.fullWidth ? 'w-full justify-center' : ''}`;
};
</script>

<template>
  <button
    type="button"
    @click.stop.prevent="copyToClipboard"
    :title="label"
    :class="classes()"
    aria-label="Copiar link do lote"
  >
    <component :is="copied ? Check : Share2" :class="showLabel ? 'w-4 h-4' : `w-[${size}px] h-[${size}px]`" />
    <span v-if="showLabel">{{ copied ? 'Copiado' : label }}</span>
  </button>
</template>
