<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

type Stage = {
    key: string;
    name: string;
    description: string | null;
    color: string;
    sort_order: number;
    is_terminal?: boolean;
    visible_to_client?: boolean;
};

type Task = {
    id: number;
    title: string;
    description: string | null;
    status: string;
    visible_to_client: boolean;
    due_date: string | null;
    completed_at: string | null;
    assignee?: { name: string };
};

type Attachment = {
    id: number;
    label: string;
    original_name: string;
    size: number;
    visible_to_client: boolean;
    uploader?: { name: string };
};

type Activity = {
    id: number;
    type: string;
    body: string;
    created_at: string;
    user?: { name: string };
};

type EventRequestDetail = {
    id: number;
    reference: string;
    title: string;
    event_type: string;
    desired_date: string | null;
    location: string | null;
    description: string | null;
    guest_count: number | null;
    budget_notes: string | null;
    stage_key: string;
    client_message: string | null;
    internal_notes: string | null;
    client_user_id: number;
    client?: { name: string; email: string; phone?: string };
    tasks?: Task[];
    activities?: Activity[];
    attachments?: Attachment[];
};

type ClientOption = { id: number; name: string; email: string };
type AssigneeOption = { id: number; name: string; email: string };

const props = defineProps<{
    eventRequest: EventRequestDetail;
    stages: Stage[];
    canManage: boolean;
    canViewAssigned?: boolean;
    canUploadAttachments?: boolean;
    eventTypes: Record<string, string>;
    taskStatuses: Record<string, string>;
    attachmentLabels: Record<string, string>;
    clients: ClientOption[];
    assignees?: AssigneeOption[];
}>();

const assigneeOptions = computed(() => props.assignees ?? []);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.canManage ? 'Solicitudes de eventos' : props.canViewAssigned ? 'Tareas de eventos' : 'Mis eventos',
        href: '/event-requests',
    },
    { title: props.eventRequest.reference, href: route('event-requests.show', props.eventRequest.id) },
];

const visibleStages = computed(() =>
    props.stages.filter(
        (stage) =>
            props.canManage || (stage.visible_to_client !== false && (stage.key !== 'rechazada' || props.eventRequest.stage_key === 'rechazada')),
    ),
);

const canUpdateTasks = computed(() => props.canManage || props.canViewAssigned);

const currentStageIndex = computed(() => visibleStages.value.findIndex((stage) => stage.key === props.eventRequest.stage_key));

const tasksByStatus = computed(() => {
    const groups: Record<string, Task[]> = { pendiente: [], en_progreso: [], completada: [], bloqueada: [] };

    for (const task of props.eventRequest.tasks ?? []) {
        if (!groups[task.status]) {
            groups[task.status] = [];
        }

        groups[task.status].push(task);
    }

    return groups;
});

const pendingCount = computed(() => (props.eventRequest.tasks ?? []).filter((task) => task.status !== 'completada').length);

const adminForm = useForm({
    stage_key: props.eventRequest.stage_key,
    client_user_id: props.eventRequest.client_user_id,
    client_message: props.eventRequest.client_message ?? '',
    internal_notes: props.eventRequest.internal_notes ?? '',
});

const taskForm = useForm({
    title: '',
    description: '',
    status: 'pendiente',
    visible_to_client: true,
    due_date: '',
    assigned_to: '' as string | number,
});

const attachmentForm = useForm<{
    file: File | null;
    label: string;
    visible_to_client: boolean;
}>({
    file: null,
    label: 'brief',
    visible_to_client: true,
});

const onAttachmentSelected = (event: Event) => {
    const input = event.target as HTMLInputElement;
    attachmentForm.file = input.files?.[0] ?? null;
};

const submitAttachment = () => {
    if (!attachmentForm.file) {
        return;
    }

    attachmentForm.post(route('event-requests.attachments.store', props.eventRequest.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            attachmentForm.reset();
            attachmentForm.label = 'brief';
            attachmentForm.visible_to_client = true;
        },
    });
};

const formatBytes = (bytes: number) => {
    if (bytes < 1024) {
        return `${bytes} B`;
    }

    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }

    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
};

const commentForm = useForm({
    body: '',
    visible_to_client: true,
});

const stageName = (key: string) => props.stages.find((stage) => stage.key === key)?.name ?? key;

const updateTaskStatus = (taskId: number, status: string) => {
    router.patch(route('event-requests.tasks.update', [props.eventRequest.id, taskId]), { status }, { preserveScroll: true });
};

