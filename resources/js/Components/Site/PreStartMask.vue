<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Lock, Clock } from 'lucide-vue-next';

const props = defineProps({
  startDate: { type: String, required: true },
  serverNow: { type: String, default: null },
  tickMs: { type: Number, default: 1000 },
  icon: { type: String, default: 'lock' }
});
const emit = defineEmits(['released']);

const skew = ref(0);
const nowTs = ref(Date.now());
let timer = null;

const startTs = computed(() => new Date(props.startDate).getTime());
const remaining = computed(() => {
  const diff = startTs.value - (nowTs.value - skew.value);
  return Math.max(diff, 0);
});
const parts = computed(() => {
  const t = Math.floor(remaining.value / 1000);
  const days = Math.floor(t / 86400);
  const hours = Math.floor((t % 86400) / 3600);
  const minutes = Math.floor((t % 3600) / 60);
  const seconds = t % 60;
  return { days, hours, minutes, seconds };
});

const active = ref(true);

const tick = () => {
  nowTs.value = Date.now();
  if (remaining.value <= 0) {
    active.value = false;
    clearInterval(timer);
    emit('released');
  }
};

onMounted(() => {
  if (props.serverNow) {
    skew.value = Date.now() - new Date(props.serverNow).getTime();
  }
  timer = setInterval(tick, props.tickMs);
  tick();
});
onUnmounted(() => { timer && clearInterval(timer); });
</script>

<template>
  <div v-if="active" class="absolute inset-0 z-30 flex items-center justify-center pointer-events-auto">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-[2px]"></div>
    <div class="relative z-10 max-w-xs mx-auto text-center bg-white/80 backdrop-blur-md border border-white/60 rounded-2xl shadow-2xl p-5">
      <div class="mx-auto w-12 h-12 rounded-full bg-gray-900/80 text-white flex items-center justify-center shadow mb-3">
        <Lock v-if="icon==='lock'" class="w-6 h-6" />
        <Clock v-else class="w-6 h-6" />
      </div>
      <p class="text-xs font-bold uppercase tracking-wider text-gray-700">Lote abre em</p>
      <div class="mt-2 flex items-center justify-center gap-1">
        <div class="flex flex-col items-center bg-gray-900/5 rounded p-1 min-w-[2.5rem]">
          <span class="font-mono text-lg font-bold text-gray-900 leading-none">{{ String(parts.days).padStart(2,'0') }}</span>
          <span class="text-[10px] text-gray-500 uppercase font-medium">Dias</span>
        </div>
        <span class="text-gray-400 font-bold">:</span>
        <div class="flex flex-col items-center bg-gray-900/5 rounded p-1 min-w-[2.5rem]">
          <span class="font-mono text-lg font-bold text-gray-900 leading-none">{{ String(parts.hours).padStart(2,'0') }}</span>
          <span class="text-[10px] text-gray-500 uppercase font-medium">Hrs</span>
        </div>
        <span class="text-gray-400 font-bold">:</span>
        <div class="flex flex-col items-center bg-gray-900/5 rounded p-1 min-w-[2.5rem]">
          <span class="font-mono text-lg font-bold text-gray-900 leading-none">{{ String(parts.minutes).padStart(2,'0') }}</span>
          <span class="text-[10px] text-gray-500 uppercase font-medium">Min</span>
        </div>
        <span class="text-gray-400 font-bold">:</span>
        <div class="flex flex-col items-center bg-gray-900/5 rounded p-1 min-w-[2.5rem]">
          <span class="font-mono text-lg font-bold text-red-600 leading-none animate-pulse">{{ String(parts.seconds).padStart(2,'0') }}</span>
          <span class="text-[10px] text-gray-500 uppercase font-medium">Seg</span>
        </div>
      </div>
      <p class="text-xs text-gray-600 mt-3">Aguarde o início do leilão</p>
    </div>
  </div>
  <!-- This overlay intentionally captures pointer events; parent should be relative -->
</template>

<style scoped>
.animate-pulse {
  animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: .5; }
}
</style>
