<script setup lang="ts">
import { assets, brand } from '@/data/site';
import MarketingLayout from '@/layouts/MarketingLayout.vue';
import { Link } from '@inertiajs/vue3';

type NewsItem = {
    id: number;
    title: string;
    slug: string;
    category: string;
    platform: string | null;
    image_url: string | null;
    excerpt: string;
    published_at: string | null;
};

defineProps<{ news: NewsItem[] }>();
</script>

<template>
    <MarketingLayout title="Noticias" description="Noticias, coberturas y publicaciones sociales de CAFE Producciones.">
        <section class="bg-zinc-950 py-20 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p class="text-sm font-black uppercase tracking-[0.2em] text-[#f0c8be]">Noticias</p>
                <h1 class="mt-4 max-w-4xl text-4xl font-black sm:text-5xl">Actividad, coberturas y redes</h1>
                <p class="mt-5 max-w-3xl text-lg leading-8 text-zinc-300">{{ brand.description }}</p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                <Link v-for="item in news" :key="item.id" :href="route('news.show', item.slug)" class="overflow-hidden rounded-md border border-zinc-200 bg-white text-zinc-950 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                    <img :src="item.image_url || assets.projectImage" :alt="item.title" class="aspect-[4/3] w-full object-cover" />
                    <div class="p-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-[#f0c8be] px-2.5 py-1 text-[11px] font-black uppercase tracking-wide text-[#7f241f]">{{ item.platform || item.category }}</span>
                            <span class="text-xs font-black uppercase tracking-wide text-[#7f241f]">{{ item.published_at || 'Publicado' }}</span>
                        </div>
                        <h2 class="mt-3 text-lg font-black">{{ item.title }}</h2>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">{{ item.excerpt }}</p>
                    </div>
                </Link>
            </div>
        </section>
    </MarketingLayout>
</template>
