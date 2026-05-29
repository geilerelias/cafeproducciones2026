<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { assets, brand, navigation } from '@/data/site';
import { Head, Link } from '@inertiajs/vue3';
import { BriefcaseBusiness, GalleryHorizontalEnd, Home, Menu, Monitor, Moon, Newspaper, Phone, Sun, Users, X } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps<{
    title: string;
    description?: string;
    image?: string;
    url?: string;
    type?: string;
}>();

const open = ref(false);
const { appearance, updateAppearance } = useAppearance();

const themeOptions = [
    { value: 'light', label: 'Claro', icon: Sun },
    { value: 'dark', label: 'Oscuro', icon: Moon },
    { value: 'system', label: 'Sistema', icon: Monitor },
] as const;

const navIcons = {
    home: Home,
    'about-us': Users,
    'our-services': BriefcaseBusiness,
    'our-gallery': GalleryHorizontalEnd,
    'news.index': Newspaper,
    'contact-us': Phone,
};

const navItems = computed(() =>
    navigation.map((item) => ({
        ...item,
        icon: navIcons[item.route as keyof typeof navIcons] ?? Home,
    })),
);

const pageTitle = computed(() => `${props.title} | ${brand.name}`);
const pageDescription = computed(() => props.description ?? brand.description);
const pageImage = computed(() => props.image ?? assets.heroImage);
const pageType = computed(() => props.type ?? 'website');
const pageUrl = computed(() => props.url ?? '');

const organizationSchema = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'LocalBusiness',
        name: brand.name,
        legalName: brand.legalName,
        description: brand.description,
        telephone: brand.phone,
        email: brand.email,
        image: assets.logoImage,
        address: {
            '@type': 'PostalAddress',
            streetAddress: brand.address,
            addressLocality: brand.city,
            addressRegion: brand.region,
            addressCountry: brand.country,
        },
        sameAs: [brand.instagram, brand.facebook],
    }),
);

let schemaElement: HTMLScriptElement | null = null;

const syncOrganizationSchema = () => {
    if (typeof document === 'undefined') {
        return;
    }

    if (!schemaElement) {
        schemaElement = document.createElement('script');
        schemaElement.type = 'application/ld+json';
        schemaElement.id = 'organization-schema';
        document.head.appendChild(schemaElement);
    }

    schemaElement.textContent = organizationSchema.value;
};

onMounted(() => {
    syncOrganizationSchema();
});

watch(organizationSchema, syncOrganizationSchema);

onBeforeUnmount(() => {
    schemaElement?.remove();
    schemaElement = null;
});
</script>

