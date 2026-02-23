<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { X, CheckCircle, Plus, Gavel } from 'lucide-vue-next';
import Swal from 'sweetalert2';

const props = defineProps({
    show: Boolean,
    currentBid: {
        type: [Number, String],
        required: true
    },
    increment: {
        type: [Number, String],
        required: true
    },
    loteNumber: [String, Number]
});

const emit = defineEmits(['close', 'submit']);

const customBidStr = ref('');
const inputRef = ref(null);

// Computeds para garantir números
const numericCurrentBid = computed(() => {
    const val = parseFloat(props.currentBid);
    return isNaN(val) ? 0 : val;
});

const numericIncrement = computed(() => {
    const val = parseFloat(props.increment);
    return isNaN(val) ? 0 : val;
});

// Formatar moeda BRL
const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

// Opções rápidas de incremento (1x, 2x, 3x, 4x o incremento)
const quickOptions = computed(() => {
    return [1, 2, 3, 4].map(multiplier => {
        const incrementValue = numericIncrement.value * multiplier;
        const totalValue = numericCurrentBid.value + incrementValue;
        return {
            multiplier,
            incrementValue,
            totalValue,
            label: `+ ${formatCurrency(incrementValue)}`
        };
    });
});

const minNextBid = computed(() => numericCurrentBid.value + numericIncrement.value);

// Resetar input ao abrir
watch(() => props.show, (val) => {
    if (val) {
        customBidStr.value = '';
        nextTick(() => {
            if (inputRef.value) inputRef.value.focus();
        });
    }
});

// Máscara de entrada monetária
const handleInput = (event) => {
    let value = event.target.value.replace(/\D/g, '');
    
    if (value === '') {
        customBidStr.value = '';
        return;
    }

    const numberValue = parseFloat(value) / 100;
    customBidStr.value = numberValue.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

const getCustomBidValue = () => {
    if (!customBidStr.value) return 0;
    return parseFloat(customBidStr.value.replace(/\./g, '').replace(',', '.'));
};

const validateAndSubmit = async (amount) => {
    if (amount < minNextBid.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Lance Inválido',
            text: `O lance mínimo deve ser de ${formatCurrency(minNextBid.value)}`,
            confirmButtonColor: '#00a550'
        });
        return;
    }

    const result = await Swal.fire({
        title: 'Confirmar Lance',
        html: `
            <div class="text-center">
                <p class="text-gray-600 mb-2">Você está prestes a dar um lance de:</p>
                <h2 class="text-3xl font-black text-[#00a550] mb-4">${formatCurrency(amount)}</h2>
                <p class="text-sm text-gray-500">Ao confirmar, seu lance será registrado imediatamente.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#00a550',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, Confirmar Lance!',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        focusConfirm: true
    });

    if (result.isConfirmed) {
        emit('submit', amount);
        emit('close');
    }
};

const submitCustomBid = () => {
    const amount = getCustomBidValue();
    if (!amount) {
        Swal.fire({
            icon: 'warning',
            title: 'Valor Inválido',
            text: 'Por favor, digite um valor para o lance.',
            confirmButtonColor: '#00a550'
        });
        return;
    }
    validateAndSubmit(amount);
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="emit('close')"></div>

        <!-- Modal Panel -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
            
            <!-- Header -->
            <div class="bg-[#002f6c] text-white p-6 flex justify-between items-start relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-blue-200 text-xs font-bold uppercase tracking-wider mb-1">Dar Lance</p>
                    <h3 class="text-2xl font-black flex items-center gap-2">
                        <Gavel class="w-6 h-6" />
                        Lote {{ loteNumber }}
                    </h3>
                </div>
                <button @click="emit('close')" class="relative z-10 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-full p-2 transition-colors">
                    <X class="w-5 h-5" />
                </button>
                
                <!-- Decorative Circles -->
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-20 h-20 bg-white/5 rounded-full blur-xl"></div>
            </div>

            <!-- Body -->
            <div class="p-6 overflow-y-auto custom-scrollbar">
                
                <!-- Current Status -->
                <div class="flex justify-between items-end mb-8 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Lance Atual</p>
                        <p class="text-3xl font-black text-gray-900 leading-none">{{ formatCurrency(numericCurrentBid) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Mínimo para superar</p>
                        <p class="text-lg font-bold text-[#00a550]">{{ formatCurrency(minNextBid) }}</p>
                    </div>
                </div>

                <!-- Quick Bids -->
                <div class="mb-8">
                    <p class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <Plus class="w-4 h-4 text-[#00a550]" />
                        Lances Rápidos (Sugeridos)
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <button 
                            v-for="opt in quickOptions" 
                            :key="opt.multiplier"
                            @click="validateAndSubmit(opt.totalValue)"
                            class="flex flex-col items-center justify-center p-3 rounded-xl border-2 border-gray-100 hover:border-[#00a550] hover:bg-green-50 transition-all group active:scale-[0.98]"
                        >
                            <span class="text-lg font-black text-gray-800 group-hover:text-[#00a550]">{{ formatCurrency(opt.totalValue) }}</span>
                            <span class="text-[10px] font-bold uppercase text-gray-400 group-hover:text-green-600 bg-gray-100 group-hover:bg-green-100 px-2 py-0.5 rounded-full mt-1">
                                {{ opt.label }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Custom Bid -->
                <div>
                    <p class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <CheckCircle class="w-4 h-4 text-blue-600" />
                        Outro Valor
                    </p>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">R$</span>
                        <input 
                            ref="inputRef"
                            type="text" 
                            v-model="customBidStr"
                            @input="handleInput"
                            placeholder="0,00"
                            class="w-full pl-12 pr-4 py-4 text-xl font-bold text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-0 transition-colors placeholder:text-gray-300"
                            @keydown.enter="submitCustomBid"
                        />
                    </div>
                    <button 
                        @click="submitCustomBid"
                        class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-500/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2 uppercase tracking-wide"
                    >
                        Confirmar Valor Manual
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}
</style>