<script setup lang="ts">
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { accentPresets, useUiPreferences } from '@/composables/useUiPreferences';
import { Menu, MoveHorizontal, Palette, Settings2 } from 'lucide-vue-next';

const { accentTone, menuPlacement, menuAlignment, updateAccentTone, updateMenuAlignment, updateMenuPlacement } = useUiPreferences();

const menuPlacementOptions = [
    { value: 'top', label: 'Superior' },
    { value: 'floating', label: 'Flotante' },
] as const;

const menuAlignmentOptions = [
    { value: 'left', label: 'Izquierda' },
    { value: 'center', label: 'Centro' },
    { value: 'right', label: 'Derecha' },
] as const;
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button
                class="brand-accent-bg fixed right-4 top-1/2 z-50 h-12 w-12 -translate-y-1/2 rounded-full p-0 shadow-[0_18px_50px_rgba(15,23,42,0.25)] sm:right-5"
                aria-label="Abrir personalizacion"
                title="Personalizar menu"
            >
                <Settings2 class="h-5 w-5" />
            </Button>
        </DialogTrigger>

        <DialogContent
            class="max-h-[90vh] w-[min(92vw,42rem)] overflow-y-auto border-zinc-200 bg-white text-zinc-950 dark:border-white/10 dark:bg-zinc-950 dark:text-white"
        >
            <DialogHeader class="space-y-2">
                <DialogTitle class="flex items-center gap-2 text-xl font-black">
                    <Menu class="h-5 w-5 text-[#a8322b]" />
                    Personalizacion del menu
                </DialogTitle>
                <DialogDescription class="text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                    Ajusta el tema, el tono de énfasis y la posición del menú desde este panel flotante.
                </DialogDescription>
            </DialogHeader>

            <div class="mt-6 grid gap-6">
                <section class="rounded-md border border-zinc-200 bg-zinc-50 p-4 dark:border-white/10 dark:bg-white/5">
                    <div class="mb-3 flex items-center gap-2 text-sm font-black uppercase tracking-[0.18em] text-zinc-500">
                        <Palette class="h-4 w-4" />
                        Tema
                    </div>
                    <AppearanceTabs />
                </section>

                <section class="rounded-md border border-zinc-200 bg-zinc-50 p-4 dark:border-white/10 dark:bg-white/5">
                    <div class="mb-3 flex items-center gap-2 text-sm font-black uppercase tracking-[0.18em] text-zinc-500">
                        <MoveHorizontal class="h-4 w-4" />
                        Posicion del menu
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            v-for="option in menuPlacementOptions"
                            :key="option.value"
                            type="button"
                            class="rounded-md border px-3 py-2 text-sm font-bold transition"
                            :class="
                                menuPlacement === option.value
                                    ? 'brand-accent-bg border-transparent shadow-md'
                                    : 'border-zinc-200 bg-white text-zinc-700 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-200'
                            "
                            @click="updateMenuPlacement(option.value)"
                        >
                            {{ option.label }}
                        </button>
                    </div>
                    <div class="mt-4 grid gap-2 sm:grid-cols-3">
                        <button
                            v-for="option in menuAlignmentOptions"
                            :key="option.value"
                            type="button"
                            class="rounded-md border px-3 py-2 text-xs font-black uppercase tracking-wide transition"
                            :class="
                                menuAlignment === option.value
                                    ? 'brand-accent-soft-bg border-transparent text-zinc-950'
                                    : 'border-zinc-200 bg-white text-zinc-700 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-200'
                            "
                            @click="updateMenuAlignment(option.value)"
                        >
                            {{ option.label }}
                        </button>
                    </div>
                </section>

                <section class="rounded-md border border-zinc-200 bg-zinc-50 p-4 dark:border-white/10 dark:bg-white/5">
                    <div class="mb-3 text-sm font-black uppercase tracking-[0.18em] text-zinc-500">Acento</div>
                    <div class="grid gap-3 sm:grid-cols-4">
                        <button
                            v-for="(preset, key) in accentPresets"
                            :key="key"
                            type="button"
                            class="rounded-md border p-3 text-left transition"
                            :class="
                                accentTone === key ? 'border-transparent shadow-lg' : 'border-zinc-200 bg-white dark:border-white/10 dark:bg-zinc-950'
                            "
                            :style="accentTone === key ? { backgroundColor: preset.base, color: preset.foreground } : undefined"
                            @click="updateAccentTone(key)"
                        >
                            <span class="block h-2.5 w-10 rounded-full" :style="{ backgroundColor: preset.base }"></span>
                            <span class="mt-3 block text-sm font-black">{{ preset.label }}</span>
                        </button>
                    </div>
                </section>
            </div>
        </DialogContent>
    </Dialog>
</template>