const tasksForColumn = (column: string) => {
    if (column === 'pendiente') {
        return [...(tasksByStatus.value.pendiente ?? []), ...(tasksByStatus.value.bloqueada ?? [])];
    }

    return tasksByStatus.value[column] ?? [];
};
</script>

<template>
    <Head :title="`${eventRequest.reference} | Seguimiento`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">{{ eventRequest.reference }}</p>
                <h1 class="mt-2 text-2xl font-black sm:text-3xl">{{ eventRequest.title }}</h1>
                <p class="mt-2 text-sm text-zinc-300">
                    {{ eventTypes[eventRequest.event_type] ?? eventRequest.event_type }}
                    <span v-if="eventRequest.client"> · {{ eventRequest.client.name }}</span>
                </p>
                <p class="mt-4 inline-flex rounded-full bg-[#a8322b] px-4 py-1.5 text-sm font-black">{{ stageName(eventRequest.stage_key) }}</p>
                <p v-if="!canManage && pendingCount > 0" class="mt-3 text-sm text-[#f0c8be]">
                    {{ canViewAssigned ? 'Tareas asignadas a ti' : '' }} · {{ pendingCount }} pendiente(s)
                </p>
            </section>

            <section class="mt-6 rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-black">Etapas del proceso</h2>
                <ol class="mt-5 grid gap-3 md:grid-cols-5">
                    <li
                        v-for="(stage, index) in visibleStages.filter((s) => !s.is_terminal || s.key === eventRequest.stage_key)"
                        :key="stage.key"
                        class="rounded-md border p-3"
                        :class="index <= currentStageIndex ? 'border-[#a8322b] bg-[#fff1ee]' : 'border-zinc-200 bg-zinc-50 opacity-70'"
                    >
                        <p class="text-xs font-black uppercase text-[#a8322b]">Paso {{ index + 1 }}</p>
                        <p class="mt-1 font-black text-zinc-900">{{ stage.name }}</p>
                        <p v-if="stage.description" class="mt-1 text-xs text-zinc-600">{{ stage.description }}</p>
                    </li>
                </ol>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[1fr_1.2fr]">
                <div class="grid gap-6">
                    <article class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-black">Detalle</h2>
                        <dl class="mt-4 grid gap-3 text-sm">
                            <div v-if="eventRequest.desired_date">
                                <dt class="font-bold text-zinc-500">Fecha deseada</dt>
                                <dd>{{ eventRequest.desired_date }}</dd>
                            </div>
                            <div v-if="eventRequest.location">
                                <dt class="font-bold text-zinc-500">Lugar</dt>
                                <dd>{{ eventRequest.location }}</dd>
                            </div>
                            <div v-if="eventRequest.guest_count">
                                <dt class="font-bold text-zinc-500">Asistentes</dt>
                                <dd>{{ eventRequest.guest_count }}</dd>
                            </div>
                            <div v-if="eventRequest.description">
                                <dt class="font-bold text-zinc-500">Descripcion</dt>
                                <dd class="whitespace-pre-wrap">{{ eventRequest.description }}</dd>
                            </div>
                            <div v-if="eventRequest.budget_notes">
                                <dt class="font-bold text-zinc-500">Presupuesto</dt>
                                <dd>{{ eventRequest.budget_notes }}</dd>
                            </div>
                        </dl>
                    </article>

                    <article class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-black">Documentos adjuntos</h2>
                        <p class="mt-1 text-sm text-zinc-500">Brief, cotizaciones y archivos de apoyo.</p>

                        <form
                            v-if="canUploadAttachments"
                            class="mt-4 grid gap-3 rounded-md border border-dashed border-zinc-300 p-4"
                            @submit.prevent="submitAttachment"
                        >
                            <label class="grid gap-2 text-sm font-bold">
                                Tipo de documento
                                <select v-model="attachmentForm.label" class="rounded-md border border-zinc-300 px-3 py-2 font-normal">
                                    <option v-for="(label, value) in attachmentLabels" :key="value" :value="value">{{ label }}</option>
                                </select>
                            </label>
                            <label class="grid gap-2 text-sm font-bold">
                                Archivo (max. 10 MB)
                                <input
                                    type="file"
                                    class="rounded-md border border-zinc-300 px-3 py-2 font-normal"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.webp,.zip"
                                    @change="onAttachmentSelected"
                                />
                            </label>
                            <label v-if="canManage" class="flex items-center gap-2 text-sm font-semibold">
                                <input v-model="attachmentForm.visible_to_client" type="checkbox" class="rounded border-zinc-300" />
                                Visible al cliente
                            </label>
                            <button
                                class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-black text-white"
                                :disabled="attachmentForm.processing || !attachmentForm.file"
                            >
                                Subir archivo
                            </button>
                        </form>

                        <ul class="mt-4 grid gap-2">
                            <li
                                v-for="file in eventRequest.attachments ?? []"
                                :key="file.id"
                                class="flex flex-wrap items-center justify-between gap-3 rounded-md border border-zinc-200 px-3 py-3 text-sm"
                            >
                                <div>
                                    <p class="font-black">{{ attachmentLabels[file.label] ?? file.label }}</p>
                                    <p class="text-zinc-600">{{ file.original_name }} · {{ formatBytes(file.size) }}</p>
                                    <p v-if="file.uploader" class="text-xs text-zinc-500">Subido por {{ file.uploader.name }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <a
                                        :href="route('event-requests.attachments.download', [eventRequest.id, file.id])"
                                        class="rounded-md bg-[#fff1ee] px-3 py-2 text-xs font-black text-[#a8322b]"
                                        >Descargar</a
                                    >
                                    <button
                                        v-if="canManage || canUploadAttachments"
                                        type="button"
                                        class="rounded-md border border-red-200 px-3 py-2 text-xs font-black text-red-700"
                                        @click="
                                            router.delete(route('event-requests.attachments.destroy', [eventRequest.id, file.id]), {
                                                preserveScroll: true,
                                            })
                                        "
                                    >
                                        Eliminar
                                    </button>
                                </div>
                            </li>
                            <li v-if="!eventRequest.attachments?.length" class="text-sm text-zinc-500">No hay archivos adjuntos todavia.</li>
                        </ul>
                    </article>

                    <article v-if="eventRequest.client_message" class="rounded-md border border-[#d7a097] bg-[#fff1ee] p-5">
                        <h2 class="text-lg font-black text-[#a8322b]">Mensaje del equipo</h2>
                        <p class="mt-3 whitespace-pre-wrap text-sm text-zinc-800">{{ eventRequest.client_message }}</p>
                    </article>

                    <article v-if="canManage" class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-black">Gestion interna</h2>
                        <form
                            class="mt-4 grid gap-4"
                            @submit.prevent="adminForm.patch(route('event-requests.update', eventRequest.id), { preserveScroll: true })"
                        >
                            <label class="grid gap-2 text-sm font-bold">
                                Etapa
                                <select v-model="adminForm.stage_key" class="rounded-md border border-zinc-300 px-3 py-3 font-normal">
                                    <option v-for="stage in stages" :key="stage.key" :value="stage.key">{{ stage.name }}</option>
                                </select>
                            </label>
                            <label class="grid gap-2 text-sm font-bold">
                                Cliente asignado
                                <select v-model="adminForm.client_user_id" class="rounded-md border border-zinc-300 px-3 py-3 font-normal">
                                    <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                                </select>
                            </label>
                            <label class="grid gap-2 text-sm font-bold">
                                Mensaje visible al cliente
                                <textarea
                                    v-model="adminForm.client_message"
                                    rows="3"
                                    class="rounded-md border border-zinc-300 px-3 py-3 font-normal"
                                ></textarea>
                            </label>
                            <label class="grid gap-2 text-sm font-bold">
                                Notas internas
                                <textarea
                                    v-model="adminForm.internal_notes"
                                    rows="3"
                                    class="rounded-md border border-zinc-300 px-3 py-3 font-normal"
                                ></textarea>
                            </label>
                            <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white" :disabled="adminForm.processing">
                                Guardar cambios
                            </button>
                        </form>
                        <form
                            class="mt-6 border-t border-zinc-200 pt-6"
                            @submit.prevent="
                                commentForm.post(route('event-requests.comments.store', eventRequest.id), {
                                    preserveScroll: true,
                                    onSuccess: () => commentForm.reset('body'),
                                })
                            "
                        >
                            <h3 class="font-black">Publicar actualizacion</h3>
                            <textarea
                                v-model="commentForm.body"
                                rows="3"
                                class="mt-2 w-full rounded-md border border-zinc-300 px-3 py-3 text-sm"
                                required
                            ></textarea>
                            <label class="mt-2 flex items-center gap-2 text-sm font-semibold">
                                <input v-model="commentForm.visible_to_client" type="checkbox" class="rounded border-zinc-300" />
                                Visible para el cliente
                            </label>
                            <button class="mt-3 rounded-md bg-[#a8322b] px-4 py-2 text-sm font-black text-white" :disabled="commentForm.processing">
                                Publicar
                            </button>
                        </form>
                    </article>
                </div>

                <div class="grid gap-6">
                    <article class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-black">Tareas y pendientes</h2>
                        <p v-if="!canManage" class="mt-1 text-sm text-zinc-500">
                            {{
                                canViewAssigned ? 'Solo ves las tareas asignadas a ti.' : 'Actividades que el equipo tiene en marcha para tu evento.'
                            }}
                        </p>

                        <form
                            v-if="canManage"
                            class="mt-4 grid gap-3 rounded-md border border-dashed border-zinc-300 p-4"
                            @submit.prevent="
                                taskForm.post(route('event-requests.tasks.store', eventRequest.id), {
                                    preserveScroll: true,
                                    onSuccess: () => taskForm.reset('title', 'description', 'due_date'),
                                })
                            "
                        >
                            <input
                                v-model="taskForm.title"
                                type="text"
                                placeholder="Nueva tarea"
                                class="rounded-md border border-zinc-300 px-3 py-2 text-sm"
                                required
                            />
                            <div class="grid gap-3 sm:grid-cols-2">
                                <select v-model="taskForm.status" class="rounded-md border border-zinc-300 px-3 py-2 text-sm">
                                    <option v-for="(label, value) in taskStatuses" :key="value" :value="value">{{ label }}</option>
                                </select>
                                <input v-model="taskForm.due_date" type="date" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" />
                            </div>
                            <label class="grid gap-2 text-sm font-bold">
                                Responsable
                                <select v-model="taskForm.assigned_to" class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-normal">
                                    <option value="">Sin asignar</option>
                                    <option v-for="person in assigneeOptions" :key="person.id" :value="person.id">{{ person.name }}</option>
                                </select>
                            </label>
                            <label class="flex items-center gap-2 text-sm font-semibold">
                                <input v-model="taskForm.visible_to_client" type="checkbox" class="rounded border-zinc-300" />
                                Visible al cliente
                            </label>
                            <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-black text-white">Agregar tarea</button>
                        </form>

                        <div class="mt-5 grid gap-4 lg:grid-cols-3">
                            <div v-for="column in ['pendiente', 'en_progreso', 'completada']" :key="column" class="rounded-md bg-zinc-50 p-3">
                                <h3 class="text-xs font-black uppercase tracking-wide text-zinc-600">{{ taskStatuses[column] }}</h3>
                                <div class="mt-3 grid gap-2">
                                    <div
                                        v-for="task in tasksForColumn(column)"
                                        :key="task.id"
                                        class="rounded-md border border-zinc-200 bg-white p-3 text-sm"
                                    >
                                        <p class="font-black">{{ task.title }}</p>
                                        <p v-if="task.assignee" class="mt-1 text-xs font-semibold text-zinc-500">
                                            Asignado: {{ task.assignee.name }}
                                        </p>
                                        <p v-if="task.due_date" class="mt-1 text-xs text-zinc-500">Vence: {{ task.due_date }}</p>
                                        <select
                                            v-if="canUpdateTasks"
                                            :value="task.status"
                                            class="mt-2 w-full rounded border border-zinc-300 px-2 py-1 text-xs"
                                            @change="updateTaskStatus(task.id, ($event.target as HTMLSelectElement).value)"
                                        >
                                            <option v-for="(label, value) in taskStatuses" :key="value" :value="value">{{ label }}</option>
                                        </select>
                                    </div>
                                    <p v-if="!tasksForColumn(column).length" class="text-xs text-zinc-400">Sin tareas</p>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-black">Linea de tiempo</h2>
                        <ol class="mt-4 grid gap-4">
                            <li v-for="activity in eventRequest.activities ?? []" :key="activity.id" class="border-l-2 border-[#a8322b] pl-4">
                                <p class="text-xs font-bold text-zinc-500">{{ new Date(activity.created_at).toLocaleString('es-CO') }}</p>
                                <p class="mt-1 text-sm text-zinc-800">{{ activity.body }}</p>
                                <p v-if="activity.user" class="mt-1 text-xs text-zinc-500">{{ activity.user.name }}</p>
                            </li>
                            <li v-if="!eventRequest.activities?.length" class="text-sm text-zinc-500">Aun no hay actualizaciones registradas.</li>
                        </ol>
                    </article>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
