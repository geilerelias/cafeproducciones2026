<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Plus, ShieldCheck, Users } from 'lucide-vue-next';

type IdentificationOption = {
    value: string;
    label: string;
};

const props = defineProps<{
    users: User[];
    roles: string[];
    identificationTypes: IdentificationOption[];
    availablePermissions: string[];
    canManageProtectedUsers: boolean;
}>();

const selectClass = 'min-w-0 rounded-md border border-zinc-300 px-3 py-3 text-sm';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Usuarios y roles', href: '/access/users' }];

const createForm = useForm({
    name: '',
    identification_type: 'cc',
    identification_number: '',
    phone: '',
    email: '',
    password: '',
    password_confirmation: '',
    roles: ['cliente'] as string[],
});

const forms = props.users.reduce(
    (carry, user) => ({
        ...carry,
        [user.id]: useForm({
            roles: user.roles?.length ? user.roles : [user.effective_role ?? user.role ?? 'cliente'],
        }),
    }),
    {} as Record<number, ReturnType<typeof useForm>>,
);

const toggleRole = (userId: number, role: string) => {
    const form = forms[userId];
    const roles = form.roles as string[];
    const nextRoles = roles.includes(role) ? roles.filter((item) => item !== role) : [...roles, role];
    form.roles = nextRoles.length ? nextRoles : ['cliente'];
};

const saveUser = (user: User) => {
    forms[user.id].patch(route('access.users.update', user.id), { preserveScroll: true });
};

const isProtectedUser = (user: User) => !props.canManageProtectedUsers && Boolean(user.roles?.some((role) => ['admin', 'superadmin'].includes(role)));
const isProtectedRole = (role: string) => !props.canManageProtectedUsers && ['admin', 'superadmin'].includes(role);

const toggleCreateRole = (role: string) => {
    if (isProtectedRole(role)) return;
    const nextRoles = createForm.roles.includes(role) ? createForm.roles.filter((item) => item !== role) : [...createForm.roles, role];
    createForm.roles = nextRoles.length ? nextRoles : ['cliente'];
};
</script>

<template>
    <Head title="Usuarios y roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="min-h-screen overflow-x-hidden bg-zinc-50 p-3 sm:p-4 md:p-6">
            <section class="rounded-md bg-zinc-950 p-5 text-white shadow-xl sm:p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f0c8be]">Usuarios</p>
                        <h1 class="mt-2 text-2xl font-black sm:text-3xl">Gestion de usuarios y roles asignados</h1>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-zinc-300">
                            El admin puede asignar o quitar roles. El correo geilerelias@gmail.com conserva el rol superadmin.
                        </p>
                    </div>
                    <ShieldCheck class="h-10 w-10 text-[#f0c8be]" />
                </div>
            </section>

            <section class="mt-6 rounded-md border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <Plus class="h-6 w-6 text-[#a8322b]" />
                    <h2 class="text-xl font-black">Registrar usuario</h2>
                </div>
                <form
                    class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                    @submit.prevent="createForm.post(route('access.users.store'), { preserveScroll: true, onSuccess: () => createForm.reset() })"
                >
                    <input v-model="createForm.name" class="min-w-0 rounded-md border border-zinc-300 px-3 py-3" placeholder="Nombre" />
                    <select v-model="createForm.identification_type" :class="selectClass">
                        <option v-for="option in identificationTypes" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                    <input
                        v-model="createForm.identification_number"
                        class="min-w-0 rounded-md border border-zinc-300 px-3 py-3"
                        placeholder="Numero de identificacion"
                    />
                    <input v-model="createForm.phone" type="tel" class="min-w-0 rounded-md border border-zinc-300 px-3 py-3" placeholder="Telefono" />
                    <input v-model="createForm.email" type="email" class="min-w-0 rounded-md border border-zinc-300 px-3 py-3" placeholder="Correo" />
                    <input
                        v-model="createForm.password"
                        type="password"
                        class="min-w-0 rounded-md border border-zinc-300 px-3 py-3"
                        placeholder="Clave"
                    />
                    <input
                        v-model="createForm.password_confirmation"
                        type="password"
                        class="min-w-0 rounded-md border border-zinc-300 px-3 py-3"
                        placeholder="Confirmar clave"
                    />
                    <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white sm:col-span-2 lg:col-span-1">Crear</button>
                    <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4">
                        <p class="text-sm font-bold text-zinc-700">Roles iniciales</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button
                                v-for="role in roles"
                                :key="role"
                                type="button"
                                class="rounded-md border px-3 py-2 text-xs font-black transition disabled:cursor-not-allowed disabled:opacity-40"
                                :class="
                                    createForm.roles.includes(role)
                                        ? 'border-[#a8322b] bg-[#fff1ee] text-[#7f241f]'
                                        : 'border-zinc-200 bg-white text-zinc-600'
                                "
                                :disabled="isProtectedRole(role)"
                                @click="toggleCreateRole(role)"
                            >
                                {{ role }}
                            </button>
                        </div>
                    </div>
                </form>
            </section>

            <section class="mt-6 overflow-hidden rounded-md border border-zinc-200 bg-white shadow-sm">
                <div class="border-b border-zinc-200 p-5">
                    <div class="flex items-center gap-3">
                        <Users class="h-6 w-6 text-[#a8322b]" />
                        <h2 class="text-xl font-black">Gestion de usuarios</h2>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200">
                    <article
                        v-for="user in users"
                        :key="user.id"
                        class="grid gap-5 p-5 xl:grid-cols-[0.8fr_1.4fr_auto] xl:items-start"
                        :class="isProtectedUser(user) ? 'bg-zinc-50' : ''"
                    >
                        <div>
                            <h3 class="font-black text-zinc-950">{{ user.name }}</h3>
                            <p v-if="user.identification_number" class="mt-1 text-sm font-semibold text-zinc-600">
                                {{ user.identification_label ?? user.identification_type }}: {{ user.identification_number }}
                            </p>
                            <p v-if="user.phone" class="mt-1 text-sm text-zinc-500">{{ user.phone }}</p>
                            <p class="mt-1 text-sm font-semibold text-zinc-500">{{ user.email }}</p>
                            <p class="mt-2 rounded-md bg-zinc-100 px-3 py-1 text-xs font-black uppercase text-zinc-700">{{ user.effective_role }}</p>
                            <p v-if="isProtectedUser(user)" class="mt-2 text-xs font-bold text-[#a8322b]">
                                Protegido: solo superadmin puede modificarlo.
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-bold text-zinc-700">Roles asignados</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <button
                                    v-for="role in roles"
                                    :key="role"
                                    type="button"
                                    class="rounded-md border px-3 py-2 text-xs font-black transition disabled:cursor-not-allowed disabled:opacity-40"
                                    :class="
                                        (forms[user.id].roles as string[]).includes(role)
                                            ? 'border-[#a8322b] bg-[#fff1ee] text-[#7f241f]'
                                            : 'border-zinc-200 bg-white text-zinc-600'
                                    "
                                    :disabled="isProtectedUser(user) || isProtectedRole(role)"
                                    @click="toggleRole(user.id, role)"
                                >
                                    {{ role }}
                                </button>
                            </div>
                            <p class="mt-3 text-xs font-semibold text-zinc-500">
                                Permisos efectivos: {{ user.effective_permissions?.join(', ') || 'sin permisos' }}
                            </p>
                        </div>

                        <button
                            class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-black text-white disabled:cursor-not-allowed disabled:opacity-40"
                            :disabled="forms[user.id].processing || isProtectedUser(user)"
                            @click="saveUser(user)"
                        >
                            Guardar
                        </button>
                    </article>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
