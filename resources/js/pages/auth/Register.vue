<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/auth/AuthSimpleLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Solicitar acceso" description="Crea tu perfil para entrar como cliente, administrador o colaborador del equipo.">
        <Head title="Crear cuenta" />

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Nombre completo</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="name"
                        placeholder="Nombre y apellido"
                        class="h-12 rounded-md bg-white/90 shadow-sm transition focus:ring-2 focus:ring-[#b44136] dark:bg-white/10"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Correo electronico</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        tabindex="2"
                        autocomplete="email"
                        placeholder="usuario@empresa.com"
                        class="h-12 rounded-md bg-white/90 shadow-sm transition focus:ring-2 focus:ring-[#b44136] dark:bg-white/10"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Contrasena</Label>
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        tabindex="3"
                        autocomplete="new-password"
                        placeholder="Crea una contrasena segura"
                        class="h-12 rounded-md bg-white/90 shadow-sm transition focus:ring-2 focus:ring-[#b44136] dark:bg-white/10"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirmar contrasena</Label>
                    <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        tabindex="4"
                        autocomplete="new-password"
                        placeholder="Repite tu contrasena"
                        class="h-12 rounded-md bg-white/90 shadow-sm transition focus:ring-2 focus:ring-[#b44136] dark:bg-white/10"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="shine-hover mt-2 h-12 w-full rounded-md bg-zinc-950 font-black shadow-[0_14px_35px_rgba(15,23,42,0.22)] transition hover:-translate-y-0.5 hover:bg-zinc-800 dark:bg-[#a8322b] dark:text-white dark:hover:bg-[#b44136]"
                    tabindex="5"
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Solicitar acceso
                </Button>
            </div>

            <div class="rounded-md bg-zinc-100 px-4 py-3 text-center text-sm text-muted-foreground dark:bg-white/5">
                Ya tienes acceso?
                <TextLink :href="route('login')" class="underline underline-offset-4" tabindex="6">Ingresar</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
