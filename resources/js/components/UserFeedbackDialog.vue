<script setup lang="ts">
import { useUserFeedback, type UserFeedbackType } from '@/composables/useUserFeedback';
import { AlertTriangle, CheckCircle2, Info, X, XCircle } from 'lucide-vue-next';
import { computed } from 'vue';

const { state, close } = useUserFeedback();

const iconMap: Record<UserFeedbackType, typeof CheckCircle2> = {
    success: CheckCircle2,
    error: XCircle,
    warning: AlertTriangle,
    info: Info,
};

const toneMap: Record<UserFeedbackType, { ring: string; icon: string }> = {
    success: { ring: 'bg-green-50 text-green-600', icon: 'bg-green-50 text-green-600' },
    error: { ring: 'bg-red-50 text-red-600', icon: 'bg-red-50 text-red-600' },
    warning: { ring: 'bg-amber-50 text-amber-600', icon: 'bg-amber-50 text-amber-600' },
    info: { ring: 'bg-sky-50 text-sky-600', icon: 'bg-sky-50 text-sky-600' },
};

const Icon = computed(() => iconMap[state.type]);
const tone = computed(() => toneMap[state.type]);
</script>

<template>
    <Teleport to="body">
        <div
            v-if="state.open"
            class="fixed inset-0 z-[200] grid place-items-center bg-zinc-950/60 px-4 backdrop-blur-sm"
            role="alertdialog"
            aria-modal="true"
            :aria-labelledby="state.title ? 'user-feedback-title' : undefined"
            @keydown.escape="close"
        >
            <div class="w-full max-w-sm rounded-md bg-white p-6 text-center shadow-[0_24px_80px_rgba(15,23,42,0.35)] dark:bg-zinc-900">
                <div class="flex justify-end">
                    <button
                        type="button"
                        class="grid h-9 w-9 place-items-center rounded-full text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-950 dark:hover:bg-zinc-800 dark:hover:text-white"
                        aria-label="Cerrar mensaje"
                        @click="close"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>
                <div class="mx-auto -mt-2 grid h-20 w-20 place-items-center rounded-full" :class="tone.icon">
                    <component :is="Icon" class="h-11 w-11" />
                </div>
                <h2 id="user-feedback-title" class="mt-5 text-2xl font-black text-zinc-950 dark:text-white">{{ state.title }}</h2>
                <p class="mt-3 text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ state.message }}</p>
                <button
                    type="button"
                    class="mt-6 w-full rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200"
                    @click="close"
                >
                    Aceptar
                </button>
            </div>
        </div>
    </Teleport>
</template>
