<script setup lang="ts">
import { assets } from '@/data/site';
import MarketingLayout from '@/layouts/MarketingLayout.vue';

type NewsItem = {
    title: string;
    category: string;
    platform: string | null;
    source_url: string | null;
    image_url: string | null;
    excerpt: string;
    body: string | null;
    published_at: string | null;
    meta_description: string | null;
};

defineProps<{ news: NewsItem }>();
</script>

<template>
    <MarketingLayout :title="news.title" :description="news.meta_description || news.excerpt" :image="news.image_url || assets.projectImage">
        <article>
            <section class="relative isolate overflow-hidden bg-zinc-950 py-24 text-white">
                <img :src="news.image_url || assets.projectImage" :alt="news.title" class="absolute inset-0 h-full w-full object-cover opacity-35" />
                <div class="absolute inset-0 bg-zinc-950/70"></div>
                <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-[#f0c8be] px-3 py-1 text-xs font-black uppercase tracking-wide text-[#7f241f]">{{
                            news.platform || news.category
                        }}</span>
                        <span class="rounded-full border border-white/20 px-3 py-1 text-xs font-black uppercase tracking-wide">{{
                            news.published_at || 'Publicado'
                        }}</span>
                    </div>
                    <h1 class="mt-5 text-4xl font-black sm:text-5xl">{{ news.title }}</h1>
                    <p class="mt-5 text-lg leading-8 text-zinc-200">{{ news.excerpt }}</p>
                </div>
            </section>

            <section class="mx-auto max-w-3xl px-4 py-14 sm:px-6 lg:px-8">
                <div class="whitespace-pre-line text-base leading-8 text-zinc-700">{{ news.body || news.excerpt }}</div>
                <a
                    v-if="news.source_url"
                    :href="news.source_url"
                    target="_blank"
                    rel="noopener"
                    class="mt-8 inline-flex rounded-md bg-[#a8322b] px-5 py-3 text-sm font-black text-white"
                >
                    Ver publicacion original
                </a>
            </section>
        </article>
    </MarketingLayout>
</template>
