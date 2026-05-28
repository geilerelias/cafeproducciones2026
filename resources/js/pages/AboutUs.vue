<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Eye, Flag, Target } from 'lucide-vue-next';
import MarketingLayout from '@/layouts/MarketingLayout.vue';
import { assets, brand, features, team } from '@/data/site';
</script>

<template>
    <MarketingLayout title="Nosotros" description="Conoce la mision, vision, equipo y experiencia de CAFE Producciones en logistica y produccion de eventos.">
        <section class="relative overflow-hidden bg-zinc-950 py-16 text-white sm:py-20 lg:py-24">
            <img :src="assets.aboutImage" alt="Equipo CAFE Producciones" class="absolute inset-0 h-full w-full object-cover opacity-35" />
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-[#f0c8be]">Quienes somos</p>
                    <h1 class="mt-4 text-3xl font-black leading-tight sm:text-4xl lg:text-5xl">Somos un equipo excepcional donde la dedicacion y el talento se unen</h1>
                    <p class="mt-5 text-lg leading-8 text-zinc-100">
                        Acompanamos a nuestros clientes en la logistica de eventos locales, regionales y nacionales con un portafolio integral.
                    </p>
                    <Link :href="route('contact-us')" class="mt-8 inline-flex items-center gap-2 rounded-md bg-[#a8322b] px-5 py-3 text-sm font-black text-white hover:bg-[#b44136]">
                        Contactenos
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </section>

        <section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.2em] text-[#a8322b]">Nuestra esencia</p>
                <h2 class="mt-3 text-3xl font-black">Una mejor forma de construir su proximo evento</h2>
                <p class="mt-5 text-base leading-8 text-zinc-600">
                    CAFE Producciones ofrece servicios para cubrir necesidades de produccion, montaje, sonido, logistica, tecnologia y acompanamiento. Trabajamos de cerca con cada cliente para convertir ideas en experiencias memorables.
                </p>
            </div>
            <img :src="assets.teamPhoto" alt="Equipo de CAFE Producciones trabajando" class="aspect-[4/3] w-full rounded-md object-cover" />
        </section>

        <section class="bg-zinc-100 py-16">
            <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 md:grid-cols-3 lg:px-8">
                <article class="rounded-md bg-white p-6 shadow-sm">
                    <Flag class="h-8 w-8 text-[#a8322b]" />
                    <h2 class="mt-4 text-2xl font-black">Mision</h2>
                    <p class="mt-3 leading-7 text-zinc-600">Brindar experiencias unicas e innovadoras en cada evento, ofreciendo excelencia y calidad en nuestros servicios.</p>
                </article>
                <article class="rounded-md bg-white p-6 shadow-sm">
                    <Eye class="h-8 w-8 text-[#a8322b]" />
                    <h2 class="mt-4 text-2xl font-black">Vision</h2>
                    <p class="mt-3 leading-7 text-zinc-600">Ser la empresa lider en el mercado de produccion y logistica para todo tipo de eventos.</p>
                </article>
                <article class="rounded-md bg-white p-6 shadow-sm">
                    <Target class="h-8 w-8 text-[#a8322b]" />
                    <h2 class="mt-4 text-2xl font-black">Proposito</h2>
                    <p class="mt-3 leading-7 text-zinc-600">Crear momentos impactantes y organizados que permanezcan en la memoria de quienes participan.</p>
                </article>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="feature in features" :key="feature.title" class="rounded-md border border-zinc-200 p-5">
                    <h3 class="text-lg font-black">{{ feature.title }}</h3>
                    <p class="mt-2 text-sm leading-6 text-zinc-600">{{ feature.description }}</p>
                </article>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
            <h2 class="text-center text-3xl font-black">Nuestro equipo</h2>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <article v-for="person in team" :key="person.name" class="text-center">
                    <img :src="person.image" :alt="person.name" class="mx-auto aspect-[4/5] w-full max-w-56 rounded-md object-cover shadow-sm" />
                    <h3 class="mt-4 text-lg font-black">{{ person.name }}</h3>
                    <p class="text-sm text-zinc-600">{{ person.role }}</p>
                </article>
            </div>
        </section>

        <section class="bg-zinc-950 px-4 py-12 text-white">
            <div class="mx-auto flex max-w-7xl flex-col justify-between gap-6 sm:flex-row sm:items-center">
                <div>
                    <h2 class="text-2xl font-black">Tienes una pregunta?</h2>
                    <p class="mt-2 text-zinc-300">{{ brand.phone }} - {{ brand.email }}</p>
                </div>
                <a :href="brand.whatsapp" target="_blank" rel="noopener" class="rounded-md bg-[#a8322b] px-5 py-3 text-sm font-black text-white">Hablar con nosotros</a>
            </div>
        </section>
    </MarketingLayout>
</template>
