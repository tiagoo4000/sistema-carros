<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { CheckCircle, XCircle, Camera, Upload, ChevronRight, ChevronLeft, FileText } from 'lucide-vue-next';

const props = defineProps({
  status: Object,
  overallStatus: String,
  approval: Object,
  rejection_reason: String,
});

const page = usePage();
// Steps: 1 Documento com Foto (RG/CNH), 2 Selfie, 3 Comprovante, 4 Revisão/Final
const step = ref(1);
const totalSteps = 4;
const progress = () => Math.round((step.value - 1) / (totalSteps - 1) * 100);

// Step 1 - Documento com foto (RG/CNH)
const MAX_UPLOAD_BYTES = 7 * 1024 * 1024; // ~7MB para evitar limites do servidor
const selectedDocTipo = ref('rg');
const formDocFoto = useForm({ tipo: 'rg', arquivo: null });
const docPreview = ref(null);
const onDocFotoChange = (e) => {
  formDocFoto.tipo = selectedDocTipo.value;
  const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
  formDocFoto.clearErrors();
  if (file && file.size > MAX_UPLOAD_BYTES) {
    formDocFoto.setError('arquivo', 'Arquivo muito grande. Tente uma foto mais leve (até ~7MB).');
    formDocFoto.arquivo = null;
    docPreview.value = null;
    return;
  }
  formDocFoto.arquivo = file;
  if (file) {
    const reader = new FileReader();
    reader.onload = () => { docPreview.value = reader.result; };
    reader.readAsDataURL(file);
  } else {
    docPreview.value = null;
  }
};
const submitDocFoto = () => {
  if (!formDocFoto.arquivo) return;
  formDocFoto.tipo = selectedDocTipo.value;
  formDocFoto.post(route('minha-conta.documentos.store'), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => { step.value = 2; }
  });
};

// Step 2 - Selfie via câmera (UX minimalista)
const videoRef = ref(null);
const canvasRef = ref(null);
const photoPreview = ref(null); // dataURL da captura
const cameraOn = ref(false);
const capturingFlash = ref(false);
const cameraError = ref('');
let mediaStream = null;
const formSelfie = useForm({ tipo: 'selfie', arquivo: null });

const startCamera = async () => {
  cameraError.value = '';
  try {
    mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
    if (videoRef.value) {
      videoRef.value.srcObject = mediaStream;
      await videoRef.value.play();
      cameraOn.value = true;
    }
  } catch (e) {
    cameraError.value = 'Não foi possível acessar a câmera. Verifique permissões ou tente outro dispositivo.';
    cameraOn.value = false;
  }
};

const stopCamera = () => {
  if (mediaStream) {
    mediaStream.getTracks().forEach(t => t.stop());
    mediaStream = null;
  }
  cameraOn.value = false;
};

const resetCapture = () => {
  formSelfie.arquivo = null;
  photoPreview.value = null;
  if (!cameraOn.value) startCamera();
};

const captureSelfie = () => {
  const video = videoRef.value;
  const canvas = canvasRef.value;
  if (!video || !canvas) return;
  const w = video.videoWidth;
  const h = video.videoHeight;
  canvas.width = w;
  canvas.height = h;
  const ctx = canvas.getContext('2d');
  ctx.drawImage(video, 0, 0, w, h);
  capturingFlash.value = true;
  setTimeout(() => (capturingFlash.value = false), 150);
  canvas.toBlob((blob) => {
    if (!blob) return;
    const file = new File([blob], 'selfie.jpg', { type: 'image/jpeg' });
    formSelfie.arquivo = file;
    // prévia
    const reader = new FileReader();
    reader.onload = () => { photoPreview.value = reader.result; stopCamera(); };
    reader.readAsDataURL(file);
  }, 'image/jpeg', 0.9);
};

const submitSelfie = () => {
  if (!formSelfie.arquivo) return;
  formSelfie.post(route('minha-conta.documentos.store'), {
    forceFormData: true,
    preserveScroll: true,
    onStart: () => {},
    onSuccess: () => {
      stopCamera();
      step.value = 3;
    }
  });
};

