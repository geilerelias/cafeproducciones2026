<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { LoaderCircle, ShieldCheck } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    twoFactorEnabled: boolean;
    twoFactorPending: boolean;
    status?: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Seguridad', href: '/settings/security' }];

const password = ref('');
const confirmationCode = ref('');
const qrSvg = ref('');
const recoveryCodes = ref<string[]>([]);
const loading = ref(false);
const errorMessage = ref('');
const step = ref<'idle' | 'qr' | 'recovery'>('idle');

const csrfToken = () => document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

const fortifyRequest = async (url: string, options: RequestInit = {}) => {
    const response = await fetch(url, {
        ...options,
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
            ...(options.headers ?? {}),
        },
        credentials: 'same-origin',
    });

    if (!response.ok) {
        const data = await response.json().catch(() => ({}));
        const validationMessage =
            (data.errors && (Object.values(data.errors).flat()[0] as string | undefined)) || data.message;
        throw new Error(validationMessage ?? 'No se pudo completar la operacion.');
    }

    const contentType = response.headers.get('content-type') ?? '';

    if (contentType.includes('application/json')) {
        return response.json();
    }

    return {};
};

const startSetup = async () => {
    errorMessage.value = '';
    loading.value = true;

    try {
        await fortifyRequest('/user/two-factor-authentication', {
            method: 'POST',
            body: JSON.stringify({ password: password.value }),
        });

        const qr = await fortifyRequest('/user/two-factor-qr-code');
        qrSvg.value = qr.svg ?? '';
        step.value = 'qr';
        confirmationCode.value = '';
    } catch (error) {
        errorMessage.value = error instanceof Error ? error.message : 'Error al activar 2FA.';
    } finally {
        loading.value = false;
    }
};

const confirmSetup = async () => {
    errorMessage.value = '';
    loading.value = true;

    try {
        await fortifyRequest('/user/confirmed-two-factor-authentication', {
            method: 'POST',
            body: JSON.stringify({ code: confirmationCode.value }),
        });

        const codes = await fortifyRequest('/user/two-factor-recovery-codes');
        recoveryCodes.value = Array.isArray(codes) ? codes : (codes.recoveryCodes ?? []);
        step.value = 'recovery';
        router.reload({ only: ['twoFactorEnabled', 'twoFactorPending'] });
    } catch (error) {
        errorMessage.value = error instanceof Error ? error.message : 'Codigo invalido.';
    } finally {
        loading.value = false;
    }
};

const disableTwoFactor = async () => {
    errorMessage.value = '';
    loading.value = true;

    try {
        await fortifyRequest('/user/two-factor-authentication', {
            method: 'DELETE',
            body: JSON.stringify({ password: password.value }),
        });

        password.value = '';
        step.value = 'idle';
        password.value = '';
        router.reload({ only: ['twoFactorEnabled', 'twoFactorPending', 'auth'] });
    } catch (error) {
        errorMessage.value = error instanceof Error ? error.message : 'No se pudo desactivar 2FA.';
    } finally {
        loading.value = false;
    }
};

const regenerateRecoveryCodes = async () => {
    errorMessage.value = '';
    loading.value = true;

    try {
        await fortifyRequest('/user/two-factor-recovery-codes', {
            method: 'POST',
            body: JSON.stringify({ password: password.value }),
        });

        const codes = await fortifyRequest('/user/two-factor-recovery-codes');
        recoveryCodes.value = Array.isArray(codes) ? codes : (codes.recoveryCodes ?? []);
        step.value = 'recovery';
    } catch (error) {
        errorMessage.value = error instanceof Error ? error.message : 'No se pudieron regenerar los codigos.';
    } finally {
        loading.value = false;
    }
};

