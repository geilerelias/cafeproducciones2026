<script setup lang="ts">
import { type PortalLayout, usePortalLayout } from '@/composables/usePortalLayout';
import { router } from '@inertiajs/vue3';
import { LayoutPanelLeft, PanelTop } from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        compact?: boolean;
    }>(),
    {
        compact: false,
    },
);

const { portalLayout, updatePortalLayout } = usePortalLayout();

const options: Array<{
    value: PortalLayout;
    label: string;
    description: string;
    icon: typeof LayoutPanelLeft;
}> = [
    {
        value: 'sidebar',
        label: 'Menu lateral',
        description: 'Barra lateral fija con acceso rapido a todos los modulos.',
        icon: LayoutPanelLeft,
    },
    {
        value: 'header',
        label: 'Menu superior',
        description: 'Navegacion en la parte superior, ideal en pantallas anchas.',
        icon: PanelTop,
    },
];

const selectLayout = (value: PortalLayout) => {
    if (portalLayout.value === value) {
        return;
    }

    updatePortalLayout(value);
    router.reload();
};
</script>

<template>
    <div class="grid gap-3" :class="compact ? 'sm:grid-cols-2' : 'sm:grid-cols-2'">
        <button
            v-for="option in options"
            :key="option.value"
            type="button"
            class="rounded-md border p-4 text-left transition"
            :class="
                portalLayout === option.value
                    ? 'border-[#a8322b] bg-[#fff1ee] shadow-sm dark:border-[#b44136] dark:bg-[#b44136]/10'
                    : 'border-zinc-200 bg-white hover:border-zinc-300 dark:border-white/10 dark:bg-zinc-900 dark:hover:border-white/20'
            "
            @click="selectLayout(option.value)"
        >
            <div class="flex items-start gap-3">
                <span
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-md"
                    :class="portalLayout === option.value ? 'bg-[#a8322b] text-white' : 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300'"
                >
                    <component :is="option.icon" class="h-5 w-5" />
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-black text-zinc-950 dark:text-white">{{ option.label }}</span>
                    <span class="mt-1 block text-xs leading-5 text-zinc-500 dark:text-zinc-400">{{ option.description }}</span>
                </span>
            </div>
        </button>
    </div>
</template>
