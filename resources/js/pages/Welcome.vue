<script setup lang="ts">
import { assets, brand, services, socialProfiles, team } from '@/data/site';
import MarketingLayout from '@/layouts/MarketingLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, BarChart3, CalendarDays, Headphones, ShieldCheck, Sparkles, Users } from 'lucide-vue-next';

const featuredServices = services.slice(0, 6);

type Notice = {
    id: number;
    title: string;
    slug: string;
    category: string;
    platform: string | null;
    image_url: string | null;
    excerpt: string;
    published_at: string | null;
};

defineProps<{ notices: Notice[] }>();

const processSteps = [
    {
        title: 'Planificacion',
        description: 'Definimos alcance, presupuesto y tiempos con un flujo simple.',
    },
    {
        title: 'Produccion',
        description: 'Montamos sonido, video, luces y personal tecnico sin improvisacion.',
    },
    {
        title: 'Medicion',
        description: 'Registramos encuestas y observaciones para mejorar cada entrega.',
    },
];

const portalHighlights = [
    {
        title: 'Agenda ordenada',
        description: 'Citas, disponibilidad y seguimiento en un solo lugar.',
        icon: CalendarDays,
    },
    {
        title: 'Encuestas claras',
        description: 'Medicion simple para revisar calidad y satisfaccion.',
        icon: BarChart3,
    },
    {
        title: 'Seguimiento real',
        description: 'Mensajes y pendientes sin ruido visual.',
        icon: ShieldCheck,
    },
];
</script>

