<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import PortalLayoutPicker from '@/components/PortalLayoutPicker.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Apariencia',
        href: '/settings/appearance',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Apariencia" />

        <SettingsLayout>
            <div class="space-y-10">
                <section class="space-y-4">
                    <HeadingSmall title="Apariencia del portal" description="Elige el tema claro, oscuro o el de tu sistema." />
                    <AppearanceTabs />
                </section>

                <section class="space-y-4">
                    <HeadingSmall
                        title="Diseno del portal"
                        description="Elige como quieres ver el menu de navegacion en el panel. El cambio se aplica de inmediato en todas las pantallas."
                    />
                    <PortalLayoutPicker />
                </section>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
