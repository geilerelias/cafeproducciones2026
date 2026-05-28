<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ClipboardList, Copy, ExternalLink, Plus, Trash2 } from 'lucide-vue-next';

type Field = {
    label: string;
    key: string;
    type: 'text' | 'number' | 'date' | 'select' | 'textarea';
    required: boolean;
    options: string;
};

type CustomForm = {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    audience: string;
    fields: Field[];
    is_active: boolean;
    is_public: boolean;
    submit_label: string;
    success_message: string;
    submissions_count: number;
};

defineProps<{ forms: CustomForm[] }>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Formularios', href: '/admin/forms' }];

const form = useForm({
    title: '',
    slug: '',
    description: '',
    audience: 'trabajador',
    is_active: true,
    is_public: true,
    submit_label: 'Enviar respuesta',
    success_message: 'Respuesta enviada correctamente.',
    fields: [
        { label: 'Nombre completo', key: 'nombre', type: 'text', required: true, options: '' },
        { label: 'Cedula', key: 'cedula', type: 'text', required: true, options: '' },
        { label: 'Edad', key: 'edad', type: 'number', required: true, options: '' },
        { label: 'Tallas', key: 'tallas', type: 'text', required: false, options: '' },
        { label: 'Tipo de sangre', key: 'tipo_sangre', type: 'text', required: false, options: '' },
        { label: 'EPS', key: 'eps', type: 'text', required: false, options: '' },
    ] as Field[],
});

const addField = () => {
    form.fields.push({ label: '', key: '', type: 'text', required: false, options: '' });
};

const removeField = (index: number) => {
    form.fields.splice(index, 1);
};

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            fields: data.fields.map((field) => ({
                ...field,
                options: String(field.options || '')
                    .split(',')
                    .map((option) => option.trim())
                    .filter(Boolean),
            })),
        }))
        .post(route('admin.forms.store'), {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
};

const copyPublicUrl = async (slug: string) => {
    await navigator.clipboard?.writeText(route('forms.public.show', slug, true));
};
</script>

<template>
    <Head title="Formularios flexibles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">Administracion</p>
                <h1 class="mt-2 text-2xl font-black sm:text-3xl">Encuestas y formularios flexibles</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-zinc-300">
                    Define campos dinamicos para hojas de vida, trabajos, datos personales, encuestas y registros operativos.
                </p>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                <form class="rounded-md border border-zinc-200 bg-white p-4 shadow-sm sm:p-5" @submit.prevent="submit">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <ClipboardList class="h-6 w-6 text-[#a8322b]" />
                            <h2 class="text-xl font-black">Nuevo formulario</h2>
                        </div>
                        <button type="button" class="inline-flex items-center gap-2 rounded-md border border-zinc-300 px-3 py-2 text-sm font-black" @click="addField">
                            <Plus class="h-4 w-4" />
                            Campo
                        </button>
                    </div>

                    <div class="mt-5 grid gap-4">
                        <input v-model="form.title" placeholder="Titulo del formulario" class="rounded-md border border-zinc-300 px-3 py-3" />
                        <input v-model="form.slug" placeholder="url-compartible-opcional" class="rounded-md border border-zinc-300 px-3 py-3" />
                        <textarea v-model="form.description" placeholder="Descripcion" rows="3" class="rounded-md border border-zinc-300 px-3 py-3"></textarea>
                        <div class="grid gap-4 md:grid-cols-2">
                            <select v-model="form.audience" class="rounded-md border border-zinc-300 px-3 py-3">
                                <option value="trabajador">Trabajador</option>
                                <option value="cliente">Cliente</option>
                                <option value="todos">Todos</option>
                            </select>
                            <label class="flex items-center gap-3 rounded-md border border-zinc-300 px-3 py-3 text-sm font-bold">
                                <input v-model="form.is_active" type="checkbox" />
                                Activo
                            </label>
                            <label class="flex items-center gap-3 rounded-md border border-zinc-300 px-3 py-3 text-sm font-bold">
                                <input v-model="form.is_public" type="checkbox" />
                                URL publica compartible
                            </label>
                        </div>
                        <input v-model="form.submit_label" placeholder="Texto del boton de envio" class="rounded-md border border-zinc-300 px-3 py-3" />
                        <input v-model="form.success_message" placeholder="Mensaje despues de enviar" class="rounded-md border border-zinc-300 px-3 py-3" />
                    </div>

                    <div class="mt-5 grid gap-3">
                        <article v-for="(field, index) in form.fields" :key="index" class="rounded-md border border-zinc-200 p-3">
                            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_0.8fr_150px_auto]">
                                <input v-model="field.label" placeholder="Etiqueta" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" />
                                <input v-model="field.key" placeholder="clave_sin_espacios" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" />
                                <select v-model="field.type" class="rounded-md border border-zinc-300 px-3 py-2 text-sm">
                                    <option value="text">Texto</option>
                                    <option value="number">Numero</option>
                                    <option value="date">Fecha</option>
                                    <option value="select">Lista</option>
                                    <option value="textarea">Parrafo</option>
                                </select>
                                <button type="button" class="rounded-md border border-zinc-300 px-3 py-2 sm:col-span-2 lg:col-span-1" @click="removeField(index)">
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                            <div class="mt-3 grid gap-3 sm:grid-cols-[1fr_auto]">
                                <input v-model="field.options" placeholder="Opciones para lista, separadas por coma" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" />
                                <label class="flex items-center gap-2 text-sm font-bold">
                                    <input v-model="field.required" type="checkbox" />
                                    Obligatorio
                                </label>
                            </div>
                        </article>
                    </div>

                    <button class="mt-5 rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white disabled:opacity-60" :disabled="form.processing">Crear formulario</button>
                </form>

                <div class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-black">Formularios creados</h2>
                    <div class="mt-5 grid gap-4">
                        <article v-for="item in forms" :key="item.id" class="rounded-md border border-zinc-200 p-4">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <h3 class="font-black">{{ item.title }}</h3>
                                    <p class="mt-1 text-sm text-zinc-600">{{ item.description || 'Sin descripcion.' }}</p>
                                    <p class="mt-2 text-xs font-black uppercase tracking-[0.16em] text-[#a8322b]">{{ item.audience }} · {{ item.fields.length }} campos · {{ item.submissions_count }} respuestas</p>
                                </div>
                                <div class="flex gap-2">
                                    <Link v-if="item.is_public" :href="route('forms.public.show', item.slug)" class="rounded-md border border-zinc-300 p-2" title="Ver URL publica">
                                        <ExternalLink class="h-4 w-4" />
                                    </Link>
                                    <button v-if="item.is_public" type="button" class="rounded-md border border-zinc-300 p-2" title="Copiar URL publica" @click="copyPublicUrl(item.slug)">
                                        <Copy class="h-4 w-4" />
                                    </button>
                                    <button class="rounded-md border border-zinc-300 p-2" @click="router.delete(route('admin.forms.destroy', item.id), { preserveScroll: true })">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                            <p v-if="item.is_public" class="mt-3 break-all rounded-md bg-zinc-100 px-3 py-2 text-xs font-bold text-zinc-600">
                                {{ route('forms.public.show', item.slug, true) }}
                            </p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span v-for="field in item.fields" :key="field.key" class="rounded-md bg-zinc-100 px-3 py-1 text-xs font-bold text-zinc-700">{{ field.label }}</span>
                            </div>
                        </article>
                    </div>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
