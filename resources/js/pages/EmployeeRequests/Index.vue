<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { FileText } from 'lucide-vue-next';

type RequestItem = {
    id: number;
    type: string;
    status: string;
    details: string | null;
    admin_response: string | null;
    created_at: string;
    user?: { name: string; email: string };
};

const props = defineProps<{
    requests: RequestItem[];
    requestTypes: Record<string, string>;
    canManage: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Solicitudes', href: '/employee-requests' }];

const createForm = useForm({
    type: 'vale',
    details: '',
});

const reviewForms = props.requests.reduce(
    (carry, item) => ({
        ...carry,
        [item.id]: useForm({
            status: item.status,
            admin_response: item.admin_response ?? '',
        }),
    }),
    {} as Record<number, ReturnType<typeof useForm>>,
);
</script>

<template>
    <Head title="Solicitudes de trabajadores" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">Talento humano</p>
                <h1 class="mt-2 text-2xl font-black sm:text-3xl">Vales, estados de cuenta y desprendibles</h1>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[0.75fr_1.25fr]">
                <form
                    class="rounded-md border border-zinc-200 bg-white p-4 shadow-sm sm:p-5"
                    @submit.prevent="
                        createForm.post(route('employee-requests.store'), { preserveScroll: true, onSuccess: () => createForm.reset('details') })
                    "
                >
                    <div class="flex items-center gap-3">
                        <FileText class="h-6 w-6 text-[#a8322b]" />
                        <h2 class="text-xl font-black">Nueva solicitud</h2>
                    </div>
                    <div class="mt-5 grid gap-4">
                        <label class="grid gap-2 text-sm font-bold">
                            Tipo
                            <select v-model="createForm.type" class="rounded-md border border-zinc-300 px-3 py-3 font-normal">
                                <option v-for="(label, value) in requestTypes" :key="value" :value="value">{{ label }}</option>
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-bold">
                            Detalle
                            <textarea
                                v-model="createForm.details"
                                rows="5"
                                class="rounded-md border border-zinc-300 px-3 py-3 font-normal"
                            ></textarea>
                        </label>
                        <button
                            class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white disabled:opacity-60"
                            :disabled="createForm.processing"
                        >
                            Enviar solicitud
                        </button>
                    </div>
                </form>

                <div class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-black">Historial</h2>
                    <div class="mt-5 grid gap-4">
                        <article v-for="item in requests" :key="item.id" class="rounded-md border border-zinc-200 p-4">
                            <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <h3 class="font-black">{{ requestTypes[item.type] ?? item.type }}</h3>
                                    <p v-if="canManage && item.user" class="mt-1 text-sm font-semibold text-zinc-500">
                                        {{ item.user.name }} · {{ item.user.email }}
                                    </p>
                                    <p class="mt-2 text-sm leading-6 text-zinc-600">{{ item.details || 'Sin detalle adicional.' }}</p>
                                </div>
                                <span class="rounded-md bg-zinc-100 px-3 py-1 text-xs font-black text-zinc-700">{{ item.status }}</span>
                            </div>

                            <form
                                v-if="canManage"
                                class="mt-4 grid gap-3 sm:grid-cols-[180px_1fr] lg:grid-cols-[180px_1fr_auto]"
                                @submit.prevent="reviewForms[item.id].patch(route('employee-requests.update', item.id), { preserveScroll: true })"
                            >
                                <select v-model="reviewForms[item.id].status" class="rounded-md border border-zinc-300 px-3 py-2 text-sm">
                                    <option value="pendiente">pendiente</option>
                                    <option value="en_revision">en revision</option>
                                    <option value="aprobado">aprobado</option>
                                    <option value="rechazado">rechazado</option>
                                    <option value="entregado">entregado</option>
                                </select>
                                <input
                                    v-model="reviewForms[item.id].admin_response"
                                    placeholder="Respuesta administrativa"
                                    class="rounded-md border border-zinc-300 px-3 py-2 text-sm"
                                />
                                <button class="rounded-md bg-[#a8322b] px-4 py-2 text-sm font-black text-white sm:col-span-2 lg:col-span-1">
                                    Actualizar
                                </button>
                            </form>

                            <p v-else-if="item.admin_response" class="mt-4 rounded-md bg-zinc-100 p-3 text-sm font-semibold text-zinc-700">
                                {{ item.admin_response }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
