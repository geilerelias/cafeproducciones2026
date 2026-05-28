<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { AlertTriangle, Bot, CheckCircle2, Mail, MapPin, Phone, Send, X } from 'lucide-vue-next';
import { ref } from 'vue';
import { brand } from '@/data/site';
import MarketingLayout from '@/layouts/MarketingLayout.vue';

type AlertState = {
    open: boolean;
    type: 'success' | 'error';
    title: string;
    message: string;
};

const assistantMessage = ref('');
const assistantLoading = ref(false);
const alertState = ref<AlertState>({
    open: false,
    type: 'success',
    title: '',
    message: '',
});
const contactForm = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
    website: '',
});
const assistantConversation = ref([
    {
        role: 'assistant',
        body: 'Hola. Puedo responder rapido sobre servicios, cotizaciones, disponibilidad, ubicacion y trabajo con CAFE Producciones.',
    },
]);

const askAssistant = async () => {
    if (!assistantMessage.value.trim()) return;

    const message = assistantMessage.value.trim();
    assistantConversation.value.push({ role: 'user', body: message });
    assistantMessage.value = '';
    assistantLoading.value = true;

    const token = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
    const response = await fetch(route('contact-assistant.store'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            Accept: 'application/json',
        },
        body: JSON.stringify({ message }),
    });
    const data = await response.json();

    assistantConversation.value.push({ role: 'assistant', body: data.answer });
    assistantLoading.value = false;
};

const showAlert = (type: AlertState['type'], title: string, message: string) => {
    alertState.value = {
        open: true,
        type,
        title,
        message,
    };
};

const closeAlert = () => {
    alertState.value.open = false;
};

const submitContact = () => {
    contactForm.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => {
            contactForm.reset();
            showAlert('success', 'Mensaje enviado', 'Gracias por contactarnos. Te responderemos pronto desde CAFE Producciones.');
        },
        onError: () => {
            showAlert('error', 'No se pudo enviar', contactForm.errors.contact || 'Revisa los campos marcados e intenta nuevamente.');
        },
    });
};
</script>

