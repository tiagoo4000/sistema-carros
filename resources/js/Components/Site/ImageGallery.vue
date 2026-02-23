<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { ChevronLeft, ChevronRight, Camera, Maximize2, X } from 'lucide-vue-next';

const props = defineProps({
    mainImage: String,
    images: { type: Array, default: () => [] },
    title: String,
    isVendaDireta: { type: Boolean, default: false },
    status: { type: String, default: '' }
});

const selectedIndex = ref(0);
const allImages = computed(() => {
    const list = [];
    if (props.mainImage) list.push({ id: 'cover', caminho: props.mainImage });
    if (props.images) props.images.forEach(img => list.push(img));
    return list;
});
const currentImage = computed(() => allImages.value[selectedIndex.value]?.caminho || null);

const showFull = ref(false);
const isZoomed = ref(false);
const zoomScale = ref(1);
const originX = ref(50);
const originY = ref(50);
const translateX = ref(0);
const translateY = ref(0);
let touch1 = null;
let touch2 = null;
let startDist = 0;
let startScale = 1;
let startPan = { x: 0, y: 0 };

const badge = computed(() => {
    if (props.status === 'vendido') return { text: 'VENDIDO', cls: 'bg-red-600 text-white' };
    if (props.isVendaDireta) return { text: 'VENDA DIRETA', cls: 'bg-rose-600 text-white' };
    return { text: 'LEILÃO ATIVO', cls: 'bg-[#00a550] text-white' };
});

const selectImage = (index) => {
    selectedIndex.value = index;
};
const nextImage = () => {
    selectedIndex.value = (selectedIndex.value + 1) % allImages.value.length;
};
const prevImage = () => {
    selectedIndex.value = (selectedIndex.value - 1 + allImages.value.length) % allImages.value.length;
};

const onMouseMove = (e) => {
    if (!isZoomed.value) return;
    const rect = e.currentTarget.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    originX.value = Math.max(0, Math.min(100, x));
    originY.value = Math.max(0, Math.min(100, y));
};
const onMouseEnter = () => {
    isZoomed.value = true;
    zoomScale.value = 1.8;
};
const onMouseLeave = () => {
    isZoomed.value = false;
    zoomScale.value = 1;
    translateX.value = 0;
    translateY.value = 0;
};

const onTouchStart = (e) => {
    if (e.touches.length === 2) {
        touch1 = e.touches[0];
        touch2 = e.touches[1];
        startDist = Math.hypot(touch2.clientX - touch1.clientX, touch2.clientY - touch1.clientY);
        startScale = zoomScale.value;
        startPan = { x: translateX.value, y: translateY.value };
    }
};
const onTouchMove = (e) => {
    if (e.touches.length === 2) {
        const a = e.touches[0];
        const b = e.touches[1];
        const dist = Math.hypot(b.clientX - a.clientX, b.clientY - a.clientY);
        const factor = dist / startDist;
        zoomScale.value = Math.max(1, Math.min(2.5, startScale * factor));
        isZoomed.value = zoomScale.value > 1.02;
    } else if (e.touches.length === 1 && isZoomed.value) {
        translateX.value = startPan.x + e.touches[0].movementX * 0.6;
        translateY.value = startPan.y + e.touches[0].movementY * 0.6;
    }
};
const onTouchEnd = () => {
    if (zoomScale.value <= 1.02) {
        isZoomed.value = false;
        zoomScale.value = 1;
        translateX.value = 0;
        translateY.value = 0;
    }
};

const keyHandler = (e) => {
    if (!showFull.value) return;
    if (e.key === 'Escape') showFull.value = false;
    if (e.key === 'ArrowRight') nextImage();
    if (e.key === 'ArrowLeft') prevImage();
};
onMounted(() => document.addEventListener('keydown', keyHandler));
onUnmounted(() => document.removeEventListener('keydown', keyHandler));

watch(() => props.mainImage, (val) => {
    if (val && selectedIndex.value === 0) return;
});
</script>

