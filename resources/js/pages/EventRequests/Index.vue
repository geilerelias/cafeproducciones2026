<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { PartyPopper, Plus, Search, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

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
type IdentificationOption = { value: string; label: string };

const props = defineProps<{
    stages: Stage[];
    requests: RequestItem[];
    canManage: boolean;
    canViewAssigned?: boolean;
    canCreate: boolean;
    eventTypes: Record<string, string>;
    identificationTypes: IdentificationOption[];
    clients: ClientOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: props.canManage ? 'Solicitudes de eventos' : 'Mis eventos', href: '/event-requests' }];
const page = usePage<SharedData>();

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

const clientSearch = ref(props.clients[0] ? `${props.clients[0].name} - ${props.clients[0].email}` : '');
const showClientOptions = ref(false);
const clientModalOpen = ref(false);

const clientForm = useForm({
    name: '',
    identification_type: 'cc',
    identification_number: '',
    phone: '',
    email: '',
});

const draggingId = ref<number | null>(null);

const filteredClients = computed(() => {
    const term = clientSearch.value.trim().toLowerCase();

    if (!term) {
        return props.clients.slice(0, 8);
    }

    return props.clients.filter((client) => `${client.name} ${client.email}`.toLowerCase().includes(term)).slice(0, 8);
});

const selectedClient = computed(() => props.clients.find((client) => client.id === Number(createForm.client_user_id)));

const selectClient = (client: ClientOption) => {
    createForm.client_user_id = client.id;
    clientSearch.value = `${client.name} - ${client.email}`;
    showClientOptions.value = false;
};

const submitClient = () => {
    clientForm.post(route('event-requests.clients.store'), {
        preserveScroll: true,
        onSuccess: () => {
            clientModalOpen.value = false;
            clientForm.reset();
        },
    });
};

