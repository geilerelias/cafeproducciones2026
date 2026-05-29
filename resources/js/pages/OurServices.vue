<script setup lang="ts">
import { assets, brand, processSteps, services } from '@/data/site';
import MarketingLayout from '@/layouts/MarketingLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, CheckCircle2 } from 'lucide-vue-next';
</script>

<template>
    <MarketingLayout
        title="Servicios"
        description="Servicios de sonido, montaje, transmision, pantallas, iluminacion, pirotecnia, mobiliario y logistica para eventos."
    >
        <section class="bg-zinc-950 py-20 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p class="text-sm font-black uppercase tracking-[0.2em] text-[#f0c8be]">Servicios</p>
                <h1 class="mt-4 max-w-4xl text-4xl font-black sm:text-5xl">Produccion integral para eventos con equipos, personal y tecnologia</h1>
                <p class="mt-5 max-w-3xl text-lg leading-8 text-zinc-300">
                    Un portafolio creado para cubrir necesidades tecnicas, logisticas, audiovisuales y de experiencia en cada etapa del evento.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                <a
                    v-for="service in services"
                    :key="service.slug"
                    :href="`#${service.slug}`"
                    class="group overflow-hidden rounded-md border border-zinc-200 bg-white shadow-sm"
                >
                    <img
                        :src="service.image"
                        :alt="service.title"
                        class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105"
                    />
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-4">
                            <h2 class="text-lg font-black">{{ service.title }}</h2>
                            <span class="shrink-0 text-xs font-black text-[#7f241f]">{{ service.images.length }} fotos</span>
                        </div>
                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-zinc-600">{{ service.description }}</p>
                    </div>
                </a>
            </div>
        </section>

        <section class="bg-zinc-100 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-black">Detalle de servicios</h2>
                <div class="mt-8 space-y-8">
                    <article
                        v-for="(service, index) in services"
                        :id="service.slug"
                        :key="service.slug"
                        class="grid overflow-hidden rounded-md bg-white shadow-sm lg:grid-cols-2"
                    >
                        <img
                            :src="service.image"
                            :alt="service.title"
                            class="h-full min-h-80 w-full object-cover"
                            :class="{ 'lg:order-2': index % 2 === 0 }"
                        />
                        <div class="p-6 sm:p-10">
                            <p class="text-sm font-black uppercase tracking-[0.2em] text-[#a8322b]">
                                Servicio {{ String(index + 1).padStart(2, '0') }}
                            </p>
                            <h3 class="mt-3 text-2xl font-black">{{ service.title }}</h3>
                            <p class="mt-4 leading-8 text-zinc-600">{{ service.description }}</p>
                            <div v-if="service.images.length" class="mt-6 grid grid-cols-4 gap-2">
                                <img
                                    v-for="image in service.images.slice(0, 4)"
                                    :key="image"
                                    :src="image"
                                    :alt="service.title"
                                    class="aspect-square rounded-md object-cover"
                                />
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8">
            <img :src="assets.projectImage" alt="Nuevo proyecto CAFE Producciones" class="aspect-[4/3] w-full rounded-md object-cover" />
            <div>
                <p class="text-sm font-black uppercase tracking-[0.2em] text-[#a8322b]">Nuevo proyecto</p>
                <h2 class="mt-3 text-3xl font-black">De la idea a la ejecucion final</h2>
                <p class="mt-5 leading-8 text-zinc-600">
                    Trabajamos de cerca con el cliente desde la conceptualizacion hasta el cierre operativo, cuidando la experiencia, los tiempos, el
                    montaje y la coordinacion general.
                </p>
                <a
                    :href="brand.whatsapp"
                    target="_blank"
                    rel="noopener"
                    class="mt-8 inline-flex items-center gap-2 rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white"
                >
                    Solicitar cotizacion
                    <ArrowRight class="h-4 w-4" />
                </a>
            </div>
        </section>

        <section class="bg-zinc-950 py-16 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-black">Proceso de trabajo</h2>
                <div class="mt-8 grid gap-4 md:grid-cols-5">
                    <article v-for="([title, description], index) in processSteps" :key="title" class="rounded-md border border-white/10 p-5">
                        <CheckCircle2 class="h-6 w-6 text-[#f0c8be]" />
                        <p class="mt-4 text-sm font-black text-[#f0c8be]">0{{ index + 1 }}</p>
                        <h3 class="mt-2 font-black">{{ title }}</h3>
                        <p class="mt-3 text-sm leading-6 text-zinc-300">{{ description }}</p>
                    </article>
                </div>
                <div class="mt-10">
                    <Link :href="route('contact-us')" class="rounded-md bg-[#a8322b] px-5 py-3 text-sm font-black text-white">Contactenos</Link>
                </div>
            </div>
        </section>
    </MarketingLayout>
</template>
