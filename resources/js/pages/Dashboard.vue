<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ClipboardList, FileText, ShieldCheck, Users } from 'lucide-vue-next';
import { computed } from 'vue';

type DashboardStat = {
    label: string;
    value: number;
    href: string;
    permission: string;
};

type RecentRequest = {
    id: number;
    type: string;
    status: string;
    created_at: string;
    user?: { name: string; email: string };
};

type RecentForm = {
    id: number;
    title: string;
    audience: string;
    is_active: boolean;
};

defineProps<{
    stats: DashboardStat[];
    recentUsers: Array<{ id: number; name: string; email: string }>;
    recentRequests: RecentRequest[];
    recentForms: RecentForm[];
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];
const page = usePage<SharedData>();
const permissions = computed(() => page.props.auth.user?.effective_permissions ?? []);
const can = (permission: string) => permissions.value.includes('*') || permissions.value.includes(permission);

const iconFor = (label: string) => {
    if (label === 'Usuarios') return Users;
    if (label === 'Roles' || label === 'Permisos') return ShieldCheck;
    if (label === 'Solicitudes') return FileText;
    return ClipboardList;
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6 md:p-8">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">Panel general</p>
                <h1 class="mt-3 text-2xl font-black sm:text-3xl md:text-4xl">Resumen operativo</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-zinc-300 md:text-base">
                    Cada modulo tiene su propia pantalla. Usa este panel solo para ver actividad reciente y entrar rapidamente a la seccion correcta.
                </p>
            </section>

            <section class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                <Link
                    v-for="item in stats.filter((stat) => can(stat.permission))"
                    :key="item.label"
                    :href="item.href"
                    class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-[#d7a097] hover:shadow-xl"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-zinc-500">{{ item.label }}</p>
                            <p class="mt-2 text-3xl font-black text-zinc-950">{{ item.value }}</p>
                        </div>
                        <div class="rounded-md bg-[#fff1ee] p-3 text-[#a8322b]">
                            <component :is="iconFor(item.label)" class="h-5 w-5" />
                        </div>
                    </div>
                </Link>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-3">
                <article v-if="can('users.manage')" class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-black">Usuarios recientes</h2>
                    <div class="mt-5 grid gap-3">
                        <div v-for="user in recentUsers" :key="user.id" class="rounded-md bg-zinc-100 p-4">
                            <p class="font-black text-zinc-950">{{ user.name }}</p>
                            <p class="mt-1 text-sm font-semibold text-zinc-500">{{ user.email }}</p>
                        </div>
                    </div>
                </article>

                <article v-if="can('employee.requests.manage')" class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-black">Solicitudes recientes</h2>
                    <div class="mt-5 grid gap-3">
                        <div v-for="request in recentRequests" :key="request.id" class="rounded-md bg-zinc-100 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <p class="font-black text-zinc-950">{{ request.type }}</p>
                                <span class="rounded-md bg-white px-2 py-1 text-xs font-black text-zinc-700">{{ request.status }}</span>
                            </div>
                            <p v-if="request.user" class="mt-2 text-sm font-semibold text-zinc-500">{{ request.user.name }}</p>
                        </div>
                    </div>
                </article>

                <article v-if="can('forms.manage')" class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-black">Encuestas y formularios</h2>
                    <div class="mt-5 grid gap-3">
                        <div v-for="form in recentForms" :key="form.id" class="rounded-md bg-zinc-100 p-4">
                            <p class="font-black text-zinc-950">{{ form.title }}</p>
                            <p class="mt-1 text-sm font-semibold text-zinc-500">{{ form.audience }} · {{ form.is_active ? 'activo' : 'inactivo' }}</p>
                        </div>
                    </div>
                </article>
            </section>
        </main>
    </AppLayout>
</template>