onMounted(() => {
  if (step.value === 2) startCamera();
});
onBeforeUnmount(() => { stopCamera(); });

// Step 3 - Comprovante de residência
const formComprovante = useForm({ tipo: 'comprovante_residencia', arquivo: null });
const onComprovanteChange = (e) => {
  const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
  formComprovante.clearErrors();
  if (file && file.size > MAX_UPLOAD_BYTES) {
    formComprovante.setError('arquivo', 'Arquivo muito grande. Tente uma foto/arquivo menor (até ~7MB).');
    formComprovante.arquivo = null;
    return;
  }
  formComprovante.arquivo = file;
};
const submitComprovante = () => {
  formComprovante.post(route('minha-conta.documentos.store'), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => step.value = 4
  });
};

// Navegação
const showSelfieGuide = ref(true);
const selfieGuideImgError = ref(false);
const selfieGuideSrc = '/images/selfie-guide.png';
const nextStep = () => { 
  if (step.value < totalSteps) step.value++; 
  if (step.value === 2 && !showSelfieGuide.value) startCamera(); 
};
const prevStep = () => { 
  if (step.value > 1) step.value--; 
  if (step.value !== 2) { 
    stopCamera(); 
    showSelfieGuide.value = true; 
  } else { 
    if (!showSelfieGuide.value) startCamera(); 
  }
};

// Controle de exibição condicional
const forceShowForm = ref(false);
const canShowForm = () => props.overallStatus === 'none' || forceShowForm.value === true;
</script>

