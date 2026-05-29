<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { PartyPopper } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Stage = {
    key: string;
    name: string;
    description: string | null;
    color: string;
    sort_order: number;
};

type RequestItem = {
    id: number;
    reference: string;
    title: string;
    event_type: string;
    desired_date: string | null;
    location: string | null;
    stage_key: string;
    created_at: string;
    client?: { id: number; name: string; email: string };
};

type ClientOption = { id: number; name: string; email: string };

const props = defineProps<{
    stages: Stage[];
    requests: RequestItem[];
    canManage: boolean;
    canViewAssigned?: boolean;
    canCreate: boolean;
    eventTypes: Record<string, string>;
    clients: ClientOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: props.canManage ? 'Solicitudes de eventos' : 'Mis eventos', href: '/event-requests' }];

const createForm = useForm({
    title: '',
    event_type: 'corporativo',
    desired_date: '',
    location: '',
    description: '',
    guest_count: '' as string | number,
    budget_notes: '',
    client_user_id: props.clients[0]?.id ?? '',
});

const draggingId = ref<number | null>(null);

const requestsByStage = computed(() => {
    const map: Record<string, RequestItem[]> = {};

    for (const stage of props.stages) {
        map[stage.key] = [];
    }

    for (const request of props.requests) {
        if (!map[request.stage_key]) {
            map[request.stage_key] = [];
        }

        map[request.stage_key].push(request);
    }

    return map;
});

const stageName = (key: string) => props.stages.find((stage) => stage.key === key)?.name ?? key;

const onDragStart = (id: number) => {
    draggingId.value = id;
};

const onDrop = (stageKey: string) => {
    if (!props.canManage || draggingId.value === null) {
        return;
    }

    router.patch(
        route('event-requests.stage.update', draggingId.value),
        { stage_key: stageKey, position: requestsByStage.value[stageKey]?.length ?? 0 },
        { preserveScroll: true },
    );

    draggingId.value = null;
};
</script>

