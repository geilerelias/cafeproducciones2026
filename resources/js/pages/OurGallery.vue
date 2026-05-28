<script setup lang="ts">
import MarketingLayout from '@/layouts/MarketingLayout.vue';
import { brand, galleryImages, services } from '@/data/site';
</script>

<template>
    <MarketingLayout title="Galeria" description="Galeria de imagenes de eventos, montajes, sonido, pantallas, escenarios y producciones realizadas por CAFE Producciones.">
        <section class="bg-zinc-950 py-20 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p class="text-sm font-black uppercase tracking-[0.2em] text-[#f0c8be]">Galeria</p>
                <h1 class="mt-4 max-w-4xl text-4xl font-black sm:text-5xl">Momentos, montajes y experiencias producidas por nuestro equipo</h1>
                <p class="mt-5 max-w-3xl text-lg leading-8 text-zinc-300">
                    Explora registros de eventos, servicios audiovisuales, logistica, escenarios y activaciones.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="columns-1 gap-4 sm:columns-2 lg:columns-3">
                <img
                    v-for="image in galleryImages"
                    :key="image"
                    :src="image"
                    alt="Galeria CAFE Producciones"
                    loading="lazy"
                    class="mb-4 w-full break-inside-avoid rounded-md object-cover shadow-sm"
                />
            </div>
        </section>

        <section class="bg-zinc-100 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-[#a8322b]">Servicios en imagenes</p>
                    <h2 class="mt-3 text-3xl font-black">Galerias por categoria</h2>
                </div>
                <div class="space-y-10">
                    <article v-for="service in services.filter((item) => item.images.length)" :key="service.slug">
                        <div class="mb-4 flex items-end justify-between gap-4">
                            <h3 class="text-xl font-black">{{ service.title }}</h3>
                            <span class="text-sm font-bold text-zinc-500">{{ service.images.length }} imagenes</span>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-4">
                            <img v-for="image in service.images" :key="image" :src="image" :alt="service.title" loading="lazy" class="aspect-square rounded-md object-cover shadow-sm" />
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="px-4 py-14">
            <div class="mx-auto flex max-w-7xl flex-col justify-between gap-5 rounded-md bg-zinc-950 p-8 text-white sm:flex-row sm:items-center">
                <div>
                    <h2 class="text-2xl font-black">Quieres una produccion como estas?</h2>
                    <p class="mt-2 text-zinc-300">Hablemos por WhatsApp y revisemos tu evento.</p>
                </div>
                <a :href="brand.whatsapp" target="_blank" rel="noopener" class="rounded-md bg-[#a8322b] px-5 py-3 text-sm font-black text-white">Cotizar evento</a>
            </div>
        </section>
    </MarketingLayout>
</template>
