<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Settings2 } from 'lucide-vue-next';

type StageRow = {
    id: number;
    key: string;
    name: string;
    description: string | null;
    color: string;
    sort_order: number;
    is_terminal: boolean;
    visible_to_client: boolean;
    requests_count: number;
};

const props = defineProps<{ stages: StageRow[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Solicitudes de eventos', href: '/event-requests' },
    { title: 'Etapas', href: '/admin/event-request-stages' },
];

const createForm = useForm({
    name: '',
    description: '',
    color: '#a8322b',
    sort_order: props.stages.length + 1,
    is_terminal: false,
    visible_to_client: true,
});

const editForms = props.stages.reduce(
    (carry, stage) => ({
        ...carry,
        [stage.id]: useForm({
            name: stage.name,
            description: stage.description ?? '',
            color: stage.color,
            sort_order: stage.sort_order,
            is_terminal: stage.is_terminal,
            visible_to_client: stage.visible_to_client,
        }),
    }),
    {} as Record<number, ReturnType<typeof useForm>>,
);
</script>

<template>
    <Head title="Etapas de solicitudes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">Configuracion</p>
                <h1 class="mt-2 text-2xl font-black sm:text-3xl">Etapas del flujo de eventos</h1>
                <p class="mt-3 max-w-3xl text-sm text-zinc-300">Renombra, reordena y define etapas visibles para el cliente. La clave interna no cambia para proteger solicitudes existentes.</p>
            </section>

            <section class="mt-6 rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <Settings2 class="h-6 w-6 text-[#a8322b]" />
                    <h2 class="text-xl font-black">Nueva etapa</h2>
                </div>
                <form
                    class="mt-5 grid gap-4 md:grid-cols-2"
                    @submit.prevent="createForm.post(route('event-request-stages.store'), { preserveScroll: true, onSuccess: () => createForm.reset('name', 'description') })"
                >
                    <label class="grid gap-2 text-sm font-bold md:col-span-2">
                        Nombre visible
                        <input v-model="createForm.name" class="rounded-md border border-zinc-300 px-3 py-3 font-normal" required />
                    </label>
                    <label class="grid gap-2 text-sm font-bold md:col-span-2">
                        Descripcion
                        <textarea v-model="createForm.description" rows="2" class="rounded-md border border-zinc-300 px-3 py-3 font-normal"></textarea>
                    </label>
                    <label class="grid gap-2 text-sm font-bold">
                        Color
                        <input v-model="createForm.color" type="color" class="h-12 w-full rounded-md border border-zinc-300" />
                    </label>
                    <label class="grid gap-2 text-sm font-bold">
                        Orden
                        <input v-model.number="createForm.sort_order" type="number" min="0" class="rounded-md border border-zinc-300 px-3 py-3 font-normal" />
                    </label>
                    <label class="flex items-center gap-2 text-sm font-bold">
                        <input v-model="createForm.is_terminal" type="checkbox" class="rounded border-zinc-300" />
                        Etapa final (cierra solicitud)
                    </label>
                    <label class="flex items-center gap-2 text-sm font-bold">
                        <input v-model="createForm.visible_to_client" type="checkbox" class="rounded border-zinc-300" />
                        Visible al cliente
                    </label>
                    <div class="md:col-span-2">
                        <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white" :disabled="createForm.processing">Crear etapa</button>
                    </div>
                </form>
            </section>

            <section class="mt-6 grid gap-4">
                <article v-for="stage in stages" :key="stage.id" class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase text-zinc-500">Clave: {{ stage.key }}</p>
                            <h3 class="mt-1 text-lg font-black">{{ stage.name }}</h3>
                            <p class="mt-1 text-sm text-zinc-600">{{ stage.requests_count }} solicitud(es) en esta etapa</p>
                        </div>
                        <span class="h-8 w-8 rounded-full border border-zinc-200" :style="{ backgroundColor: stage.color }"></span>
                    </div>
                    <form
                        class="mt-4 grid gap-4 md:grid-cols-2"
                        @submit.prevent="editForms[stage.id].patch(route('event-request-stages.update', stage.id), { preserveScroll: true })"
                    >
                        <label class="grid gap-2 text-sm font-bold md:col-span-2">
                            Nombre
                            <input v-model="editForms[stage.id].name" class="rounded-md border border-zinc-300 px-3 py-3 font-normal" required />
                        </label>
                        <label class="grid gap-2 text-sm font-bold md:col-span-2">
                            Descripcion
                            <textarea v-model="editForms[stage.id].description" rows="2" class="rounded-md border border-zinc-300 px-3 py-3 font-normal"></textarea>
                        </label>
                        <label class="grid gap-2 text-sm font-bold">
                            Color
                            <input v-model="editForms[stage.id].color" type="color" class="h-12 w-full rounded-md border border-zinc-300" />
                        </label>
                        <label class="grid gap-2 text-sm font-bold">
                            Orden
                            <input v-model.number="editForms[stage.id].sort_order" type="number" min="0" class="rounded-md border border-zinc-300 px-3 py-3 font-normal" />
                        </label>
                        <label class="flex items-center gap-2 text-sm font-bold">
                            <input v-model="editForms[stage.id].is_terminal" type="checkbox" class="rounded border-zinc-300" />
                            Etapa final
                        </label>
                        <label class="flex items-center gap-2 text-sm font-bold">
                            <input v-model="editForms[stage.id].visible_to_client" type="checkbox" class="rounded border-zinc-300" />
                            Visible al cliente
                        </label>
                        <div class="flex flex-wrap gap-3 md:col-span-2">
                            <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white" :disabled="editForms[stage.id].processing">Guardar</button>
                            <button
                                v-if="stage.requests_count === 0"
                                type="button"
                                class="rounded-md border border-red-300 px-5 py-3 text-sm font-black text-red-700"
                                @click="editForms[stage.id].delete(route('event-request-stages.destroy', stage.id), { preserveScroll: true })"
                            >
                                Eliminar
                            </button>
                        </div>
                    </form>
                </article>
            </section>
        </main>
    </AppLayout>
</template>