<template>
    <Head :title="canManage ? 'Solicitudes de eventos' : 'Mis eventos'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">Produccion de eventos</p>
                <h1 class="mt-2 text-2xl font-black sm:text-3xl">
                    {{ canManage ? 'Tablero de solicitudes' : canViewAssigned ? 'Eventos con tareas asignadas' : 'Seguimiento de mis eventos' }}
                </h1>
                <p class="mt-3 max-w-3xl text-sm text-zinc-300">
                    {{
                        canManage
                            ? 'Organiza cada solicitud por etapa. Arrastra las tarjetas entre columnas para actualizar el estado.'
                            : canViewAssigned
                              ? 'Entra a cada solicitud para actualizar el estado de las tareas que te asignaron.'
                              : 'Crea una solicitud y consulta en que etapa va tu evento, las tareas pendientes y las actualizaciones del equipo.'
                    }}
                </p>
            </section>

            <section v-if="canCreate" class="mt-6 rounded-md border border-zinc-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-center gap-3">
                    <PartyPopper class="h-6 w-6 text-[#a8322b]" />
                    <h2 class="text-xl font-black">Nueva solicitud de evento</h2>
                </div>
                <form
                    class="mt-5 grid gap-4 md:grid-cols-2"
                    @submit.prevent="
                        createForm.post(route('event-requests.store'), {
                            preserveScroll: true,
                            onSuccess: () => createForm.reset('title', 'description', 'location', 'budget_notes', 'guest_count', 'desired_date'),
                        })
                    "
                >
                    <label v-if="canManage" class="grid gap-2 text-sm font-bold md:col-span-2">
                        Cliente
                        <select v-model="createForm.client_user_id" class="rounded-md border border-zinc-300 px-3 py-3 font-normal" required>
                            <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }} · {{ client.email }}</option>
                        </select>
                    </label>
                    <label class="grid gap-2 text-sm font-bold md:col-span-2">
                        Titulo del evento
                        <input v-model="createForm.title" type="text" class="rounded-md border border-zinc-300 px-3 py-3 font-normal" required />
                    </label>
                    <label class="grid gap-2 text-sm font-bold">
                        Tipo
                        <select v-model="createForm.event_type" class="rounded-md border border-zinc-300 px-3 py-3 font-normal">
                            <option v-for="(label, value) in eventTypes" :key="value" :value="value">{{ label }}</option>
                        </select>
                    </label>
                    <label class="grid gap-2 text-sm font-bold">
                        Fecha deseada
                        <input v-model="createForm.desired_date" type="date" class="rounded-md border border-zinc-300 px-3 py-3 font-normal" />
                    </label>
                    <label class="grid gap-2 text-sm font-bold">
                        Lugar
                        <input v-model="createForm.location" type="text" class="rounded-md border border-zinc-300 px-3 py-3 font-normal" />
                    </label>
                    <label class="grid gap-2 text-sm font-bold">
                        Asistentes estimados
                        <input
                            v-model="createForm.guest_count"
                            type="number"
                            min="1"
                            class="rounded-md border border-zinc-300 px-3 py-3 font-normal"
                        />
                    </label>
                    <label class="grid gap-2 text-sm font-bold md:col-span-2">
                        Descripcion
                        <textarea
                            v-model="createForm.description"
                            rows="4"
                            class="rounded-md border border-zinc-300 px-3 py-3 font-normal"
                        ></textarea>
                    </label>
                    <label class="grid gap-2 text-sm font-bold md:col-span-2">
                        Presupuesto / notas
                        <input v-model="createForm.budget_notes" type="text" class="rounded-md border border-zinc-300 px-3 py-3 font-normal" />
                    </label>
                    <div class="md:col-span-2">
                        <button
                            class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white disabled:opacity-60"
                            :disabled="createForm.processing"
                        >
                            Enviar solicitud
                        </button>
                    </div>
                </form>
            </section>

            <section v-if="canManage" class="mt-6 overflow-x-auto pb-2">
                <div class="flex min-w-max gap-4">
                    <div
                        v-for="stage in stages"
                        :key="stage.key"
                        class="w-72 shrink-0 rounded-md border border-zinc-200 bg-zinc-100/80 p-3"
                        @dragover.prevent
                        @drop.prevent="onDrop(stage.key)"
                    >
                        <div class="mb-3 flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full" :style="{ backgroundColor: stage.color }"></span>
                            <h3 class="text-sm font-black uppercase tracking-wide text-zinc-800">{{ stage.name }}</h3>
                            <span class="ml-auto rounded-full bg-white px-2 py-0.5 text-xs font-bold text-zinc-600">
                                {{ requestsByStage[stage.key]?.length ?? 0 }}
                            </span>
                        </div>
                        <div class="grid gap-3">
                            <Link
                                v-for="item in requestsByStage[stage.key] ?? []"
                                :key="item.id"
                                :href="route('event-requests.show', item.id)"
                                draggable="true"
                                class="block cursor-grab rounded-md border border-zinc-200 bg-white p-3 shadow-sm transition hover:border-[#d7a097] active:cursor-grabbing"
                                @dragstart="onDragStart(item.id)"
                            >
                                <p class="text-xs font-bold text-[#a8322b]">{{ item.reference }}</p>
                                <h4 class="mt-1 font-black text-zinc-900">{{ item.title }}</h4>
                                <p v-if="item.client" class="mt-2 text-xs font-semibold text-zinc-500">{{ item.client.name }}</p>
                                <p class="mt-2 text-xs text-zinc-500">{{ eventTypes[item.event_type] ?? item.event_type }}</p>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <section v-else class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <Link
                    v-for="item in requests"
                    :key="item.id"
                    :href="route('event-requests.show', item.id)"
                    class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-[#d7a097] hover:shadow-md"
                >
                    <p class="text-xs font-bold text-[#a8322b]">{{ item.reference }}</p>
                    <h3 class="mt-1 text-lg font-black">{{ item.title }}</h3>
                    <p class="mt-3 inline-flex rounded-full bg-[#fff1ee] px-3 py-1 text-xs font-black text-[#a8322b]">
                        {{ stageName(item.stage_key) }}
                    </p>
                    <p v-if="item.desired_date" class="mt-3 text-sm text-zinc-600">Fecha deseada: {{ item.desired_date }}</p>
                </Link>
                <p
                    v-if="requests.length === 0"
                    class="rounded-md border border-dashed border-zinc-300 bg-white p-8 text-center text-zinc-500 md:col-span-2 xl:col-span-3"
                >
                    Aun no tienes solicitudes. Usa el formulario de arriba para crear la primera.
                </p>
            </section>
        </main>
    </AppLayout>
</template>
