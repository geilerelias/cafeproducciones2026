<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Download, Newspaper, Trash2 } from 'lucide-vue-next';

type NewsItem = {
    id: number;
    title: string;
    slug: string;
    category: string;
    platform: string | null;
    source_url: string | null;
    image_url: string | null;
    excerpt: string;
    body: string | null;
    status: 'draft' | 'published';
    is_featured: boolean;
    published_at: string | null;
    meta_title: string | null;
    meta_description: string | null;
};

const props = defineProps<{ news: NewsItem[] }>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Noticias', href: '/admin/news' }];

const createForm = useForm({
    title: '',
    slug: '',
    category: 'Noticias',
    platform: '',
    source_url: '',
    image_url: '',
    excerpt: '',
    body: '',
    status: 'draft',
    is_featured: false,
    published_at: '',
    meta_title: '',
    meta_description: '',
});

const importForm = useForm({
    source_url: '',
});

const editForms = props.news.reduce(
    (carry, item) => ({
        ...carry,
        [item.id]: useForm({
            title: item.title,
            slug: item.slug,
            category: item.category,
            platform: item.platform ?? '',
            source_url: item.source_url ?? '',
            image_url: item.image_url ?? '',
            excerpt: item.excerpt,
            body: item.body ?? '',
            status: item.status,
            is_featured: item.is_featured,
            published_at: item.published_at ?? '',
            meta_title: item.meta_title ?? '',
            meta_description: item.meta_description ?? '',
        }),
    }),
    {} as Record<number, ReturnType<typeof useForm>>,
);
</script>

