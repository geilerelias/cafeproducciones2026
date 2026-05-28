<script setup lang="ts">
import DashboardPreferencesPanel from '@/components/DashboardPreferencesPanel.vue';
import { useAppearance } from '@/composables/useAppearance';
import { accentPresets, useUiPreferences } from '@/composables/useUiPreferences';
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
const { accentTone, menuPlacement, menuAlignment, updateAccentTone, updateMenuPlacement, updateMenuAlignment } = useUiPreferences();

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

const headerShellClass = computed(() =>
    menuPlacement.value === 'floating'
        ? 'mx-auto mt-3 max-w-7xl rounded-2xl border border-white/70 bg-white/80 shadow-[0_16px_50px_rgba(15,23,42,0.08)] backdrop-blur-xl dark:border-white/10 dark:bg-zinc-950/80'
        : 'border-b border-black/5 bg-white/90 shadow-[0_12px_40px_rgba(15,23,42,0.05)] backdrop-blur-xl dark:border-white/10 dark:bg-zinc-950/85',
);

const navAlignmentClass = computed(() =>
    menuAlignment.value === 'left' ? 'justify-start' : menuAlignment.value === 'right' ? 'justify-end' : 'justify-center',
);

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
        <header class="sticky top-0 z-50">
            <div :class="headerShellClass">
                <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <Link :href="route('home')" class="group flex min-w-0 items-center gap-3">
                        <span
                            class="grid h-12 w-12 shrink-0 place-items-center rounded-md shadow-lg ring-1 ring-black/5 transition group-hover:-translate-y-0.5 dark:bg-white"
                        >
                            <img :src="assets.logoImage" alt="CAFE Producciones" class="h-10 w-10 object-contain" />
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-black uppercase tracking-wide text-zinc-950 dark:text-white sm:text-base"
                                >CAFE Producciones</span
                            >
                            <span class="block truncate text-xs font-semibold text-zinc-500 dark:text-zinc-400">Eventos, logistica y produccion</span>
                        </span>
                    </Link>

                    <nav class="hidden flex-1 lg:flex" :class="navAlignmentClass">
                        <div
                            class="inline-flex items-center gap-1 rounded-full border border-zinc-200 bg-white/90 p-1 shadow-sm dark:border-white/10 dark:bg-white/5"
                        >
                            <Link
                                v-for="item in navItems"
                                :key="item.route"
                                :href="route(item.route)"
                                class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition"
                                :class="
                                    route().current(item.route)
                                        ? 'brand-accent-bg shadow-lg'
                                        : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-white/10 dark:hover:text-white'
                                "
                            >
                                <component :is="item.icon" class="h-4 w-4" />
                                {{ item.title }}
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

                    <div class="ml-auto lg:hidden">
                        <button
                            type="button"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-zinc-200 bg-white shadow-sm dark:border-white/10 dark:bg-white/5"
                            aria-label="Abrir menu"
                            @click="open = !open"
                        >
                            <X v-if="open" class="h-5 w-5" />
                            <Menu v-else class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <div v-if="open" class="border-t border-zinc-200 bg-white px-4 py-4 shadow-xl dark:border-white/10 dark:bg-zinc-950 lg:hidden">
                    <nav class="grid gap-2">
                        <Link
                            v-for="item in navItems"
                            :key="item.route"
                            :href="route(item.route)"
                            class="flex items-center justify-between rounded-md px-4 py-3 text-sm font-semibold transition"
                            :class="route().current(item.route) ? 'brand-accent-bg' : 'bg-zinc-100 text-zinc-800 dark:bg-white/5 dark:text-zinc-100'"
                            @click="open = false"
                        >
                            <span class="inline-flex items-center gap-2">
                                <component :is="item.icon" class="h-4 w-4" />
                                {{ item.title }}
                            </span>
                        </Link>
                    </nav>

                    <div class="mt-4 grid gap-4">
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                v-for="option in themeOptions"
                                :key="option.value"
                                type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-md px-3 py-2 text-sm font-bold transition"
                                :class="
                                    appearance === option.value ? 'brand-accent-bg' : 'bg-zinc-100 text-zinc-700 dark:bg-white/5 dark:text-zinc-200'
                                "
                                @click="updateAppearance(option.value)"
                            >
                                <component :is="option.icon" class="h-4 w-4" />
                                {{ option.label }}
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <button
                                class="rounded-md border px-3 py-2 text-sm font-semibold"
                                :class="
                                    menuPlacement === 'top'
                                        ? 'brand-accent-border brand-accent-soft-bg text-zinc-950'
                                        : 'border-zinc-200 bg-white dark:border-white/10 dark:bg-white/5'
                                "
                                @click="updateMenuPlacement('top')"
                            >
                                Menú arriba
                            </button>
                            <button
                                class="rounded-md border px-3 py-2 text-sm font-semibold"
                                :class="
                                    menuPlacement === 'floating'
                                        ? 'brand-accent-border brand-accent-soft-bg text-zinc-950'
                                        : 'border-zinc-200 bg-white dark:border-white/10 dark:bg-white/5'
                                "
                                @click="updateMenuPlacement('floating')"
                            >
                                Menú flotante
                            </button>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <button
                                class="rounded-md border px-3 py-2 text-xs font-bold"
                                :class="
                                    menuAlignment === 'left'
                                        ? 'brand-accent-border brand-accent-soft-bg text-zinc-950'
                                        : 'border-zinc-200 bg-white dark:border-white/10 dark:bg-white/5'
                                "
                                @click="updateMenuAlignment('left')"
                            >
                                Izquierda
                            </button>
                            <button
                                class="rounded-md border px-3 py-2 text-xs font-bold"
                                :class="
                                    menuAlignment === 'center'
                                        ? 'brand-accent-border brand-accent-soft-bg text-zinc-950'
                                        : 'border-zinc-200 bg-white dark:border-white/10 dark:bg-white/5'
                                "
                                @click="updateMenuAlignment('center')"
                            >
                                Centro
                            </button>
                            <button
                                class="rounded-md border px-3 py-2 text-xs font-bold"
                                :class="
                                    menuAlignment === 'right'
                                        ? 'brand-accent-border brand-accent-soft-bg text-zinc-950'
                                        : 'border-zinc-200 bg-white dark:border-white/10 dark:bg-white/5'
                                "
                                @click="updateMenuAlignment('right')"
                            >
                                Derecha
                            </button>
                        </div>

                        <div class="grid grid-cols-4 gap-2">
                            <button
                                v-for="(preset, key) in accentPresets"
                                :key="key"
                                type="button"
                                class="rounded-md border px-2 py-2 text-left text-xs font-bold transition"
                                :class="
                                    accentTone === key
                                        ? 'border-transparent text-white shadow-md'
                                        : 'border-zinc-200 bg-white text-zinc-700 dark:border-white/10 dark:bg-white/5 dark:text-zinc-200'
                                "
                                :style="{
                                    backgroundColor: accentTone === key ? preset.base : undefined,
                                    color: accentTone === key ? preset.foreground : undefined,
                                }"
                                @click="updateAccentTone(key)"
                            >
                                <span class="block h-2 w-8 rounded-full" :style="{ backgroundColor: preset.base }"></span>
                                <span class="mt-2 block">{{ preset.label }}</span>
                            </button>
                        </div>

                        <Link
                            :href="route('login')"
                            class="brand-accent-bg inline-flex justify-center rounded-md px-4 py-3 text-sm font-black shadow-lg"
                            @click="open = false"
                        >
                            Iniciar sesion para agendar
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <slot />
        </main>

        <DashboardPreferencesPanel />

        <footer class="relative overflow-hidden bg-white text-zinc-950 dark:bg-zinc-950 dark:text-zinc-50">
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[var(--brand-accent)] to-transparent"></div>
            <div class="absolute -right-24 top-10 h-72 w-72 rounded-full bg-[#a8322b]/10 blur-3xl"></div>
            <div class="absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-[#6b625d]/10 blur-3xl"></div>
            <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-[1.2fr_0.8fr_1fr] lg:px-8">
                <div>
                    <div class="mb-5 flex items-center gap-3">
                        <span class="grid h-16 w-16 place-items-center rounded-md bg-black/5 ring-1 ring-black/5 dark:bg-white/10 dark:ring-white/10">
                            <img :src="assets.logoTransparent" alt="CAFE Producciones" class="h-12 w-12 object-contain" />
                        </span>
                        <div>
                            <p class="text-lg font-black uppercase tracking-wide">CAFE Producciones</p>
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
                    <div class="mt-5 grid gap-2">
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
                <div>
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-[#a8322b] dark:text-[#f0c8be]">Contacto</h2>
                    <div class="mt-5 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                        <p class="leading-6">{{ brand.address }}</p>
                        <a class="block hover:text-zinc-950 dark:hover:text-white" :href="`tel:${brand.phone}`">{{ brand.phone }}</a>
                        <a class="block hover:text-zinc-950 dark:hover:text-white" :href="`tel:${brand.secondaryPhone}`">{{
                            brand.secondaryPhone
                        }}</a>
                        <a class="block hover:text-zinc-950 dark:hover:text-white" :href="`mailto:${brand.email}`">{{ brand.email }}</a>
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
