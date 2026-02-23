<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  label: { type: String, required: true },
  to: { type: [String, Object], default: null },
  variant: { type: String, default: 'neutral' },
  disabled: { type: Boolean, default: false },
  type: { type: String, default: 'button' },
  size: { type: String, default: 'sm' },
});

const emit = defineEmits(['click']);

const classes = () => {
  const sizeMap = {
    sm: 'px-2.5 py-1.5 text-xs rounded-md',
    md: 'px-3 py-2 text-sm rounded-md',
    lg: 'px-4 py-2.5 text-sm rounded-lg',
  };
  const base = `inline-flex items-center gap-1.5 ${sizeMap[props.size] || sizeMap.sm} font-medium shadow-sm transition-colors border`;
  const map = {
    primary: 'bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600',
    neutral: 'bg-white hover:bg-gray-50 text-gray-700 border-gray-300',
    danger: 'bg-red-600 hover:bg-red-700 text-white border-red-600',
  };
  return `${base} ${map[props.variant] || map.neutral}`;
};
</script>

<template>
  <component
    :is="to ? Link : 'button'"
    :href="to || undefined"
    :class="classes()"
    :type="to ? undefined : type"
    :disabled="disabled"
    @click="() => !to && emit('click')"
  >
    <slot />
    <span>{{ label }}</span>
  </component>
</template>