watch(
    () => page.props.flash?.created_client_id,
    (clientId) => {
        if (!clientId) return;

        const client = props.clients.find((item) => item.id === Number(clientId));

        if (client) {
            selectClient(client);
        }
    },
);

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
        <main class="min-h-screen w-full min-w-0 max-w-full overflow-x-clip bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="w-full min-w-0 max-w-full overflow-hidden rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
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

            <section v-if="canCreate" class="mt-6 max-w-full overflow-hidden rounded-md border border-zinc-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-center gap-3">
                    <PartyPopper class="h-6 w-6 shrink-0 text-[#a8322b]" />
                    <h2 class="min-w-0 text-lg font-black sm:text-xl">Nueva solicitud de evento</h2>
                </div>
                <form
                    class="event-request-form mt-5 grid w-full min-w-0 max-w-full grid-cols-1 gap-4 sm:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)]"
                    @submit.prevent="
                        createForm.post(route('event-requests.store'), {
                            preserveScroll: true,
                            onSuccess: () => createForm.reset('title', 'description', 'location', 'budget_notes', 'guest_count', 'desired_date'),
                        })
                    "
                >
                    <div v-if="canManage" class="grid min-w-0 max-w-full gap-2 text-sm font-bold sm:col-span-2 xl:col-span-4">
                        Cliente
                        <div class="grid min-w-0 max-w-full gap-2 sm:grid-cols-[minmax(0,1fr)_auto]">
                            <div class="relative min-w-0 max-w-full">
                                <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                                <input
                                    v-model="clientSearch"
                                    type="search"
                                    class="block w-full min-w-0 max-w-full rounded-md border border-zinc-300 py-3 pl-10 pr-3 font-normal"
                                    placeholder="Buscar cliente por nombre o correo"
                                    required
                                    @focus="showClientOptions = true"
                                    @input="showClientOptions = true"
                                />
                                <input v-model="createForm.client_user_id" type="hidden" />
                                <div
                                    v-if="showClientOptions"
                                    class="absolute z-20 mt-2 max-h-64 w-full min-w-0 overflow-y-auto rounded-md border border-zinc-200 bg-white p-1 shadow-xl"
                                >
                                    <button
                                        v-for="client in filteredClients"
                                        :key="client.id"
                                        type="button"
                                        class="block w-full min-w-0 rounded px-3 py-2 text-left text-sm font-semibold hover:bg-zinc-100"
                                        @click="selectClient(client)"
                                    >
                                        <span class="block truncate text-zinc-950">{{ client.name }}</span>
                                        <span class="block truncate text-xs text-zinc-500">{{ client.email }}</span>
                                    </button>
                                    <p v-if="filteredClients.length === 0" class="px-3 py-3 text-sm font-semibold text-zinc-500">
                                        No se encontraron clientes.
                                    </p>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-[#f0c8be] bg-[#fff7f5] px-4 py-3 text-sm font-black text-[#a8322b] transition hover:bg-[#fff1ee] sm:w-auto"
                                @click="clientModalOpen = true"
                            >
                                <Plus class="h-4 w-4" />
                                Añadir
                            </button>
                        </div>
                        <p v-if="selectedClient" class="text-xs font-semibold text-zinc-500">
                            Cliente seleccionado: {{ selectedClient.name }} - {{ selectedClient.email }}
                        </p>
                        <select v-model="createForm.client_user_id" class="hidden" required>
                            <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }} · {{ client.email }}</option>
                        </select>
                    </div>
                    <label class="grid min-w-0 max-w-full gap-2 text-sm font-bold sm:col-span-2 xl:col-span-4">
                        Titulo del evento
                        <input
                            v-model="createForm.title"
                            type="text"
                            class="block w-full min-w-0 max-w-full rounded-md border border-zinc-300 px-3 py-3 font-normal"
                            required
                        />
                    </label>
                    <label class="grid min-w-0 max-w-full gap-2 text-sm font-bold">
                        Tipo
                        <select
                            v-model="createForm.event_type"
                            class="block w-full min-w-0 max-w-full rounded-md border border-zinc-300 px-3 py-3 font-normal"
                        >
                            <option v-for="(label, value) in eventTypes" :key="value" :value="value">{{ label }}</option>
                        </select>
                    </label>
                    <label class="grid min-w-0 max-w-full gap-2 text-sm font-bold">
                        Fecha deseada
                        <input
                            v-model="createForm.desired_date"
                            type="date"
                            class="block w-full min-w-0 max-w-full rounded-md border border-zinc-300 px-3 py-3 font-normal"
                        />
                    </label>
                    <label class="grid min-w-0 max-w-full gap-2 text-sm font-bold">
                        Lugar
                        <input
                            v-model="createForm.location"
                            type="text"
                            class="block w-full min-w-0 max-w-full rounded-md border border-zinc-300 px-3 py-3 font-normal"
                        />
                    </label>
                    <label class="grid min-w-0 max-w-full gap-2 text-sm font-bold">
                        Asistentes estimados
                        <input
                            v-model="createForm.guest_count"
                            type="number"
                            min="1"
                            class="block w-full min-w-0 max-w-full rounded-md border border-zinc-300 px-3 py-3 font-normal"
                        />
                    </label>
                    <label class="grid min-w-0 max-w-full gap-2 text-sm font-bold sm:col-span-2 xl:col-span-4">
                        Descripcion
                        <textarea
                            v-model="createForm.description"
                            rows="4"
                            class="block w-full min-w-0 max-w-full resize-y rounded-md border border-zinc-300 px-3 py-3 font-normal"
                        ></textarea>
                    </label>
                    <label class="grid min-w-0 max-w-full gap-2 text-sm font-bold sm:col-span-2 xl:col-span-4">
                        Presupuesto / notas
                        <input
                            v-model="createForm.budget_notes"
                            type="text"
                            class="block w-full min-w-0 max-w-full rounded-md border border-zinc-300 px-3 py-3 font-normal"
                        />
                    </label>
                    <div class="min-w-0 max-w-full sm:col-span-2 xl:col-span-4">
                        <button
                            class="w-full rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white disabled:opacity-60 sm:w-auto"
                            :disabled="createForm.processing"
                        >
                            Enviar solicitud
                        </button>
                    </div>
                </form>
            </section>

            <div v-if="clientModalOpen" class="fixed inset-0 z-50 grid place-items-center overflow-y-auto bg-black/70 p-4">
                <form class="w-full max-w-xl rounded-md bg-white p-5 shadow-2xl" @submit.prevent="submitClient">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-black text-zinc-950">Registrar cliente</h3>
                            <p class="mt-1 text-sm font-semibold text-zinc-500">Crea el cliente y quedara disponible para esta solicitud.</p>
                        </div>
                        <button type="button" class="rounded-md p-2 text-zinc-500 hover:bg-zinc-100" @click="clientModalOpen = false">
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="mt-5 grid min-w-0 gap-4 sm:grid-cols-2">
                        <label class="grid min-w-0 gap-2 text-sm font-bold sm:col-span-2">
                            Nombre completo
                            <input
                                v-model="clientForm.name"
                                class="block w-full min-w-0 rounded-md border border-zinc-300 px-3 py-3 font-normal"
                                required
                            />
                            <span v-if="clientForm.errors.name" class="text-xs font-bold text-[#a8322b]">{{ clientForm.errors.name }}</span>
                        </label>
                        <label class="grid min-w-0 gap-2 text-sm font-bold">
                            Tipo de identificacion
                            <select
                                v-model="clientForm.identification_type"
                                class="block w-full min-w-0 rounded-md border border-zinc-300 px-3 py-3 font-normal"
                                required
                            >
                                <option v-for="option in identificationTypes" :key="option.value" :value="option.value">{{ option.label }}</option>
                            </select>
                            <span v-if="clientForm.errors.identification_type" class="text-xs font-bold text-[#a8322b]">{{
                                clientForm.errors.identification_type
                            }}</span>
                        </label>
                        <label class="grid min-w-0 gap-2 text-sm font-bold">
                            Numero de identificacion
                            <input
                                v-model="clientForm.identification_number"
                                class="block w-full min-w-0 rounded-md border border-zinc-300 px-3 py-3 font-normal"
                                required
                            />
                            <span v-if="clientForm.errors.identification_number" class="text-xs font-bold text-[#a8322b]">{{
                                clientForm.errors.identification_number
                            }}</span>
                        </label>
                        <label class="grid min-w-0 gap-2 text-sm font-bold">
                            Telefono
                            <input
                                v-model="clientForm.phone"
                                type="tel"
                                class="block w-full min-w-0 rounded-md border border-zinc-300 px-3 py-3 font-normal"
                                required
                            />
                            <span v-if="clientForm.errors.phone" class="text-xs font-bold text-[#a8322b]">{{ clientForm.errors.phone }}</span>
                        </label>
                        <label class="grid min-w-0 gap-2 text-sm font-bold">
                            Correo
                            <input
                                v-model="clientForm.email"
                                type="email"
                                class="block w-full min-w-0 rounded-md border border-zinc-300 px-3 py-3 font-normal"
                                required
                            />
                            <span v-if="clientForm.errors.email" class="text-xs font-bold text-[#a8322b]">{{ clientForm.errors.email }}</span>
                        </label>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" class="rounded-md border border-zinc-300 px-5 py-3 text-sm font-black" @click="clientModalOpen = false">
                            Cancelar
                        </button>
                        <button
                            class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white disabled:opacity-60"
                            :disabled="clientForm.processing"
                        >
                            Guardar cliente
                        </button>
                    </div>
                </form>
            </div>

            <section v-if="canManage" class="mt-6 w-full">
                <div class="grid gap-4 [grid-template-columns:repeat(auto-fit,minmax(260px,1fr))]">
                    <div
                        v-for="stage in stages"
                        :key="stage.key"
                        class="rounded-md border border-zinc-200 bg-zinc-100/80 p-3"
                        @dragover.prevent
                        @drop.prevent="onDrop(stage.key)"
                    >
                        <div class="mb-3 flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full" :style="{ backgroundColor: stage.color }"></span>
                            <h3 class="text-sm font-black uppercase tracking-wide text-zinc-800">
                                {{ stage.name }}
                            </h3>
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

                                <p v-if="item.client" class="mt-2 text-xs font-semibold text-zinc-500">
                                    {{ item.client.name }}
                                </p>

                                <p class="mt-2 text-xs text-zinc-500">
                                    {{ eventTypes[item.event_type] ?? item.event_type }}
                                </p>
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

<style scoped>
.event-request-form,
.event-request-form * {
    box-sizing: border-box;
}

.event-request-form :deep(input),
.event-request-form :deep(select),
.event-request-form :deep(textarea) {
    max-width: 100%;
    min-width: 0;
}
</style>
