<script setup lang="ts">
import { usePortalLayout } from '@/composables/usePortalLayout';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const { portalLayout } = usePortalLayout();
</script>

<template>
    <AppSidebarLayout v-if="portalLayout === 'sidebar'" :key="'layout-sidebar'" :breadcrumbs="breadcrumbs">
        <slot />
    </AppSidebarLayout>
    <AppHeaderLayout v-else :key="'layout-header'" :breadcrumbs="breadcrumbs">
        <slot />
    </AppHeaderLayout>
</template>
