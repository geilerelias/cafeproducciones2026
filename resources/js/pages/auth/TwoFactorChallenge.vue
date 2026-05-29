<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/auth/AuthSimpleLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const useRecoveryCode = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const submit = () => {
    form.post(route('two-factor.login.store'), {
        onFinish: () => {
            if (!useRecoveryCode.value) {
                form.reset('code');
            } else {
                form.reset('recovery_code');
            }
        },
    });
};
</script>

<template>
    <AuthBase title="Verificacion en dos pasos" description="Confirma tu identidad con el codigo de tu aplicacion de autenticacion o un codigo de recuperacion.">
        <Head title="Verificacion en dos pasos" />

        <form @submit.prevent="submit" class="space-y-6">
            <div v-if="!useRecoveryCode" class="grid gap-2">
                <Label for="code">Codigo de autenticacion</Label>
                <Input
                    id="code"
                    v-model="form.code"
                    type="text"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    autofocus
                    required
                    placeholder="000000"
                    class="h-12 text-center text-lg tracking-[0.35em]"
                />
                <InputError :message="form.errors.code" />
            </div>

            <div v-else class="grid gap-2">
                <Label for="recovery_code">Codigo de recuperacion</Label>
                <Input
                    id="recovery_code"
                    v-model="form.recovery_code"
                    type="text"
                    autocomplete="off"
                    autofocus
                    required
                    placeholder="xxxx-xxxx"
                    class="h-12"
                />
                <InputError :message="form.errors.recovery_code" />
            </div>

            <Button type="submit" class="h-12 w-full font-black" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Continuar al panel
            </Button>

            <button
                type="button"
                class="w-full text-center text-sm font-semibold text-[#a8322b] underline underline-offset-4"
                @click="useRecoveryCode = !useRecoveryCode"
            >
                {{ useRecoveryCode ? 'Usar codigo de la aplicacion' : 'Usar codigo de recuperacion' }}
            </button>
        </form>
    </AuthBase>
</template>
