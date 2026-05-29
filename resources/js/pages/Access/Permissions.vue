<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { KeyRound, Trash2 } from 'lucide-vue-next';

type PermissionItem = {
    id: number;
    name: string;
    guard_name: string;
    roles_count: number;
};

defineProps<{ permissions: PermissionItem[] }>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Permisos', href: '/access/permissions' }];
const form = useForm({ name: '' });
</script>

<template>
    <Head title="Permisos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">Control de acceso</p>
                <h1 class="mt-2 text-2xl font-black sm:text-3xl">Permisos</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-zinc-300">
                    Crea permisos atomicos y luego asignales roles desde la pantalla de roles.
                </p>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[0.65fr_1.35fr]">
                <form
                    class="rounded-md border border-zinc-200 bg-white p-4 shadow-sm sm:p-5"
                    @submit.prevent="form.post(route('access.permissions.store'), { preserveScroll: true, onSuccess: () => form.reset() })"
                >
                    <div class="flex items-center gap-3">
                        <KeyRound class="h-6 w-6 text-[#a8322b]" />
                        <h2 class="text-xl font-black">Nuevo permiso</h2>
                    </div>
                    <input v-model="form.name" class="mt-5 w-full rounded-md border border-zinc-300 px-3 py-3" placeholder="modulo.accion" />
                    <button class="mt-5 rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white">Crear permiso</button>
                </form>

                <div class="overflow-hidden rounded-md border border-zinc-200 bg-white shadow-sm">
                    <div class="grid gap-0 divide-y divide-zinc-200">
                        <article
                            v-for="permission in permissions"
                            :key="permission.id"
                            class="grid gap-3 p-4 md:grid-cols-[1fr_auto_auto] md:items-center"
                        >
                            <div>
                                <h3 class="font-black text-zinc-950">{{ permission.name }}</h3>
                                <p class="mt-1 text-sm font-semibold text-zinc-500">Guard: {{ permission.guard_name }}</p>
                            </div>
                            <span class="rounded-md bg-zinc-100 px-3 py-1 text-xs font-black text-zinc-700">{{ permission.roles_count }} roles</span>
                            <button
                                class="rounded-md border border-zinc-300 p-2 disabled:opacity-40"
                                :disabled="permission.roles_count > 0"
                                @click="router.delete(route('access.permissions.destroy', permission.id), { preserveScroll: true })"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </article>
                    </div>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
