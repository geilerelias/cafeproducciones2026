<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ShieldCheck, Trash2 } from 'lucide-vue-next';

type RoleItem = {
    id: number;
    name: string;
    permissions: string[];
    users_count: number;
};

const props = defineProps<{
    roles: RoleItem[];
    availablePermissions: string[];
    canManageProtectedRoles: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Roles', href: '/access/roles' }];

const createForm = useForm({
    name: '',
    permissions: [] as string[],
});

const roleForms = props.roles.reduce(
    (carry, role) => ({
        ...carry,
        [role.id]: useForm({
            name: role.name,
            permissions: role.permissions,
        }),
    }),
    {} as Record<number, ReturnType<typeof useForm>>,
);

const toggle = (permissions: string[], permission: string) => {
    return permissions.includes(permission) ? permissions.filter((item) => item !== permission) : [...permissions, permission];
};

const isProtectedRole = (role: RoleItem) => !props.canManageProtectedRoles && ['admin', 'superadmin'].includes(role.name);
</script>

<template>
    <Head title="Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">Control de acceso</p>
                <h1 class="mt-2 text-2xl font-black sm:text-3xl">Roles</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-zinc-300">Administra los permisos que hereda cada rol.</p>
            </section>

            <section class="mt-6 grid gap-6 xl:grid-cols-[0.7fr_1.3fr]">
                <form class="rounded-md border border-zinc-200 bg-white p-4 shadow-sm sm:p-5" @submit.prevent="createForm.post(route('access.roles.store'), { preserveScroll: true, onSuccess: () => createForm.reset() })">
                    <div class="flex items-center gap-3">
                        <ShieldCheck class="h-6 w-6 text-[#a8322b]" />
                        <h2 class="text-xl font-black">Nuevo rol</h2>
                    </div>
                    <input v-model="createForm.name" class="mt-5 w-full rounded-md border border-zinc-300 px-3 py-3" placeholder="nombre-del-rol" />
                    <div class="mt-4 flex flex-wrap gap-2">
                        <button
                            v-for="permission in availablePermissions"
                            :key="permission"
                            type="button"
                            class="rounded-md border px-3 py-2 text-xs font-black"
                            :class="createForm.permissions.includes(permission) ? 'border-[#a8322b] bg-[#fff1ee] text-[#7f241f]' : 'border-zinc-200 bg-white text-zinc-600'"
                            @click="createForm.permissions = toggle(createForm.permissions, permission)"
                        >
                            {{ permission }}
                        </button>
                    </div>
                    <button class="mt-5 rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white">Crear rol</button>
                </form>

                <div class="grid gap-4">
                    <article v-for="role in roles" :key="role.id" class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm" :class="isProtectedRole(role) ? 'opacity-75' : ''">
                        <form @submit.prevent="roleForms[role.id].patch(route('access.roles.update', role.id), { preserveScroll: true })">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <input v-model="roleForms[role.id].name" class="w-full min-w-0 rounded-md border border-zinc-300 px-3 py-2 font-black" :disabled="['trabajador', 'cliente', 'admin', 'superadmin'].includes(role.name)" />
                                    <p class="mt-2 text-sm font-semibold text-zinc-500">{{ role.users_count }} usuarios asignados</p>
                                    <p v-if="isProtectedRole(role)" class="mt-2 text-xs font-bold text-[#a8322b]">Protegido: solo superadmin puede cambiar este rol.</p>
                                </div>
                                <div class="flex gap-2">
                                    <button class="rounded-md bg-[#a8322b] px-4 py-2 text-sm font-black text-white disabled:cursor-not-allowed disabled:opacity-40" :disabled="isProtectedRole(role)">Guardar</button>
                                    <button v-if="!['trabajador', 'cliente', 'admin', 'superadmin'].includes(role.name)" type="button" class="rounded-md border border-zinc-300 p-2" @click="router.delete(route('access.roles.destroy', role.id), { preserveScroll: true })">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button
                                    v-for="permission in availablePermissions"
                                    :key="permission"
                                    type="button"
                                    class="rounded-md border px-3 py-2 text-xs font-black disabled:cursor-not-allowed disabled:opacity-40"
                                    :class="(roleForms[role.id].permissions as string[]).includes(permission) ? 'border-[#a8322b] bg-[#fff1ee] text-[#7f241f]' : 'border-zinc-200 bg-white text-zinc-600'"
                                    :disabled="isProtectedRole(role)"
                                    @click="roleForms[role.id].permissions = toggle(roleForms[role.id].permissions as string[], permission)"
                                >
                                    {{ permission }}
                                </button>
                            </div>
                        </form>
                    </article>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