<template>
    <Head>
        <title>{{ pageTitle }}</title>
        <meta name="description" :content="pageDescription" />
        <meta name="robots" content="index, follow" />
        <meta property="og:title" :content="pageTitle" />
        <meta property="og:description" :content="pageDescription" />
        <meta property="og:type" :content="pageType" />
        <meta property="og:image" :content="pageImage" />
        <meta v-if="pageUrl" property="og:url" :content="pageUrl" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="pageTitle" />
        <meta name="twitter:description" :content="pageDescription" />
        <meta name="twitter:image" :content="pageImage" />
    </Head>

    <div class="marketing-shell min-h-screen bg-white text-zinc-950 antialiased dark:bg-zinc-950 dark:text-zinc-50">
        <header
            class="sticky top-0 z-50 border-b border-black/5 bg-white/90 shadow-[0_12px_40px_rgba(15,23,42,0.05)] backdrop-blur-xl dark:border-white/10 dark:bg-zinc-950/85"
        >
            <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3 sm:gap-4 sm:px-6 sm:py-4 lg:px-8">
                <Link :href="route('home')" class="group flex min-w-0 items-center gap-2.5 sm:gap-3">
                    <span
                        class="grid h-10 w-10 shrink-0 place-items-center rounded-md shadow-lg ring-1 ring-black/5 transition group-hover:-translate-y-0.5 dark:bg-white sm:h-12 sm:w-12"
                    >
                        <img :src="assets.logoImage" alt="CAFE Producciones" class="h-8 w-8 object-contain sm:h-10 sm:w-10" />
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-xs font-black uppercase tracking-wide text-zinc-950 dark:text-white sm:text-base"
                            >CAFE Producciones</span
                        >
                        <span class="hidden truncate text-xs font-semibold text-zinc-500 dark:text-zinc-400 sm:block"
                            >Eventos, logistica y produccion</span
                        >
                    </span>
                </Link>

                <nav class="hidden flex-1 justify-center lg:flex">
                    <div
                        class="inline-flex max-w-full flex-wrap items-center justify-center gap-1 rounded-full border border-zinc-200 bg-white/90 p-1 shadow-sm dark:border-white/10 dark:bg-white/5"
                    >
                        <Link
                            v-for="item in navItems"
                            :key="item.route"
                            :href="route(item.route)"
                            class="inline-flex items-center gap-2 rounded-full px-3 py-2 text-sm font-semibold transition xl:px-4"
                            :class="
                                route().current(item.route)
                                    ? 'brand-accent-bg shadow-lg'
                                    : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-white/10 dark:hover:text-white'
                            "
                        >
                            <component :is="item.icon" class="h-4 w-4 shrink-0" />
                            <span class="whitespace-nowrap">{{ item.title }}</span>
                        </Link>
                    </div>
                </nav>

                <div class="hidden items-center gap-2 lg:flex">
                    <div
                        class="inline-flex items-center gap-1 rounded-full border border-zinc-200 bg-white/90 p-1 shadow-sm dark:border-white/10 dark:bg-white/5"
                    >
                        <button
                            v-for="option in themeOptions"
                            :key="option.value"
                            type="button"
                            class="grid h-9 w-9 place-items-center rounded-full transition"
                            :class="
                                appearance === option.value
                                    ? 'brand-accent-bg shadow-md'
                                    : 'text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-white/10'
                            "
                            :title="option.label"
                            @click="updateAppearance(option.value)"
                        >
                            <component :is="option.icon" class="h-4 w-4" />
                        </button>
                    </div>

                    <Link
                        :href="route('login')"
                        class="brand-accent-bg inline-flex items-center justify-center rounded-full px-5 py-2.5 text-sm font-black shadow-lg transition hover:-translate-y-0.5"
                    >
                        Portal
                    </Link>
                </div>

                <div class="ml-auto flex items-center gap-2 lg:hidden">
                    <Link
                        :href="route('login')"
                        class="brand-accent-bg inline-flex items-center justify-center rounded-full px-3 py-2 text-xs font-black shadow-md sm:px-4 sm:text-sm"
                    >
                        Portal
                    </Link>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-zinc-200 bg-white shadow-sm dark:border-white/10 dark:bg-white/5"
                        :aria-expanded="open"
                        aria-label="Abrir menu"
                        @click="open = !open"
                    >
                        <X v-if="open" class="h-5 w-5" />
                        <Menu v-else class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <div
                v-if="open"
                class="max-h-[calc(100dvh-4.5rem)] overflow-y-auto border-t border-zinc-200 bg-white px-4 py-4 shadow-xl dark:border-white/10 dark:bg-zinc-950 lg:hidden"
            >
                <nav class="grid gap-2">
                    <Link
                        v-for="item in navItems"
                        :key="item.route"
                        :href="route(item.route)"
                        class="flex items-center gap-3 rounded-md px-4 py-3 text-sm font-semibold transition"
                        :class="route().current(item.route) ? 'brand-accent-bg' : 'bg-zinc-100 text-zinc-800 dark:bg-white/5 dark:text-zinc-100'"
                        @click="open = false"
                    >
                        <component :is="item.icon" class="h-4 w-4 shrink-0" />
                        {{ item.title }}
                    </Link>
                </nav>
            </div>
        </header>

        <main class="min-w-0">
            <slot />
        </main>

        <footer class="relative overflow-hidden bg-white text-zinc-950 dark:bg-zinc-950 dark:text-zinc-50">
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[var(--brand-accent)] to-transparent"></div>
            <div class="absolute -right-24 top-10 h-72 w-72 rounded-full bg-[#a8322b]/10 blur-3xl"></div>
            <div class="absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-[#6b625d]/10 blur-3xl"></div>
            <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 sm:py-14 md:grid-cols-2 lg:grid-cols-[1.2fr_0.8fr_1fr] lg:px-8">
                <div class="md:col-span-2 lg:col-span-1">
                    <div class="mb-5 flex items-center gap-3">
                        <span
                            class="grid h-14 w-14 shrink-0 place-items-center rounded-md bg-black/5 ring-1 ring-black/5 dark:bg-white/10 dark:ring-white/10 sm:h-16 sm:w-16"
                        >
                            <img :src="assets.logoTransparent" alt="CAFE Producciones" class="h-10 w-10 object-contain sm:h-12 sm:w-12" />
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-base font-black uppercase tracking-wide sm:text-lg">CAFE Producciones</p>
                            <p class="text-sm text-[#a8322b] dark:text-[#f0c8be]">Produccion integral de eventos</p>
                        </div>
                    </div>
                    <p class="max-w-sm text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ brand.description }}</p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <a
                            :href="brand.instagram"
                            target="_blank"
                            rel="noopener"
                            class="rounded-md border border-black/20 px-3 py-2 text-xs font-bold text-zinc-700 transition hover:border-[var(--brand-accent-border)] hover:text-[#a8322b] dark:border-white/10 dark:text-zinc-300 dark:hover:text-[#f0c8be]"
                            >Instagram</a
                        >
                        <a
                            :href="brand.facebook"
                            target="_blank"
                            rel="noopener"
                            class="rounded-md border border-black/20 px-3 py-2 text-xs font-bold text-zinc-700 transition hover:border-[var(--brand-accent-border)] hover:text-[#a8322b] dark:border-white/10 dark:text-zinc-300 dark:hover:text-[#f0c8be]"
                            >Facebook</a
                        >
                        <a
                            :href="brand.whatsapp"
                            target="_blank"
                            rel="noopener"
                            class="rounded-md border border-black/20 px-3 py-2 text-xs font-bold text-zinc-700 transition hover:border-[var(--brand-accent-border)] hover:text-[#a8322b] dark:border-white/10 dark:text-zinc-300 dark:hover:text-[#f0c8be]"
                            >WhatsApp</a
                        >
                    </div>
                </div>
                <div>
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-[#a8322b] dark:text-[#f0c8be]">Navegacion</h2>
                    <div class="mt-5 grid gap-2 sm:grid-cols-2 md:grid-cols-1">
                        <Link
                            v-for="item in navItems"
                            :key="item.route"
                            :href="route(item.route)"
                            class="group inline-flex items-center gap-2 text-sm font-semibold text-zinc-600 transition hover:text-zinc-950 dark:text-zinc-300 dark:hover:text-white"
                        >
                            <span
                                class="h-px w-4 bg-black/20 transition group-hover:w-7 group-hover:bg-[var(--brand-accent)] dark:bg-white/20"
                            ></span>
                            {{ item.title }}
                        </Link>
                    </div>
                </div>
                <div class="md:col-span-2 lg:col-span-1">
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-[#a8322b] dark:text-[#f0c8be]">Contacto</h2>
                    <div class="mt-5 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                        <p class="leading-6">{{ brand.address }}</p>
                        <a class="block break-all hover:text-zinc-950 dark:hover:text-white" :href="`tel:${brand.phone}`">{{ brand.phone }}</a>
                        <a class="block break-all hover:text-zinc-950 dark:hover:text-white" :href="`tel:${brand.secondaryPhone}`">{{
                            brand.secondaryPhone
                        }}</a>
                        <a class="block break-all hover:text-zinc-950 dark:hover:text-white" :href="`mailto:${brand.email}`">{{ brand.email }}</a>
                    </div>
                    <Link
                        :href="route('login')"
                        class="brand-accent-bg mt-6 inline-flex rounded-md px-4 py-2 text-sm font-black transition hover:-translate-y-0.5"
                    >
                        Entrar al portal
                    </Link>
                </div>
            </div>
            <div class="relative border-t border-black/10 px-4 py-5 text-center text-xs text-zinc-500 dark:border-white/10 dark:text-zinc-400">
                (c) cafeproducciones.com 2021 - {{ new Date().getFullYear() }}. Todos los derechos reservados.
            </div>
        </footer>
    </div>
</template>
