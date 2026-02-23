<script setup>
defineProps({
  stackMobile: { type: Boolean, default: true },
});
</script>

<template>
  <div class="flex w-full gap-2" :class="stackMobile ? 'flex-col sm:flex-row sm:items-center sm:justify-between' : 'items-center justify-between'">
    <div class="flex flex-wrap items-center gap-2">
      <slot name="main" />
      <slot name="neutral" />
    </div>
    <div class="flex flex-wrap items-center gap-2 sm:border-l sm:pl-3">
      <slot name="danger" />
    </div>
  </div>
</template>