<template>
    <MarketingLayout title="Contacto" description="Contacta a CAFE Producciones en Riohacha para cotizar servicios de logistica, sonido, montaje y produccion de eventos.">
        <section class="bg-zinc-950 py-20 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p class="text-sm font-black uppercase tracking-[0.2em] text-[#f0c8be]">Contacto</p>
                <h1 class="mt-4 max-w-4xl text-4xl font-black sm:text-5xl">Mantente en contacto con nosotros</h1>
                <p class="mt-5 max-w-3xl text-lg leading-8 text-zinc-300">
                    Estamos en Riohacha, La Guajira, listos para ayudarte a planificar y producir tu proximo evento.
                </p>
            </div>
        </section>

        <section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div>
                <h2 class="text-3xl font-black">CAFE Producciones: tu socio en logistica y eventos</h2>
                <p class="mt-5 leading-8 text-zinc-600">
                    Si necesitas informacion detallada sobre nuestros servicios, contactanos. Podemos ayudarte a definir alcance, necesidades tecnicas, personal, montaje, tiempos y presupuesto.
                </p>

                <div class="mt-8 space-y-5">
                    <div class="flex gap-4">
                        <MapPin class="mt-1 h-6 w-6 shrink-0 text-[#a8322b]" />
                        <p class="leading-7 text-zinc-700">{{ brand.address }}</p>
                    </div>
                    <div class="flex gap-4">
                        <Phone class="mt-1 h-6 w-6 shrink-0 text-[#a8322b]" />
                        <div>
                            <a class="block font-bold text-zinc-900" :href="`tel:${brand.phone}`">{{ brand.phone }}</a>
                            <a class="block font-bold text-zinc-900" :href="`tel:${brand.secondaryPhone}`">{{ brand.secondaryPhone }}</a>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <Mail class="mt-1 h-6 w-6 shrink-0 text-[#a8322b]" />
                        <a class="font-bold text-zinc-900" :href="`mailto:${brand.email}`">{{ brand.email }}</a>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a :href="brand.whatsapp" target="_blank" rel="noopener" class="inline-flex justify-center rounded-md bg-[#a8322b] px-5 py-3 text-sm font-black text-white">WhatsApp</a>
                    <a :href="`mailto:${brand.email}`" class="inline-flex justify-center rounded-md border border-zinc-300 px-5 py-3 text-sm font-black text-zinc-950">Enviar correo</a>
                </div>
            </div>

            <div class="grid gap-6">
                <form class="rounded-md border border-zinc-200 bg-white p-6 shadow-sm" @submit.prevent="submitContact">
                    <h2 class="text-2xl font-black">Envianos tu mensaje</h2>
                    <div class="mt-6 grid gap-4">
                        <label class="hidden">
                            Sitio web
                            <input v-model="contactForm.website" tabindex="-1" autocomplete="off" />
                        </label>
                        <label class="grid gap-2 text-sm font-bold">
                            Nombre
                            <input v-model="contactForm.name" class="rounded-md border border-zinc-300 px-3 py-3 font-normal outline-none focus:border-[#a8322b]" required />
                            <span v-if="contactForm.errors.name" class="text-xs font-bold text-red-700">{{ contactForm.errors.name }}</span>
                        </label>
                        <label class="grid gap-2 text-sm font-bold">
                            Email
                            <input v-model="contactForm.email" type="email" class="rounded-md border border-zinc-300 px-3 py-3 font-normal outline-none focus:border-[#a8322b]" required />
                            <span v-if="contactForm.errors.email" class="text-xs font-bold text-red-700">{{ contactForm.errors.email }}</span>
                        </label>
                        <label class="grid gap-2 text-sm font-bold">
                            Telefono
                            <input v-model="contactForm.phone" class="rounded-md border border-zinc-300 px-3 py-3 font-normal outline-none focus:border-[#a8322b]" />
                            <span v-if="contactForm.errors.phone" class="text-xs font-bold text-red-700">{{ contactForm.errors.phone }}</span>
                        </label>
                        <label class="grid gap-2 text-sm font-bold">
                            Tema
                            <input v-model="contactForm.subject" class="rounded-md border border-zinc-300 px-3 py-3 font-normal outline-none focus:border-[#a8322b]" />
                            <span v-if="contactForm.errors.subject" class="text-xs font-bold text-red-700">{{ contactForm.errors.subject }}</span>
                        </label>
                        <label class="grid gap-2 text-sm font-bold">
                            Descripcion del mensaje
                            <textarea v-model="contactForm.message" rows="6" class="rounded-md border border-zinc-300 px-3 py-3 font-normal outline-none focus:border-[#a8322b]" required></textarea>
                            <span v-if="contactForm.errors.message" class="text-xs font-bold text-red-700">{{ contactForm.errors.message }}</span>
                        </label>
                        <button type="submit" class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white disabled:cursor-not-allowed disabled:opacity-60" :disabled="contactForm.processing">
                            {{ contactForm.processing ? 'Enviando...' : 'Enviar mensaje' }}
                        </button>
                    </div>
                </form>

                <div class="rounded-md border border-zinc-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <Bot class="h-6 w-6 text-[#a8322b]" />
                        <h2 class="text-2xl font-black">Asistente rapido</h2>
                    </div>
                    <div class="mt-5 grid max-h-80 gap-3 overflow-y-auto rounded-md bg-zinc-50 p-3">
                        <div v-for="(message, index) in assistantConversation" :key="index" class="max-w-[90%] rounded-md px-4 py-3 text-sm leading-6" :class="message.role === 'user' ? 'ml-auto bg-zinc-950 text-white' : 'bg-white text-zinc-700 shadow-sm'">
                            {{ message.body }}
                        </div>
                        <div v-if="assistantLoading" class="rounded-md bg-white px-4 py-3 text-sm font-semibold text-zinc-500 shadow-sm">Respondiendo...</div>
                    </div>
                    <form class="mt-4 flex gap-2" @submit.prevent="askAssistant">
                        <input v-model="assistantMessage" class="min-w-0 flex-1 rounded-md border border-zinc-300 px-3 py-3 outline-none focus:border-[#a8322b]" placeholder="Pregunta sobre servicios o cotizaciones" />
                        <button class="inline-flex items-center justify-center rounded-md bg-[#a8322b] px-4 text-white" :disabled="assistantLoading">
                            <Send class="h-5 w-5" />
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-md border border-zinc-200">
                <iframe
                    title="Ubicacion de CAFE Producciones"
                    :src="brand.mapEmbed"
                    width="100%"
                    height="520"
                    style="border: 0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </section>

        <Teleport to="body">
            <div v-if="alertState.open" class="fixed inset-0 z-[100] grid place-items-center bg-zinc-950/60 px-4 backdrop-blur-sm" role="alertdialog" aria-modal="true">
                <div class="w-full max-w-sm rounded-md bg-white p-6 text-center shadow-[0_24px_80px_rgba(15,23,42,0.35)]">
                    <button type="button" class="ml-auto grid h-9 w-9 place-items-center rounded-full text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-950" aria-label="Cerrar alerta" @click="closeAlert">
                        <X class="h-5 w-5" />
                    </button>
                    <div class="mx-auto mt-1 grid h-20 w-20 place-items-center rounded-full" :class="alertState.type === 'success' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'">
                        <CheckCircle2 v-if="alertState.type === 'success'" class="h-11 w-11" />
                        <AlertTriangle v-else class="h-11 w-11" />
                    </div>
                    <h2 class="mt-5 text-2xl font-black text-zinc-950">{{ alertState.title }}</h2>
                    <p class="mt-3 text-sm leading-6 text-zinc-600">{{ alertState.message }}</p>
                    <button type="button" class="mt-6 w-full rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white transition hover:bg-zinc-800" @click="closeAlert">
                        Aceptar
                    </button>
                </div>
            </div>
        </Teleport>
    </MarketingLayout>
</template>
