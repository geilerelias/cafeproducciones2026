<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/auth/AuthSimpleLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase title="Ingresar" description="Accede como cliente, administrador o equipo interno para gestionar procesos, citas y seguimiento operativo.">
        <Head title="Iniciar sesion" />

        <div v-if="status" class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-medium text-emerald-700 dark:border-emerald-900/30 dark:bg-emerald-950/30 dark:text-emerald-300">
            {{ status }}
        </div>

        <div class="mb-6 rounded-md border border-[#e8c8c2] bg-[#fff7f5]/90 p-4 text-sm text-[#3f1714] shadow-sm dark:border-[#b44136]/20 dark:bg-[#b44136]/10 dark:text-[#ffe8e2]">
            <div class="space-y-2">
            <p>Gestiona solicitudes, asignaciones, tareas y seguimiento desde un solo acceso.</p>
            <p>Clientes, administradores y trabajadores usan el mismo portal con permisos definidos.</p>
            <p>Encuestas, trazabilidad y reportes quedan centralizados para operar con orden.</p>
        </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Correo electronico o usuario</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="usuario@empresa.com"
                        class="h-12 rounded-md bg-white/90 shadow-sm transition focus:ring-2 focus:ring-[#b44136] dark:bg-white/10"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Contrasena</Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm" tabindex="5">Recuperar acceso</TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="Ingresa tu contrasena"
                        class="h-12 rounded-md bg-white/90 shadow-sm transition focus:ring-2 focus:ring-[#b44136] dark:bg-white/10"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between" tabindex="3">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model:checked="form.remember" tabindex="4" />
                        <span>Recordarme en este equipo</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="shine-hover mt-2 h-12 w-full rounded-md bg-zinc-950 font-black shadow-[0_14px_35px_rgba(15,23,42,0.22)] transition hover:-translate-y-0.5 hover:bg-zinc-800 dark:bg-[#a8322b] dark:text-white dark:hover:bg-[#b44136]"
                    tabindex="4"
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Entrar al panel
                </Button>
            </div>

            <div class="rounded-md bg-zinc-100 px-4 py-3 text-center text-sm text-muted-foreground dark:bg-white/5">
                Aun no tienes acceso?
                <TextLink :href="route('register')" :tabindex="5">Solicitar registro</TextLink>
            </div>
            <Link :href="route('home')" class="block text-center text-xs font-bold text-zinc-500 hover:text-zinc-950 dark:hover:text-white">Volver al sitio publico</Link>
        </form>
    </AuthBase>
</template>