<template>
    <div class="flex flex-col gap-3">
        <div class="relative rounded-2xl overflow-hidden border border-gray-200 bg-black/5 group h-[300px] md:h-[480px]">
            <transition name="fade-scale" mode="out-in">
                <img
                    v-if="currentImage"
                    :key="currentImage"
                    :src="`/storage/${currentImage}`"
                    :alt="title"
                    class="w-full h-full object-contain md:object-cover select-none"
                    :style="{
                        transformOrigin: originX + '% ' + originY + '%',
                        transform: `scale(${zoomScale}) translate(${translateX}px, ${translateY}px)`,
                        transition: isZoomed ? 'transform 60ms linear' : 'transform 200ms ease'
                    }"
                    @mousemove="onMouseMove"
                    @mouseenter="onMouseEnter"
                    @mouseleave="onMouseLeave"
                    @touchstart.passive="onTouchStart"
                    @touchmove.passive="onTouchMove"
                    @touchend.passive="onTouchEnd"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-gray-400" key="empty">
                    <Camera class="w-16 h-16 opacity-50" />
                </div>
            </transition>

            <div v-if="badge" class="absolute top-3 left-3 z-20">
                <span :class="badge.cls" class="px-3 py-1 rounded-lg font-black text-[10px] uppercase tracking-wider shadow-md"> {{ badge.text }} </span>
            </div>

            <button v-if="allImages.length > 1" @click.stop="prevImage" class="absolute left-3 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/70 text-white p-2.5 rounded-full backdrop-blur-sm transition-all">
                <ChevronLeft class="w-6 h-6" />
            </button>
            <button v-if="allImages.length > 1" @click.stop="nextImage" class="absolute right-3 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/70 text-white p-2.5 rounded-full backdrop-blur-sm transition-all">
                <ChevronRight class="w-6 h-6" />
            </button>

            <div v-if="allImages.length > 1" class="absolute bottom-3 right-3 bg-black/60 text-white text-[11px] font-bold px-3 py-1 rounded-full backdrop-blur-sm">
                {{ selectedIndex + 1 }} / {{ allImages.length }}
            </div>

            <button v-if="currentImage" @click="showFull = true" class="absolute bottom-3 left-3 bg-black/60 text-white p-2 rounded-full hover:bg-black/80">
                <Maximize2 class="w-5 h-5" />
            </button>
        </div>

        <div v-if="allImages.length > 1" class="flex gap-3 overflow-x-auto no-scrollbar py-1 justify-center">
            <button
                v-for="(img, index) in allImages"
                :key="img.id || index"
                @click="selectImage(index)"
                class="relative w-20 h-20 sm:w-22 sm:h-22 flex-shrink-0 rounded-xl overflow-hidden border-2 transition-all shadow-sm"
                :class="selectedIndex === index ? 'border-[#00a550] ring-2 ring-[#00a550]/30' : 'border-transparent hover:border-gray-300 opacity-80 hover:opacity-100'"
            >
                <img :src="`/storage/${img.caminho}`" class="w-full h-full object-cover" />
            </button>
        </div>

        <div v-if="showFull" class="fixed inset-0 z-[60] bg-black/95 flex items-center justify-center">
            <button @click="showFull = false" class="absolute top-5 right-5 bg-white/10 hover:bg-white/20 text-white p-3 rounded-full">
                <X class="w-6 h-6" />
            </button>
            <button v-if="allImages.length > 1" @click.stop="prevImage" class="absolute left-5 top-1/2 -translate-y-1/2 text-white p-3 rounded-full hover:bg-white/10">
                <ChevronLeft class="w-8 h-8" />
            </button>
            <img
                v-if="currentImage"
                :src="`/storage/${currentImage}`"
                :alt="title"
                class="max-w-[92vw] max-h-[82vh] object-contain select-none transition-all duration-300"
            />
            <button v-if="allImages.length > 1" @click.stop="nextImage" class="absolute right-5 top-1/2 -translate-y-1/2 text-white p-3 rounded-full hover:bg-white/10">
                <ChevronRight class="w-8 h-8" />
            </button>
            <div v-if="allImages.length > 1" class="absolute bottom-6 bg-white/10 text-white text-xs font-bold px-4 py-1.5 rounded-full">
                {{ selectedIndex + 1 }} / {{ allImages.length }}
            </div>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.fade-scale-enter-active { transition: opacity .25s ease, transform .25s ease; }
.fade-scale-leave-active { transition: opacity .2s ease, transform .2s ease; }
.fade-scale-enter-from, .fade-scale-leave-to { opacity: 0; transform: scale(0.98); }
</style>