<template>
    <Head title="Gestion de noticias" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">Contenido</p>
                <h1 class="mt-2 text-2xl font-black sm:text-3xl">Gestion dinamica de noticias</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-zinc-300">
                    Crea noticias manuales o importa una URL de Facebook, Instagram o una pagina web para traer metadatos iniciales.
                </p>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[0.85fr_1.15fr]">
                <div class="grid gap-6">
                    <form
                        class="rounded-md border border-zinc-200 bg-white p-4 shadow-sm sm:p-5"
                        @submit.prevent="importForm.post(route('admin.news.import'), { preserveScroll: true, onSuccess: () => importForm.reset() })"
                    >
                        <div class="flex items-center gap-3">
                            <Download class="h-6 w-6 text-[#a8322b]" />
                            <h2 class="text-xl font-black">Importar desde URL social</h2>
                        </div>
                        <div class="mt-5 grid gap-3 sm:grid-cols-[1fr_auto]">
                            <input
                                v-model="importForm.source_url"
                                type="url"
                                class="rounded-md border border-zinc-300 px-3 py-3"
                                placeholder="https://www.instagram.com/p/..."
                            />
                            <button class="rounded-md bg-[#a8322b] px-5 py-3 text-sm font-black text-white" :disabled="importForm.processing">
                                Importar
                            </button>
                        </div>
                        <p class="mt-3 text-xs font-semibold text-zinc-500">
                            Facebook e Instagram pueden limitar sus metadatos. Si no llegan imagen o texto, completa los campos manualmente.
                        </p>
                        <button
                            type="button"
                            class="mt-4 w-full rounded-md border border-zinc-300 px-4 py-3 text-sm font-black text-zinc-950 transition hover:bg-zinc-100"
                            @click="router.post(route('admin.news.import-social-profiles'), {}, { preserveScroll: true })"
                        >
                            Extraer perfiles oficiales Facebook e Instagram
                        </button>
                    </form>

                    <form
                        class="rounded-md border border-zinc-200 bg-white p-4 shadow-sm sm:p-5"
                        @submit.prevent="createForm.post(route('admin.news.store'), { preserveScroll: true, onSuccess: () => createForm.reset() })"
                    >
                        <div class="flex items-center gap-3">
                            <Newspaper class="h-6 w-6 text-[#a8322b]" />
                            <h2 class="text-xl font-black">Nueva noticia</h2>
                        </div>
                        <div class="mt-5 grid gap-4">
                            <input v-model="createForm.title" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Titulo" />
                            <input v-model="createForm.slug" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="slug-opcional" />
                            <div class="grid gap-4 sm:grid-cols-2">
                                <input v-model="createForm.category" class="rounded-md border border-zinc-300 px-3 py-3" placeholder="Categoria" />
                                <input
                                    v-model="createForm.platform"
                                    class="rounded-md border border-zinc-300 px-3 py-3"
                                    placeholder="Instagram, Facebook, Web"
                                />
                            </div>
                            <input
                                v-model="createForm.source_url"
                                type="url"
                                class="rounded-md border border-zinc-300 px-3 py-3"
                                placeholder="URL fuente"
                            />
                            <input
                                v-model="createForm.image_url"
                                type="url"
                                class="rounded-md border border-zinc-300 px-3 py-3"
                                placeholder="URL de imagen para portada y redes"
                            />
                            <textarea
                                v-model="createForm.excerpt"
                                rows="3"
                                class="rounded-md border border-zinc-300 px-3 py-3"
                                placeholder="Resumen para tarjetas y SEO"
                            ></textarea>
                            <textarea
                                v-model="createForm.body"
                                rows="6"
                                class="rounded-md border border-zinc-300 px-3 py-3"
                                placeholder="Contenido"
                            ></textarea>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <select v-model="createForm.status" class="rounded-md border border-zinc-300 px-3 py-3">
                                    <option value="draft">Borrador</option>
                                    <option value="published">Publicado</option>
                                </select>
                                <input v-model="createForm.published_at" type="datetime-local" class="rounded-md border border-zinc-300 px-3 py-3" />
                            </div>
                            <label class="flex items-center gap-3 rounded-md border border-zinc-300 px-3 py-3 text-sm font-bold">
                                <input v-model="createForm.is_featured" type="checkbox" />
                                Destacada
                            </label>
                            <input
                                v-model="createForm.meta_title"
                                class="rounded-md border border-zinc-300 px-3 py-3"
                                placeholder="Meta titulo opcional"
                            />
                            <textarea
                                v-model="createForm.meta_description"
                                rows="2"
                                class="rounded-md border border-zinc-300 px-3 py-3"
                                placeholder="Meta descripcion opcional"
                            ></textarea>
                            <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white" :disabled="createForm.processing">
                                Crear noticia
                            </button>
                        </div>
                    </form>
                </div>

                <div class="grid gap-4">
                    <article v-for="item in news" :key="item.id" class="rounded-md border border-zinc-200 bg-white p-4 shadow-sm">
                        <form
                            class="grid gap-4"
                            @submit.prevent="editForms[item.id].patch(route('admin.news.update', item.id), { preserveScroll: true })"
                        >
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.18em] text-[#a8322b]">{{ item.status }}</p>
                                    <h2 class="mt-1 text-lg font-black">{{ item.title }}</h2>
                                    <Link
                                        :href="route('news.show', item.slug)"
                                        class="mt-1 inline-flex text-xs font-bold text-zinc-500 hover:text-[#a8322b]"
                                        >Ver publica</Link
                                    >
                                </div>
                                <button
                                    type="button"
                                    class="rounded-md border border-zinc-300 p-2"
                                    @click="router.delete(route('admin.news.destroy', item.id), { preserveScroll: true })"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                            <input v-model="editForms[item.id].title" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" />
                            <input v-model="editForms[item.id].slug" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" />
                            <div class="grid gap-3 sm:grid-cols-2">
                                <input v-model="editForms[item.id].category" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" />
                                <input v-model="editForms[item.id].platform" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" />
                            </div>
                            <input
                                v-model="editForms[item.id].source_url"
                                class="rounded-md border border-zinc-300 px-3 py-2 text-sm"
                                placeholder="URL fuente"
                            />
                            <input
                                v-model="editForms[item.id].image_url"
                                class="rounded-md border border-zinc-300 px-3 py-2 text-sm"
                                placeholder="URL imagen"
                            />
                            <textarea
                                v-model="editForms[item.id].excerpt"
                                rows="2"
                                class="rounded-md border border-zinc-300 px-3 py-2 text-sm"
                            ></textarea>
                            <textarea
                                v-model="editForms[item.id].body"
                                rows="4"
                                class="rounded-md border border-zinc-300 px-3 py-2 text-sm"
                            ></textarea>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <select v-model="editForms[item.id].status" class="rounded-md border border-zinc-300 px-3 py-2 text-sm">
                                    <option value="draft">Borrador</option>
                                    <option value="published">Publicado</option>
                                </select>
                                <input
                                    v-model="editForms[item.id].published_at"
                                    type="datetime-local"
                                    class="rounded-md border border-zinc-300 px-3 py-2 text-sm"
                                />
                            </div>
                            <label class="flex items-center gap-2 text-sm font-bold">
                                <input v-model="editForms[item.id].is_featured" type="checkbox" />
                                Destacada
                            </label>
                            <input
                                v-model="editForms[item.id].meta_title"
                                class="rounded-md border border-zinc-300 px-3 py-2 text-sm"
                                placeholder="Meta titulo"
                            />
                            <textarea
                                v-model="editForms[item.id].meta_description"
                                rows="2"
                                class="rounded-md border border-zinc-300 px-3 py-2 text-sm"
                                placeholder="Meta descripcion"
                            ></textarea>
                            <button class="rounded-md bg-[#a8322b] px-4 py-2 text-sm font-black text-white" :disabled="editForms[item.id].processing">
                                Guardar
                            </button>
                        </form>
                    </article>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