<template>
    <MarketingLayout title="Produccion de eventos en Riohacha" :description="brand.description">
        <section class="relative isolate overflow-hidden bg-zinc-950 text-white">
            <img
                :src="assets.heroImage"
                alt="Produccion de eventos CAFE Producciones"
                class="absolute inset-0 h-full w-full object-cover opacity-45"
            />
            <div class="absolute inset-0 bg-gradient-to-br from-zinc-950 via-zinc-950/80 to-[#7f241f]/25"></div>

            <div
                class="relative mx-auto grid min-h-[90vh] max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-[1.02fr_0.98fr] lg:px-8 lg:py-24"
            >
                <div class="max-w-2xl">
                    <img :src="assets.logoImage" alt="CAFE Producciones" class="mb-6 h-20 w-20 object-contain" />
                    <p class="mb-5 text-xs font-black uppercase tracking-[0.28em] text-[#f0c8be]">Eventos sociales, corporativos e institucionales</p>
                    <h1 class="animate-fade-up text-5xl font-light leading-none tracking-tight sm:text-6xl lg:text-7xl">
                        Haz crecer
                        <span class="block font-black">tu evento</span>
                    </h1>
                    <p class="mt-6 max-w-xl text-base leading-8 text-zinc-100 sm:text-lg">
                        Produccion, logistica, tecnologia y acompanamiento para experiencias memorables en Riohacha, La Guajira y Colombia.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <Link
                            :href="route('our-services')"
                            class="shine-hover inline-flex items-center justify-center gap-2 rounded-md bg-[#a8322b] px-5 py-3 text-sm font-black text-white transition hover:bg-[#b44136]"
                        >
                            Ver servicios
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                        <a
                            :href="brand.whatsapp"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center justify-center rounded-md border border-white/35 px-5 py-3 text-sm font-bold text-white transition hover:bg-white hover:text-zinc-950"
                        >
                            Cotizar por WhatsApp
                        </a>
                    </div>

                    <div class="mt-10 flex flex-wrap gap-3">
                        <span
                            class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-zinc-200"
                            >Agenda</span
                        >
                        <span
                            class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-zinc-200"
                            >Encuestas</span
                        >
                        <span
                            class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-zinc-200"
                            >Dashboard</span
                        >
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="grid gap-3 sm:grid-cols-3">
                        <article
                            v-for="item in portalHighlights"
                            :key="item.title"
                            class="rounded-md border border-white/15 bg-white/10 p-4 backdrop-blur"
                        >
                            <component :is="item.icon" class="h-6 w-6 text-[#f0c8be]" />
                            <h3 class="mt-3 text-sm font-black">{{ item.title }}</h3>
                            <p class="mt-2 text-xs leading-5 text-zinc-300">{{ item.description }}</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="mb-8 max-w-2xl">
                <p class="text-sm font-black uppercase tracking-[0.2em] text-[#7f241f]">Proceso</p>
                <h2 class="mt-3 text-3xl font-black sm:text-4xl">Una secuencia simple, sin ruido</h2>
                <p class="mt-4 text-base leading-8 text-zinc-600">
                    La pagina avanza en tres capas: entendemos el evento, ejecutamos la produccion y cerramos con medicion.
                </p>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <article v-for="item in processSteps" :key="item.title" class="rounded-md border border-zinc-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-[#7f241f]">{{ item.title }}</p>
                    <p class="mt-4 text-sm leading-7 text-zinc-600">{{ item.description }}</p>
                </article>
            </div>
        </section>

        <section class="mesh-bg py-20">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.92fr_1.08fr] lg:px-8">
                <div class="animate-slide-in max-w-xl">
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-[#7f241f]">Portal inteligente</p>
                    <h2 class="mt-3 text-3xl font-black text-zinc-950 sm:text-4xl">Un inicio ordenado para citas, encuestas y seguimiento</h2>
                    <p class="mt-5 text-base leading-8 text-zinc-600">
                        El portal separa lo comercial de lo operativo. El cliente entra, agenda, responde y sigue el estado del proceso sin ruido
                        visual.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <Link
                            :href="route('login')"
                            class="shine-hover inline-flex justify-center rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white"
                        >
                            Iniciar sesion
                        </Link>
                        <Link
                            :href="route('register')"
                            class="inline-flex justify-center rounded-md border border-zinc-300 bg-white px-5 py-3 text-sm font-black text-zinc-950"
                        >
                            Crear cuenta
                        </Link>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <article class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                        <Headphones class="h-6 w-6 text-[#a8322b]" />
                        <h3 class="mt-4 text-lg font-black">Servicios</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">Portafolio completo con detalle por categoria.</p>
                    </article>
                    <article class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                        <Sparkles class="h-6 w-6 text-[#a8322b]" />
                        <h3 class="mt-4 text-lg font-black">Experiencia</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">Produccion limpia, visual clara y acompanamiento profesional.</p>
                    </article>
                    <article class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                        <Users class="h-6 w-6 text-[#a8322b]" />
                        <h3 class="mt-4 text-lg font-black">Equipo</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">Conoce a los responsables de cada parte del proceso.</p>
                    </article>
                    <article class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                        <ShieldCheck class="h-6 w-6 text-[#a8322b]" />
                        <h3 class="mt-4 text-lg font-black">Confianza</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">Cada cita y cada encuesta queda trazada en el portal.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-zinc-100 py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 max-w-2xl">
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-[#a8322b]">Portafolio</p>
                    <h2 class="mt-3 text-3xl font-black sm:text-4xl">Servicios principales</h2>
                    <p class="mt-4 text-base leading-8 text-zinc-600">Una muestra resumida para que la portada no se sienta cargada.</p>
                </div>

                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="service in featuredServices"
                        :key="service.slug"
                        class="group overflow-hidden rounded-md bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                    >
                        <img
                            :src="service.image"
                            :alt="service.title"
                            class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105"
                        />
                        <div class="p-5">
                            <Headphones class="h-6 w-6 text-[#a8322b]" />
                            <h3 class="mt-3 text-lg font-black">{{ service.title }}</h3>
                            <p class="mt-2 line-clamp-3 text-sm leading-6 text-zinc-600">{{ service.description }}</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div class="max-w-2xl">
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-[#a8322b]">Redes</p>
                    <h2 class="mt-3 text-3xl font-black sm:text-4xl">Noticias y contenido social</h2>
                    <p class="mt-4 text-base leading-8 text-zinc-600">
                        Lo que publicamos en Instagram y Facebook se organiza aqui como referencias visuales de produccion y cobertura.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a
                        v-for="profile in socialProfiles"
                        :key="profile.label"
                        :href="profile.href"
                        target="_blank"
                        rel="noopener"
                        class="rounded-md border border-zinc-200 bg-white px-4 py-3 text-sm font-black text-zinc-950 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                    >
                        {{ profile.label }}
                    </a>
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-3">
                <a
                    v-for="notice in notices"
                    :key="notice.id"
                    :href="route('news.show', notice.slug)"
                    class="overflow-hidden rounded-md border border-zinc-200 bg-white text-zinc-950 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg"
                >
                    <img :src="notice.image_url || assets.projectImage" :alt="notice.title" class="aspect-[4/3] w-full object-cover" />
                    <div class="p-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-[#f0c8be] px-2.5 py-1 text-[11px] font-black uppercase tracking-wide text-[#7f241f]">{{
                                notice.platform || notice.category
                            }}</span>
                            <span class="text-xs font-black uppercase tracking-wide text-[#7f241f]">{{ notice.published_at || 'Publicado' }}</span>
                        </div>
                        <h3 class="mt-3 text-lg font-black">{{ notice.title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">{{ notice.excerpt }}</p>
                    </div>
                </a>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-[0.92fr_1.08fr]">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-[#a8322b]">Equipo</p>
                    <h2 class="mt-3 text-3xl font-black sm:text-4xl">Conoce nuestro equipo</h2>
                    <p class="mt-4 max-w-xl text-base leading-8 text-zinc-600">
                        Talento comprometido, emprendedor y apasionado por la produccion de eventos de calidad.
                    </p>
                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <article v-for="person in team" :key="person.name" class="text-center">
                            <img
                                :src="person.image"
                                :alt="person.name"
                                class="mx-auto aspect-[4/5] w-full max-w-56 rounded-md object-cover shadow-sm"
                            />
                            <h3 class="mt-4 text-lg font-black">{{ person.name }}</h3>
                            <p class="text-sm text-zinc-600">{{ person.role }}</p>
                        </article>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-[#a8322b]">Noticias</p>
                    <h2 class="mt-3 text-3xl font-black sm:text-4xl">Ultimos acontecimientos</h2>
                    <p class="mt-4 max-w-xl text-base leading-8 text-zinc-600">
                        Referencias reales de produccion para mostrar actividad y continuidad.
                    </p>
                    <div class="mt-8 grid gap-5">
                        <a
                            v-for="notice in notices"
                            :key="notice.id"
                            :href="route('news.show', notice.slug)"
                            class="overflow-hidden rounded-md border border-zinc-200 bg-white text-zinc-950 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg"
                        >
                            <div class="grid gap-0 md:grid-cols-[0.95fr_1.05fr]">
                                <img :src="notice.image_url || assets.projectImage" :alt="notice.title" class="h-full min-h-52 w-full object-cover" />
                                <div class="p-6">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="rounded-full bg-[#f0c8be] px-2.5 py-1 text-[11px] font-black uppercase tracking-wide text-[#7f241f]"
                                            >{{ notice.platform || notice.category }}</span
                                        >
                                        <span class="text-xs font-black uppercase tracking-wide text-[#7f241f]">{{ notice.published_at || 'Publicado' }}</span>
                                    </div>
                                    <h3 class="mt-2 text-lg font-black">{{ notice.title }}</h3>
                                    <p class="mt-3 text-sm leading-7 text-zinc-600">{{ notice.excerpt }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <img :src="assets.teamPhoto" alt="Equipo de trabajo de CAFE Producciones" class="mt-16 aspect-[16/7] w-full rounded-md object-cover" />
        </section>
    </MarketingLayout>
</template>
