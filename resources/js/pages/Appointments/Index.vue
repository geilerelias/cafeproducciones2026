<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { CalendarDays } from 'lucide-vue-next';

type Appointment = {
    id: number;
    subject: string;
    scheduled_at: string;
    status: string;
    notes: string | null;
    user?: { name: string; email: string };
};

const props = defineProps<{ appointments: Appointment[]; canManage: boolean }>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Citas', href: '/appointments' }];
const form = useForm({ subject: '', scheduled_at: '', notes: '' });

const reviewForms = props.appointments.reduce(
    (carry, item) => ({ ...carry, [item.id]: useForm({ status: item.status, notes: item.notes ?? '' }) }),
    {} as Record<number, ReturnType<typeof useForm>>,
);
</script>

<template>
    <Head title="Citas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">Agenda laboral</p>
                <h1 class="mt-2 text-2xl font-black sm:text-3xl">Citas de empleados</h1>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[0.7fr_1.3fr]">
                <form
                    class="rounded-md border border-zinc-200 bg-white p-4 shadow-sm sm:p-5"
                    @submit.prevent="form.post(route('appointments.store'), { preserveScroll: true, onSuccess: () => form.reset() })"
                >
                    <div class="flex items-center gap-3">
                        <CalendarDays class="h-6 w-6 text-[#a8322b]" />
                        <h2 class="text-xl font-black">Generar cita</h2>
                    </div>
                    <div class="mt-5 grid gap-4">
                        <input v-model="form.subject" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Asunto" />
                        <input v-model="form.scheduled_at" type="datetime-local" class="rounded-md border border-zinc-300 px-3 py-3" />
                        <textarea v-model="form.notes" rows="4" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Notas"></textarea>
                        <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white">Crear cita</button>
                    </div>
                </form>

                <div class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-black">Listado de citas</h2>
                    <div class="mt-5 grid gap-4">
                        <article v-for="item in appointments" :key="item.id" class="rounded-md border border-zinc-200 p-4">
                            <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <h3 class="font-black">{{ item.subject }}</h3>
                                    <p class="mt-1 text-sm font-semibold text-zinc-500">{{ item.scheduled_at }}</p>
                                    <p v-if="canManage && item.user" class="mt-1 text-sm text-zinc-500">
                                        {{ item.user.name }} · {{ item.user.email }}
                                    </p>
                                    <p class="mt-2 text-sm text-zinc-600">{{ item.notes || 'Sin notas.' }}</p>
                                </div>
                                <span class="rounded-md bg-zinc-100 px-3 py-1 text-xs font-black text-zinc-700">{{ item.status }}</span>
                            </div>
                            <form
                                v-if="canManage"
                                class="mt-4 grid gap-3 sm:grid-cols-[180px_1fr] lg:grid-cols-[180px_1fr_auto]"
                                @submit.prevent="reviewForms[item.id].patch(route('appointments.update', item.id), { preserveScroll: true })"
                            >
                                <select v-model="reviewForms[item.id].status" class="rounded-md border border-zinc-300 px-3 py-2 text-sm">
                                    <option value="pendiente">pendiente</option>
                                    <option value="confirmada">confirmada</option>
                                    <option value="cancelada">cancelada</option>
                                    <option value="atendida">atendida</option>
                                </select>
                                <input v-model="reviewForms[item.id].notes" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" />
                                <button class="rounded-md bg-[#a8322b] px-4 py-2 text-sm font-black text-white sm:col-span-2 lg:col-span-1">
                                    Actualizar
                                </button>
                            </form>
                        </article>
                    </div>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
