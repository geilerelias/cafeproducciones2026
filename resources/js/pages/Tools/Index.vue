<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Wrench } from 'lucide-vue-next';

type ToolItem = {
    id: number;
    name: string;
    code: string | null;
    status: string;
    notes: string | null;
    assignments: Array<{ id: number; status: string; user?: { name: string }; event?: { name: string } }>;
};

defineProps<{ tools: ToolItem[] }>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Herramientas', href: '/tools' }];
const form = useForm({ name: '', code: '', notes: '' });
</script>

<template>
    <Head title="Herramientas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">Inventario</p>
                <h1 class="mt-2 text-2xl font-black sm:text-3xl">Herramientas asignables</h1>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[0.65fr_1.35fr]">
                <form class="rounded-md border border-zinc-200 bg-white p-4 shadow-sm sm:p-5" @submit.prevent="form.post(route('tools.store'), { preserveScroll: true, onSuccess: () => form.reset() })">
                    <div class="flex items-center gap-3">
                        <Wrench class="h-6 w-6 text-[#a8322b]" />
                        <h2 class="text-xl font-black">Nueva herramienta</h2>
                    </div>
                    <div class="mt-5 grid gap-4">
                        <input v-model="form.name" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Nombre" />
                        <input v-model="form.code" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Codigo interno" />
                        <textarea v-model="form.notes" rows="4" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Notas"></textarea>
                        <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white">Crear herramienta</button>
                    </div>
                </form>

                <div class="grid gap-4">
                    <article v-for="tool in tools" :key="tool.id" class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div>
                                <h3 class="font-black">{{ tool.name }}</h3>
                                <p class="mt-1 text-sm font-semibold text-zinc-500">{{ tool.code || 'Sin codigo' }}</p>
                                <p class="mt-2 text-sm text-zinc-600">{{ tool.notes || 'Sin notas.' }}</p>
                            </div>
                            <span class="rounded-md bg-zinc-100 px-3 py-1 text-xs font-black text-zinc-700">{{ tool.status }}</span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span v-for="assignment in tool.assignments" :key="assignment.id" class="rounded-md bg-[#fff1ee] px-3 py-1 text-xs font-bold text-[#7f241f]">
                                {{ assignment.user?.name }} · {{ assignment.event?.name || 'sin evento' }} · {{ assignment.status }}
                            </span>
                        </div>
                    </article>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