const showEnabledPanel = computed(() => props.twoFactorEnabled && step.value === 'idle');
const showSetupPanel = computed(() => !props.twoFactorEnabled && step.value !== 'recovery');
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Seguridad de la cuenta" />

        <SettingsLayout>
            <div class="space-y-8">
                <HeadingSmall
                    title="Seguridad de la cuenta"
                    description="Protege tu acceso con autenticacion en dos factores. Recomendado para administradores y personal operativo."
                />

                <div
                    v-if="status === 'two-factor-enabled'"
                    class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
                >
                    Autenticacion en dos factores activada correctamente.
                </div>

                <section class="rounded-md border border-zinc-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-zinc-900">
                    <div class="flex items-start gap-4">
                        <div class="rounded-md bg-[#fff1ee] p-3 text-[#a8322b]">
                            <ShieldCheck class="h-6 w-6" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-black">Autenticacion en dos factores (2FA)</h3>
                            <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                                Al activarla, cada inicio de sesion pedira un codigo de tu app (Google Authenticator, Microsoft Authenticator, etc.).
                            </p>
                            <p
                                class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-black"
                                :class="twoFactorEnabled ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-100 text-zinc-700'"
                            >
                                {{ twoFactorEnabled ? 'Activa' : twoFactorPending ? 'Pendiente de confirmar' : 'Inactiva' }}
                            </p>
                        </div>
                    </div>

                    <div v-if="errorMessage" class="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ errorMessage }}
                    </div>

                    <div v-if="showEnabledPanel" class="mt-6 space-y-4">
                        <p class="text-sm text-zinc-600">Tu cuenta esta protegida con 2FA.</p>
                        <div class="grid gap-2 max-w-sm">
                            <Label for="disable-password">Contrasena para desactivar</Label>
                            <Input id="disable-password" v-model="password" type="password" autocomplete="current-password" />
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <Button variant="outline" :disabled="loading" @click="regenerateRecoveryCodes">Regenerar codigos de recuperacion</Button>
                            <Button variant="destructive" :disabled="loading" @click="disableTwoFactor">Desactivar 2FA</Button>
                        </div>
                    </div>

                    <div v-else-if="showSetupPanel" class="mt-6 space-y-4">
                        <div v-if="step === 'idle'" class="space-y-4">
                            <div class="grid gap-2 max-w-sm">
                                <Label for="setup-password">Confirma tu contrasena</Label>
                                <Input id="setup-password" v-model="password" type="password" autocomplete="current-password" />
                            </div>
                            <Button :disabled="loading || !password" @click="startSetup">
                                <LoaderCircle v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
                                Activar autenticacion en dos factores
                            </Button>
                        </div>

                        <div v-if="step === 'qr'" class="space-y-4">
                            <p class="text-sm text-zinc-600">Escanea este codigo QR con tu aplicacion de autenticacion y luego ingresa el codigo de 6 digitos.</p>
                            <div class="inline-block rounded-md border border-zinc-200 bg-white p-4" v-html="qrSvg"></div>
                            <div class="grid gap-2 max-w-xs">
                                <Label for="confirmation-code">Codigo de verificacion</Label>
                                <Input
                                    id="confirmation-code"
                                    v-model="confirmationCode"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    placeholder="000000"
                                    class="text-center tracking-[0.3em]"
                                />
                            </div>
                            <Button :disabled="loading || confirmationCode.length < 6" @click="confirmSetup">
                                <LoaderCircle v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
                                Confirmar y activar
                            </Button>
                        </div>
                    </div>

                    <div v-if="step === 'recovery' && recoveryCodes.length" class="mt-6 space-y-4">
                        <p class="text-sm font-semibold text-zinc-800">Guarda estos codigos de recuperacion en un lugar seguro. Cada uno solo se puede usar una vez.</p>
                        <ul class="grid gap-2 rounded-md bg-zinc-50 p-4 font-mono text-sm dark:bg-white/5 sm:grid-cols-2">
                            <li v-for="code in recoveryCodes" :key="code">{{ code }}</li>
                        </ul>
                        <Button
                            variant="outline"
                            @click="
                                step = 'idle';
                                router.reload({ only: ['twoFactorEnabled', 'twoFactorPending', 'auth'] });
                            "
                        >
                            Entendido
                        </Button>
                    </div>
                </section>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
