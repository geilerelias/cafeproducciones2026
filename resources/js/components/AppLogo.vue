<script setup lang="ts">
import { assets, brand } from '@/data/site';

interface Props {
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <span
        class="grid aspect-square size-9 shrink-0 place-items-center rounded-md bg-white shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10"
    >
        <img :src="assets.logoImage" :alt="brand.name" class="size-7 object-contain" />
    </span>
    <div class="ml-2 grid min-w-0 flex-1 text-left leading-tight group-data-[collapsible=icon]:hidden">
        <span class="truncate text-sm font-black uppercase tracking-wide text-sidebar-foreground">{{ brand.name }}</span>
        <span class="truncate text-xs font-medium text-sidebar-foreground/70">Portal operativo</span>
    </div>
</template>
