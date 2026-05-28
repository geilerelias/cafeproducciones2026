<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { BriefcaseBusiness, CircleDollarSign, UserPlus, Wrench } from 'lucide-vue-next';

type Employee = { id: number; name: string; email: string };
type Tool = { id: number; name: string; code: string | null; status: string };
type EventItem = {
    id: number;
    name: string;
    location: string | null;
    starts_at: string;
    ends_at: string | null;
    status: string;
    description: string | null;
    assignments_count: number;
    registered_count: number;
    assignments: Array<{
        id: number;
        user_id: number;
        task: string;
        payment_amount: string;
        payment_status: string;
        registered_at: string | null;
        user: Employee;
    }>;
    tool_assignments: Array<{
        id: number;
        status: string;
        responsibility_notes: string | null;
        user: Employee;
        tool: Tool;
    }>;
};

const props = defineProps<{
    events: EventItem[];
    employees: Employee[];
    tools: Tool[];
    canManage: boolean;
    canRegister: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Eventos', href: '/events' }];
const eventForm = useForm({ name: '', location: '', starts_at: '', ends_at: '', description: '' });

const assignmentForms = props.events.reduce(
    (carry, event) => ({
        ...carry,
        [event.id]: useForm({ user_id: props.employees[0]?.id ?? '', task: '', payment_amount: 0, payment_status: 'pendiente', notes: '' }),
    }),
    {} as Record<number, ReturnType<typeof useForm>>,
);

const toolForms = props.events.reduce(
    (carry, event) => ({
        ...carry,
        [event.id]: useForm({ tool_id: props.tools[0]?.id ?? '', user_id: props.employees[0]?.id ?? '', condition_out: '', responsibility_notes: '' }),
    }),
    {} as Record<number, ReturnType<typeof useForm>>,
);
</script>

<template>
    <Head title="Eventos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">Operacion</p>
                <h1 class="mt-2 text-2xl font-black sm:text-3xl">Eventos, empleados, pagos y herramientas</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-zinc-300">Los administradores ven todos los eventos; los empleados ven sus eventos asignados y confirman participacion.</p>
            </section>

            <form v-if="canManage" class="mt-6 rounded-md border border-zinc-200 bg-white p-4 shadow-sm sm:p-5" @submit.prevent="eventForm.post(route('events.store'), { preserveScroll: true, onSuccess: () => eventForm.reset() })">
                <div class="flex items-center gap-3">
                    <BriefcaseBusiness class="h-6 w-6 text-[#a8322b]" />
                    <h2 class="text-xl font-black">Crear evento</h2>
                </div>
                <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    <input v-model="eventForm.name" class="min-w-0 rounded-md border border-zinc-300 px-3 py-3" placeholder="Nombre del evento" />
                    <input v-model="eventForm.location" class="min-w-0 rounded-md border border-zinc-300 px-3 py-3" placeholder="Lugar" />
                    <input v-model="eventForm.starts_at" type="datetime-local" class="min-w-0 rounded-md border border-zinc-300 px-3 py-3" />
                    <input v-model="eventForm.ends_at" type="datetime-local" class="min-w-0 rounded-md border border-zinc-300 px-3 py-3" />
                    <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white sm:col-span-2 xl:col-span-1">Crear</button>
                    <textarea v-model="eventForm.description" rows="3" class="xl:col-span-5 rounded-md border border-zinc-300 px-3 py-3" placeholder="Descripcion"></textarea>
                </div>
            </form>

            <section class="mt-6 grid gap-5">
                <article v-for="event in events" :key="event.id" class="rounded-md border border-zinc-200 bg-white p-4 shadow-sm sm:p-5">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <h2 class="text-xl font-black">{{ event.name }}</h2>
                            <p class="mt-1 text-sm font-semibold text-zinc-500">{{ event.location || 'Sin lugar' }} · {{ event.starts_at }}</p>
                            <p class="mt-2 text-sm leading-6 text-zinc-600">{{ event.description || 'Sin descripcion.' }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-md bg-zinc-100 px-3 py-1 text-xs font-black text-zinc-700">{{ event.status }}</span>
                            <span class="rounded-md bg-[#fff1ee] px-3 py-1 text-xs font-black text-[#7f241f]">{{ event.registered_count }}/{{ event.assignments_count }} registrados</span>
                        </div>
                    </div>

                    <section v-if="canManage" class="mt-5 rounded-md border border-[#d7a097] bg-[#fff8f6] p-4">
                        <div class="mb-3 flex items-center gap-2">
                            <UserPlus class="h-5 w-5 text-[#a8322b]" />
                            <h3 class="font-black">Asignar trabajador al evento</h3>
                        </div>
                        <form v-if="employees.length" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_140px_150px_auto]" @submit.prevent="assignmentForms[event.id].post(route('events.employees.store', event.id), { preserveScroll: true, onSuccess: () => assignmentForms[event.id].reset('task', 'payment_amount', 'notes') })">
                            <select v-model="assignmentForms[event.id].user_id" class="min-w-0 rounded-md border border-zinc-300 bg-white px-3 py-3 text-sm">
                                <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.name }}</option>
                            </select>
                            <input v-model="assignmentForms[event.id].task" class="min-w-0 rounded-md border border-zinc-300 bg-white px-3 py-3 text-sm" placeholder="Tarea en el evento" />
                            <input v-model="assignmentForms[event.id].payment_amount" type="number" min="0" class="min-w-0 rounded-md border border-zinc-300 bg-white px-3 py-3 text-sm" placeholder="Pago" />
                            <select v-model="assignmentForms[event.id].payment_status" class="min-w-0 rounded-md border border-zinc-300 bg-white px-3 py-3 text-sm">
                                <option value="pendiente">pendiente</option>
                                <option value="aprobado">aprobado</option>
                                <option value="pagado">pagado</option>
                            </select>
                            <button class="rounded-md bg-zinc-950 px-4 py-3 text-sm font-black text-white sm:col-span-2 lg:col-span-1">Asignar</button>
                        </form>
                        <p v-else class="rounded-md bg-white p-3 text-sm font-bold text-zinc-600">No hay trabajadores registrados para asignar.</p>
                    </section>

                    <div class="mt-5 grid gap-4 xl:grid-cols-2">
                        <section class="rounded-md border border-zinc-200 p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <CircleDollarSign class="h-5 w-5 text-[#a8322b]" />
                                <h3 class="font-black">Empleados, tareas y pagos</h3>
                            </div>
                            <div class="grid gap-3">
                                <div v-for="assignment in event.assignments" :key="assignment.id" class="rounded-md bg-zinc-100 p-3">
                                    <div class="flex flex-col gap-1 md:flex-row md:items-start md:justify-between">
                                        <div>
                                            <p class="font-black">{{ assignment.user.name }}</p>
                                            <p class="text-sm text-zinc-600">{{ assignment.task }}</p>
                                        </div>
                                        <div class="text-sm font-black text-zinc-700">${{ assignment.payment_amount }} · {{ assignment.payment_status }}</div>
                                    </div>
                                    <p class="mt-2 text-xs font-bold" :class="assignment.registered_at ? 'text-green-700' : 'text-zinc-500'">
                                        {{ assignment.registered_at ? 'Empleado registrado para participar' : 'Pendiente por registro del empleado' }}
                                    </p>
                                    <form v-if="canRegister && !assignment.registered_at" class="mt-3" @submit.prevent="router.post(route('events.register', event.id), {}, { preserveScroll: true })">
                                        <button class="rounded-md bg-[#a8322b] px-4 py-2 text-sm font-black text-white">Confirmar participacion</button>
                                    </form>
                                </div>
                            </div>

                        </section>

                        <section class="rounded-md border border-zinc-200 p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <Wrench class="h-5 w-5 text-[#a8322b]" />
                                <h3 class="font-black">Herramientas responsables</h3>
                            </div>
                            <div class="grid gap-3">
                                <div v-for="assignment in event.tool_assignments" :key="assignment.id" class="rounded-md bg-zinc-100 p-3">
                                    <p class="font-black">{{ assignment.tool.name }} · {{ assignment.user.name }}</p>
                                    <p class="mt-1 text-sm text-zinc-600">{{ assignment.responsibility_notes || 'Sin observaciones.' }}</p>
                                    <p class="mt-1 text-xs font-bold text-zinc-500">{{ assignment.status }}</p>
                                </div>
                            </div>

                            <form v-if="canManage" class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto]" @submit.prevent="toolForms[event.id].post(route('events.tools.store', event.id), { preserveScroll: true, onSuccess: () => toolForms[event.id].reset('condition_out', 'responsibility_notes') })">
                                <select v-model="toolForms[event.id].tool_id" class="min-w-0 rounded-md border border-zinc-300 px-3 py-2 text-sm">
                                    <option v-for="tool in tools" :key="tool.id" :value="tool.id">{{ tool.name }}</option>
                                </select>
                                <select v-model="toolForms[event.id].user_id" class="min-w-0 rounded-md border border-zinc-300 px-3 py-2 text-sm">
                                    <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.name }}</option>
                                </select>
                                <input v-model="toolForms[event.id].responsibility_notes" class="min-w-0 rounded-md border border-zinc-300 px-3 py-2 text-sm" placeholder="Responsabilidad" />
                                <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-black text-white sm:col-span-2 lg:col-span-1">Asignar</button>
                            </form>
                        </section>
                    </div>
                </article>
            </section>
        </main>
    </AppLayout>
</template>
