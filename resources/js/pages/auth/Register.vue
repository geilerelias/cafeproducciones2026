<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/auth/AuthSimpleLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed } from 'vue';

type IdentificationOption = {
    value: string;
    label: string;
};

const props = defineProps<{
    identificationTypes: IdentificationOption[];
}>();

const form = useForm({
    name: '',
    identification_type: 'cc',
    identification_number: '',
    phone: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const identificationPlaceholder = computed(() => {
    const placeholders: Record<string, string> = {
        cc: 'Ej. 1234567890',
        ce: 'Ej. 1234567',
        ti: 'Ej. 1234567890',
        ptp: 'Numero del PTP',
        pep: 'Numero del PEP',
        pasaporte: 'Ej. AB123456',
        certificado_migracion: 'Numero del certificado',
        otro: 'Numero del documento',
    };

    return placeholders[form.identification_type] ?? 'Numero de identificacion';
});

const selectClass =
    'flex h-12 w-full rounded-md border border-input bg-white/90 px-3 py-2 text-sm shadow-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#b44136] dark:bg-white/10';

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase
        title="Solicitar acceso"
        description="Completa tus datos de contacto. Te enviaremos un correo para confirmar tu cuenta antes de ingresar al portal."
    >
        <Head title="Crear cuenta" />

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="grid gap-2 sm:col-span-2">
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
                    <Label for="identification_type">Tipo de identificacion</Label>
                    <select id="identification_type" v-model="form.identification_type" required tabindex="2" :class="selectClass">
                        <option v-for="option in props.identificationTypes" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.identification_type" />
                </div>

                <div class="grid gap-2">
                    <Label for="identification_number">Numero de identificacion</Label>
                    <Input
                        id="identification_number"
                        v-model="form.identification_number"
                        type="text"
                        required
                        tabindex="3"
                        autocomplete="off"
                        :placeholder="identificationPlaceholder"
                        class="h-12 rounded-md bg-white/90 shadow-sm transition focus:ring-2 focus:ring-[#b44136] dark:bg-white/10"
                    />
                    <InputError :message="form.errors.identification_number" />
                </div>

                <div class="grid gap-2 sm:col-span-2">
                    <Label for="phone">Telefono de contacto</Label>
                    <Input
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                        required
                        tabindex="4"
                        autocomplete="tel"
                        placeholder="Ej. 3001234567"
                        class="h-12 rounded-md bg-white/90 shadow-sm transition focus:ring-2 focus:ring-[#b44136] dark:bg-white/10"
                    />
                    <InputError :message="form.errors.phone" />
                </div>

                <div class="grid gap-2 sm:col-span-2">
                    <Label for="email">Correo electronico</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        tabindex="5"
                        autocomplete="email"
                        placeholder="usuario@empresa.com"
                        class="h-12 rounded-md bg-white/90 shadow-sm transition focus:ring-2 focus:ring-[#b44136] dark:bg-white/10"
                    />
                    <InputError :message="form.errors.email" />
                    <p class="text-xs text-muted-foreground">Enviaremos un enlace de confirmacion a este correo.</p>
                </div>

                <div class="grid gap-2">
                    <Label for="password">Contrasena</Label>
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        tabindex="6"
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
                        tabindex="7"
                        autocomplete="new-password"
                        placeholder="Repite tu contrasena"
                        class="h-12 rounded-md bg-white/90 shadow-sm transition focus:ring-2 focus:ring-[#b44136] dark:bg-white/10"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="shine-hover mt-1 h-12 w-full rounded-md bg-zinc-950 font-black shadow-[0_14px_35px_rgba(15,23,42,0.22)] transition hover:-translate-y-0.5 hover:bg-zinc-800 dark:bg-[#a8322b] dark:text-white dark:hover:bg-[#b44136] sm:col-span-2"
                    tabindex="8"
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Crear cuenta y verificar correo
                </Button>
            </div>

            <div class="rounded-md bg-zinc-100 px-4 py-3 text-center text-sm text-muted-foreground dark:bg-white/5">
                Ya tienes acceso?
                <TextLink :href="route('login')" class="underline underline-offset-4" tabindex="9">Ingresar</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