<template>
  <Head title="Verificação de Conta" />
  <SiteLayout>
    <div class="min-h-[70vh] bg-gray-50">
      <div class="max-w-2xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-4 text-center">Verificação de Conta</h1>
        
        <!-- Estado: Aprovado -->
        <div v-if="overallStatus === 'approved'" class="bg-white rounded-2xl shadow border border-emerald-200 p-6 mb-6">
          <div class="flex items-start gap-3">
            <div class="text-emerald-600">
              <CheckCircle class="w-6 h-6" />
            </div>
            <div class="flex-1">
              <div class="font-bold text-gray-900 text-lg">Conta Verificada</div>
              <p class="text-sm text-gray-700 mt-1">Seus documentos foram analisados e aprovados. Você já pode participar dos leilões normalmente.</p>
              <div class="mt-3 text-xs text-gray-500" v-if="approval">
                <div v-if="approval.approved_at">Aprovado em: {{ new Date(approval.approved_at).toLocaleString('pt-BR') }}</div>
                <div v-if="approval.approved_by">Aprovado por: {{ approval.approved_by }}</div>
              </div>
              <div class="mt-4">
                <Link :href="route('minha-conta.meus-documentos')" class="inline-flex items-center px-4 py-2 rounded border border-gray-300 hover:bg-gray-50 text-sm">
                  Visualizar documentos enviados
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Estado: Pendente -->
        <div v-else-if="overallStatus === 'pending'" class="bg-white rounded-2xl shadow border border-amber-200 p-6 mb-6">
          <div class="flex items-start gap-3">
            <div class="text-amber-600">
              <Clock class="w-6 h-6" />
            </div>
            <div class="flex-1">
              <div class="font-bold text-gray-900 text-lg">Documentos em Análise</div>
              <p class="text-sm text-gray-700 mt-1">Seus documentos foram enviados e estão sendo analisados. Você será notificado por e-mail assim que forem avaliados.</p>
              <div class="mt-4">
                <Link :href="route('minha-conta.meus-documentos')" class="inline-flex items-center px-4 py-2 rounded border border-gray-300 hover:bg-gray-50 text-sm">
                  Visualizar documentos enviados
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Estado: Rejeitado -->
        <div v-else-if="overallStatus === 'rejected'" class="bg-white rounded-2xl shadow border border-red-200 p-6 mb-6">
          <div class="flex items-start gap-3">
            <div class="text-red-600">
              <XCircle class="w-6 h-6" />
            </div>
            <div class="flex-1">
              <div class="font-bold text-gray-900 text-lg">Documentação Rejeitada</div>
              <p class="text-sm text-gray-700 mt-1">Revise as orientações e envie novamente seus documentos.</p>
              <div v-if="rejection_reason" class="mt-2 p-3 rounded border border-red-200 bg-red-50 text-red-700 text-sm">
                Motivo: {{ rejection_reason }}
              </div>
              <div class="mt-4">
                <button @click="forceShowForm = true; step = 1" class="inline-flex items-center px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700 text-sm">
                  Enviar Novamente
                </button>
                <Link :href="route('minha-conta.meus-documentos')" class="ml-2 inline-flex items-center px-4 py-2 rounded border border-gray-300 hover:bg-gray-50 text-sm">
                  Visualizar documentos enviados
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Bar (somente quando formulário visível) -->
        <div v-if="canShowForm()" class="mb-6">
          <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full bg-emerald-600 transition-all" :style="{ width: progress() + '%' }"></div>
          </div>
          <div class="mt-2 flex justify-between text-xs text-gray-500">
            <span>Documento</span>
            <span>Selfie</span>
            <span>Comprovante</span>
            <span>Revisão</span>
          </div>
        </div>

        <!-- Stepper -->
        <div v-if="canShowForm()" class="bg-white rounded-2xl shadow border border-gray-200 p-6">
          <!-- Etapa 1 -->
          <div v-if="step === 1" class="space-y-5">
            <h2 class="text-lg font-semibold text-gray-900 text-center">Documento com Foto</h2>
            <p class="text-sm text-gray-600 text-center">Envie foto do seu RG ou CNH.</p>
            <div class="flex justify-center gap-4">
              <label class="flex items-center gap-2">
                <input type="radio" value="rg" v-model="selectedDocTipo" />
                <span>RG</span>
              </label>
              <label class="flex items-center gap-2">
                <input type="radio" value="cnh" v-model="selectedDocTipo" />
                <span>CNH</span>
              </label>
            </div>

            <div class="space-y-4">
              <div v-if="docPreview" class="mx-auto max-w-md rounded-2xl overflow-hidden border border-gray-200 bg-gray-50">
                <img :src="docPreview" alt="Prévia do documento" class="w-full h-64 object-contain bg-white" />
              </div>
              <div v-if="formDocFoto.errors?.arquivo" class="text-sm text-red-600 text-center">
                {{ formDocFoto.errors.arquivo }}
              </div>
              <div v-if="formDocFoto.errors?.status" class="text-sm text-red-600 text-center">
                {{ formDocFoto.errors.status }}
              </div>
              <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <label class="px-6 py-3 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 cursor-pointer">
                  <input type="file" accept="image/*,.pdf" class="hidden" @change="onDocFotoChange" />
                  Selecionar Arquivo
                </label>
              </div>
            </div>

            <div class="flex justify-between pt-2">
              <button disabled class="inline-flex items-center px-4 py-2 rounded bg-gray-200 text-gray-600">
                <ChevronLeft class="w-4 h-4 mr-1" /> Voltar
              </button>
              <button @click="submitDocFoto" :disabled="!formDocFoto.arquivo || formDocFoto.processing" class="inline-flex items-center px-5 py-2.5 rounded bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50">
                Enviar e Continuar <ChevronRight class="w-4 h-4 ml-2" />
              </button>
            </div>
          </div>

          <!-- Etapa 2 -->
          <div v-else-if="step === 2" class="space-y-5">
            <h2 class="text-lg font-semibold text-gray-900 text-center">Selfie com Documento</h2>
            <template v-if="showSelfieGuide">
              <div class="mx-auto max-w-md bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
                <div class="flex justify-center">
                  <img v-if="!selfieGuideImgError" :src="selfieGuideSrc" alt="Exemplo de selfie com documento" class="w-full max-w-sm rounded-xl object-contain" @error="selfieGuideImgError = true" />
                  <svg v-else width="220" height="160" viewBox="0 0 220 160" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-gray-400">
                    <rect x="10" y="10" width="200" height="140" rx="16" fill="#F5F7FA" stroke="#E5E7EB"/>
                    <circle cx="110" cy="70" r="26" fill="#E5F2EC" stroke="#A7F3D0"/>
                    <rect x="130" y="90" width="60" height="40" rx="6" fill="#E6EEFF" stroke="#BFDBFE"/>
                    <rect x="136" y="96" width="48" height="8" rx="3" fill="#BFDBFE"/>
                    <rect x="136" y="108" width="40" height="6" rx="3" fill="#DBEAFE"/>
                    <path d="M100 92c6 4 14 4 20 0" stroke="#34D399" stroke-width="2" stroke-linecap="round"/>
                    <path d="M86 66c2-10 12-18 24-18s22 8 24 18" stroke="#A7F3D0" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </div>
                <ul class="text-sm text-gray-700 space-y-2">
                  <li>• Segure o documento próximo ao rosto.</li>
                  <li>• Enquadre rosto e documento com boa iluminação.</li>
                  <li>• Evite acessórios que ocultem o rosto.</li>
                  <li>• Mantenha a câmera estável ao capturar.</li>
                </ul>
                <div class="text-center pt-2">
                  <button @click="showSelfieGuide = false; startCamera();" class="px-6 py-3 rounded-full bg-blue-600 text-white hover:bg-blue-700">
                    Começar
                  </button>
                </div>
              </div>
            </template>

            <template v-else>
              <p class="text-sm text-gray-600 text-center">Enquadre seu rosto e documento. Capture quando estiver pronto.</p>
              <div class="relative mx-auto max-w-md rounded-2xl overflow-hidden border border-gray-200 bg-black/5">
                <video v-show="!photoPreview" ref="videoRef" class="w-full h-80 object-cover bg-black"></video>
                <img v-if="photoPreview" :src="photoPreview" alt="Prévia" class="w-full h-80 object-cover" />
                <canvas ref="canvasRef" class="hidden"></canvas>
                <div v-show="capturingFlash" class="absolute inset-0 bg-white/80 pointer-events-none animate-pulse"></div>
              </div>
              <p v-if="cameraError" class="text-center text-sm text-red-600">{{ cameraError }}</p>
  
              <div class="flex flex-col items-center gap-3">
                <button v-if="!cameraOn && !photoPreview" @click="startCamera"
                  class="inline-flex items-center px-6 py-3 rounded-full bg-blue-600 text-white hover:bg-blue-700">
                  <Camera class="w-5 h-5 mr-2" /> Ativar Câmera
                </button>
                <button v-if="cameraOn && !photoPreview" @click="captureSelfie"
                  class="inline-flex items-center px-8 py-4 rounded-full bg-blue-600 text-white hover:bg-blue-700 text-lg font-semibold">
                  Capturar
                </button>
                <div v-if="photoPreview" class="flex gap-3">
                  <button @click="resetCapture" class="px-5 py-2.5 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300">
                    Refazer
                  </button>
                  <button @click="submitSelfie" :disabled="formSelfie.processing || !formSelfie.arquivo"
                    class="px-6 py-2.5 rounded-full bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50">
                    Enviar e Continuar
                  </button>
                </div>
              </div>
            </template>
          </div>

          <!-- Etapa 3 -->
          <div v-else-if="step === 3" class="space-y-5">
            <h2 class="text-lg font-semibold text-gray-900">Etapa 3 — Comprovante de Residência</h2>
            <p class="text-sm text-gray-600">Envie um comprovante de residência recente.</p>
            <input type="file" accept="image/*,.pdf" @change="onComprovanteChange" class="block w-full rounded border border-gray-300" />
            <div v-if="formComprovante.errors?.arquivo" class="text-sm text-red-600 mt-1">
              {{ formComprovante.errors.arquivo }}
            </div>
            <div class="flex justify-between pt-2">
              <button @click="prevStep" class="inline-flex items-center px-4 py-2 rounded bg-gray-200 text-gray-700">
                <ChevronLeft class="w-4 h-4 mr-1" /> Voltar
              </button>
              <button @click="submitComprovante" :disabled="formComprovante.processing || !formComprovante.arquivo" class="inline-flex items-center px-5 py-2.5 rounded bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50">
                Enviar e Continuar <ChevronRight class="w-4 h-4 ml-2" />
              </button>
            </div>
          </div>

          <!-- Etapa 4 -->
          <div v-else class="space-y-5">
            <h2 class="text-lg font-semibold text-gray-900">Etapa 4 — Revisão</h2>
            <p class="text-sm text-gray-600">Confira o status de cada documento. Se algo foi rejeitado, o motivo estará visível.</p>
            <div class="space-y-3">
              <div class="flex items-center justify-between p-3 rounded border" :class="(status.rg?.validado || status.cnh?.validado) ? 'border-emerald-200 bg-emerald-50' : (status.rg?.observacoes || status.cnh?.observacoes) ? 'border-red-200 bg-red-50' : 'border-amber-200 bg-amber-50'">
                <div class="text-sm">
                  <div class="font-medium">Documento com Foto (RG/CNH)</div>
                  <div v-if="status.rg?.observacoes || status.cnh?.observacoes" class="text-red-700">Motivo: {{ status.rg?.observacoes || status.cnh?.observacoes }}</div>
                </div>
                <div>
                  <span v-if="status.rg?.validado || status.cnh?.validado" class="text-emerald-700 text-sm inline-flex items-center"><CheckCircle class="w-4 h-4 mr-1" /> Aprovado</span>
                  <span v-else-if="status.rg || status.cnh" class="text-amber-700 text-sm inline-flex items-center"><XCircle class="w-4 h-4 mr-1" /> Pendente</span>
                  <span v-else class="text-gray-500 text-sm">Não enviado</span>
                </div>
              </div>
              <div class="flex items-center justify-between p-3 rounded border" :class="status.selfie?.validado ? 'border-emerald-200 bg-emerald-50' : status.selfie?.observacoes ? 'border-red-200 bg-red-50' : 'border-amber-200 bg-amber-50'">
                <div class="text-sm">
                  <div class="font-medium">Selfie com Documento</div>
                  <div v-if="status.selfie?.observacoes" class="text-red-700">Motivo: {{ status.selfie.observacoes }}</div>
                </div>
                <div>
                  <span v-if="status.selfie?.validado" class="text-emerald-700 text-sm inline-flex items-center"><CheckCircle class="w-4 h-4 mr-1" /> Aprovado</span>
                  <span v-else-if="status.selfie" class="text-amber-700 text-sm inline-flex items-center"><XCircle class="w-4 h-4 mr-1" /> Pendente</span>
                  <span v-else class="text-gray-500 text-sm">Não enviado</span>
                </div>
              </div>
              <div class="flex items-center justify-between p-3 rounded border" :class="status.comprovante_residencia?.validado ? 'border-emerald-200 bg-emerald-50' : status.comprovante_residencia?.observacoes ? 'border-red-200 bg-red-50' : 'border-amber-200 bg-amber-50'">
                <div class="text-sm">
                  <div class="font-medium">Comprovante de Residência</div>
                  <div v-if="status.comprovante_residencia?.observacoes" class="text-red-700">Motivo: {{ status.comprovante_residencia.observacoes }}</div>
                </div>
                <div>
                  <span v-if="status.comprovante_residencia?.validado" class="text-emerald-700 text-sm inline-flex items-center"><CheckCircle class="w-4 h-4 mr-1" /> Aprovado</span>
                  <span v-else-if="status.comprovante_residencia" class="text-amber-700 text-sm inline-flex items-center"><XCircle class="w-4 h-4 mr-1" /> Pendente</span>
                  <span v-else class="text-gray-500 text-sm">Não enviado</span>
                </div>
              </div>
            </div>
            <div class="flex justify-between pt-2">
              <button @click="prevStep" class="inline-flex items-center px-4 py-2 rounded bg-gray-200 text-gray-700">
                <ChevronLeft class="w-4 h-4 mr-1" /> Voltar
              </button>
              <Link :href="route('leiloes.index')" class="inline-flex items-center px-5 py-2.5 rounded bg-emerald-600 text-white hover:bg-emerald-700">
                Concluir
              </Link>
            </div>
            <div class="p-3 rounded border border-amber-200 bg-amber-50 text-amber-800 text-sm">
              Após aprovação de todos os documentos, você poderá participar dos leilões.
            </div>
          </div>
        </div>
      </div>
    </div>
  </SiteLayout>
</template>
