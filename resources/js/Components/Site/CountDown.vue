<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    endDate: {
        type: String,
        required: true
    },
    status: {
        type: String,
        default: 'aberto'
    },
    serverNow: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['expired']);

const timeLeft = ref({ days: 0, hours: 0, minutes: 0, seconds: 0 });
const interval = ref(null);
const isExpired = ref(false);
const emittedOnce = ref(false);
const driftMs = ref(0);

const stopInterval = () => {
    if (interval.value) {
        clearInterval(interval.value);
        interval.value = null;
    }
};

const startInterval = () => {
    stopInterval();
    updateTimer();
    interval.value = setInterval(updateTimer, 1000);
};

const updateTimer = () => {
    if (props.status === 'vendido' || props.status === 'finalizado') {
        isExpired.value = true;
        timeLeft.value = { days: 0, hours: 0, minutes: 0, seconds: 0 };
        if (!emittedOnce.value) {
            emit('expired');
            emittedOnce.value = true;
        }
        return;
    }

    if (!props.endDate) {
        // Sem data, não há contagem. Mantém ativo sem expirar.
        isExpired.value = false;
        return;
    }

    const clientNow = Date.now();
    const now = clientNow - driftMs.value;
    const end = new Date(props.endDate).getTime();
    const distance = end - now;

    if (distance < 0) {
        isExpired.value = true;
        timeLeft.value = { days: 0, hours: 0, minutes: 0, seconds: 0 };
        if (!emittedOnce.value) {
            emit('expired');
            emittedOnce.value = true;
        }
        return;
    }

    isExpired.value = false;
    timeLeft.value = {
        days: Math.floor(distance / (1000 * 60 * 60 * 24)),
        hours: Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
        minutes: Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
        seconds: Math.floor((distance % (1000 * 60)) / 1000)
    };
};

watch(() => props.status, () => {
    updateTimer();
});

onMounted(() => {
    if (props.serverNow) {
        driftMs.value = Date.now() - new Date(props.serverNow).getTime();
    }
    startInterval();
});

onUnmounted(() => {
    stopInterval();
});

watch(() => props.endDate, () => {
    emittedOnce.value = false;
    if (!props.endDate) {
        stopInterval();
        isExpired.value = false;
        return;
    }
    startInterval();
});

watch(() => props.status, (val) => {
    if (val === 'vendido' || val === 'finalizado') {
        stopInterval();
        isExpired.value = true;
        timeLeft.value = { days: 0, hours: 0, minutes: 0, seconds: 0 };
        if (!emittedOnce.value) {
            emit('expired');
            emittedOnce.value = true;
        }
    }
});

watch(() => props.serverNow, (val) => {
    if (val) {
        driftMs.value = Date.now() - new Date(val).getTime();
    } else {
        driftMs.value = 0;
    }
});
</script>

<template>
    <div class="inline-flex items-center">
        <div v-if="!isExpired" class="flex items-center gap-1 sm:gap-2">
            <div class="flex flex-col items-center bg-gray-100 rounded p-1 min-w-[2.5rem] sm:min-w-[3rem]">
                <span class="font-mono text-lg sm:text-xl font-bold text-[#00a550] leading-none">
                    {{ String(timeLeft.days).padStart(2, '0') }}
                </span>
                <span class="text-[10px] text-gray-500 uppercase font-medium">Dias</span>
            </div>
            <span class="text-gray-300 font-bold">:</span>
            <div class="flex flex-col items-center bg-gray-100 rounded p-1 min-w-[2.5rem] sm:min-w-[3rem]">
                <span class="font-mono text-lg sm:text-xl font-bold text-[#00a550] leading-none">
                    {{ String(timeLeft.hours).padStart(2, '0') }}
                </span>
                <span class="text-[10px] text-gray-500 uppercase font-medium">Hrs</span>
            </div>
            <span class="text-gray-300 font-bold">:</span>
            <div class="flex flex-col items-center bg-gray-100 rounded p-1 min-w-[2.5rem] sm:min-w-[3rem]">
                <span class="font-mono text-lg sm:text-xl font-bold text-[#00a550] leading-none">
                    {{ String(timeLeft.minutes).padStart(2, '0') }}
                </span>
                <span class="text-[10px] text-gray-500 uppercase font-medium">Min</span>
            </div>
            <span class="text-gray-300 font-bold">:</span>
            <div class="flex flex-col items-center bg-gray-100 rounded p-1 min-w-[2.5rem] sm:min-w-[3rem]">
                <span class="font-mono text-lg sm:text-xl font-bold text-red-600 leading-none animate-pulse">
                    {{ String(timeLeft.seconds).padStart(2, '0') }}
                </span>
                <span class="text-[10px] text-gray-500 uppercase font-medium">Seg</span>
            </div>
        </div>
        
        <div v-else class="flex items-center gap-2 px-4 py-2 bg-red-50 rounded-lg border border-red-100">
            <span class="text-red-600 font-bold uppercase tracking-wider text-sm">Encerrado</span>
        </div>
    </div>
</template>
