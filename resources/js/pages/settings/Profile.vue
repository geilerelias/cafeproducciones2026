<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle, Trash2, Upload } from 'lucide-vue-next';
import { ref } from 'vue';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Perfil', href: '/settings/profile' }];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const { getInitials } = useInitials();

const form = useForm({
    name: user.name,
    email: user.email,
});

const photoForm = useForm<{ photo: File | null }>({
    photo: null,
});

const photoInput = ref<HTMLInputElement | null>(null);

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};

const selectPhoto = () => {
    photoInput.value?.click();
};

const uploadPhoto = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) {
        return;
    }

    photoForm
        .transform((data) => ({
            photo: file,
        }))
        .post(route('profile.photo.store'), {
            preserveScroll: true,
            forceFormData: true,
            onFinish: () => {
                photoForm.reset();
                if (photoInput.value) {
                    photoInput.value.value = '';
                }
            },
        });
};

const removePhoto = () => {
    photoForm.delete(route('profile.photo.destroy'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Perfil" />

        <SettingsLayout>
            <div class="flex flex-col space-y-8">
                <HeadingSmall title="Informacion del perfil" description="Actualiza tu foto, nombre y correo electronico." />

                <section class="rounded-md border border-zinc-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-zinc-900">
                    <h3 class="text-sm font-black uppercase tracking-wide text-zinc-500">Foto de perfil</h3>
                    <div class="mt-4 flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                        <Avatar class="h-20 w-20 rounded-xl">
                            <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                            <AvatarFallback class="rounded-xl text-lg font-black">{{ getInitials(user.name) }}</AvatarFallback>
                        </Avatar>
                        <div class="flex flex-wrap gap-2">
                            <input ref="photoInput" type="file" accept="image/*" class="hidden" @change="uploadPhoto" />
                            <Button type="button" variant="outline" :disabled="photoForm.processing" @click="selectPhoto">
                                <Upload class="mr-2 h-4 w-4" />
                                Subir foto
                            </Button>
                            <Button v-if="user.avatar" type="button" variant="ghost" :disabled="photoForm.processing" @click="removePhoto">
                                <Trash2 class="mr-2 h-4 w-4" />
                                Eliminar
                            </Button>
                            <LoaderCircle v-if="photoForm.processing" class="h-5 w-5 animate-spin text-zinc-400" />
                        </div>
                    </div>
                    <InputError class="mt-2" :message="photoForm.errors.photo" />
                    <p v-if="status === 'profile-photo-updated'" class="mt-2 text-sm text-emerald-600">Foto actualizada.</p>
                    <p v-if="status === 'profile-photo-deleted'" class="mt-2 text-sm text-emerald-600">Foto eliminada.</p>
                </section>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Nombre completo</Label>
                        <Input id="name" v-model="form.name" required autocomplete="name" placeholder="Nombre y apellido" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Correo electronico</Label>
                        <Input id="email" v-model="form.email" type="email" required autocomplete="username" placeholder="usuario@empresa.com" />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="text-sm text-zinc-700 dark:text-zinc-300">
                            Tu correo no esta verificado.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="font-semibold text-[#a8322b] underline underline-offset-4"
                            >
                                Reenviar enlace de verificacion
                            </Link>
                        </p>
                        <p v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-emerald-600">
                            Se envio un nuevo enlace de verificacion.
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Guardar cambios</Button>
                        <TransitionRoot
                            :show="form.recentlySuccessful"
                            enter="transition ease-in-out"
                            enter-from="opacity-0"
                            leave="transition ease-in-out"
                            leave-to="opacity-0"
                        >
                            <p class="text-sm text-zinc-500">Guardado.</p>
                        </TransitionRoot>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
