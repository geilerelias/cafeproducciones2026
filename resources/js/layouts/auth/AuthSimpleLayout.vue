<script setup lang="ts">
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import { Link } from '@inertiajs/vue3';
import { CalendarCheck, ClipboardList, Sparkles } from 'lucide-vue-next';
import authLogo from '../../../images/logoCaFe.png';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="mesh-bg relative min-h-svh overflow-hidden p-4 text-zinc-950 dark:text-white sm:p-6 lg:p-8">
        <div class="absolute left-1/2 top-10 h-64 w-64 -translate-x-1/2 rounded-full bg-[#b44136]/20 blur-3xl"></div>
        <div class="relative mx-auto flex min-h-[calc(100svh-2rem)] max-w-6xl items-center justify-center">
            <div
                class="grid w-full overflow-hidden rounded-md border border-white/70 bg-white/65 shadow-[0_30px_100px_rgba(15,23,42,0.18)] backdrop-blur-2xl dark:border-white/10 dark:bg-zinc-950/75 lg:grid-cols-[1.05fr_0.95fr]"
            >
                <section class="relative hidden min-h-[760px] overflow-hidden bg-zinc-950 p-10 text-white lg:flex lg:flex-col">
                    <div
                        class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(164,50,43,0.28),transparent_24rem),radial-gradient(circle_at_80%_80%,rgba(107,98,93,0.18),transparent_22rem)]"
                    ></div>
                    <div class="relative z-10">
                        <Link :href="route('home')" class="inline-flex items-center gap-3">
                            <img :src="authLogo" alt="CAFE Producciones" class="h-14 w-14 rounded-md object-contain shadow-lg ring-1 ring-white/10" />
                            <span>
                                <span class="block text-sm font-black uppercase tracking-wide">CAFE Producciones</span>
                                <span class="block text-xs text-zinc-400">Acceso operativo</span>
                            </span>
                        </Link>

                        <div class="mt-20 max-w-md">
                            <p class="text-sm font-black uppercase tracking-[0.25em] text-[#f0c8be]">Gestion inteligente</p>
                            <h2 class="mt-4 text-4xl font-black leading-tight">
                                Gestiona procesos, citas y seguimiento operativo desde un solo acceso
                            </h2>
                            <p class="mt-5 text-sm leading-7 text-zinc-300">
                                Clientes, administradores y equipo interno entran al mismo entorno para coordinar solicitudes, asignar tareas, revisar
                                avances y medir resultados.
                            </p>
                        </div>
                    </div>

                    <div class="relative z-10 mt-auto grid gap-3">
                        <div class="rounded-md border border-white/10 bg-white/10 p-4 backdrop-blur">
                            <CalendarCheck class="h-5 w-5 text-[#f0c8be]" />
                            <p class="mt-3 text-sm font-black">Agenda y tareas</p>
                            <p class="mt-1 text-xs leading-5 text-zinc-400">Solicitudes, asignaciones y seguimientos en un flujo claro.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-md border border-white/10 bg-white/10 p-4 backdrop-blur">
                                <ClipboardList class="h-5 w-5 text-[#cdb6ad]" />
                                <p class="mt-3 text-sm font-black">Encuestas</p>
                            </div>
                            <div class="rounded-md border border-white/10 bg-white/10 p-4 backdrop-blur">
                                <Sparkles class="h-5 w-5 text-[#e7b0aa]" />
                                <p class="mt-3 text-sm font-black">Operacion</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    class="relative flex min-h-0 flex-col justify-center bg-white/90 p-5 text-zinc-950 dark:bg-zinc-950/90 dark:text-white sm:min-h-[640px] sm:p-10 lg:min-h-[760px]"
                >
                    <div class="absolute right-6 top-6">
                        <AppearanceTabs />
                    </div>

                    <div class="mx-auto w-full max-w-md">
                        <div class="mb-8 flex items-center gap-3 lg:hidden">
                            <Link :href="route('home')" class="flex items-center gap-3 font-medium">
                                <img
                                    :src="authLogo"
                                    alt="CAFE Producciones"
                                    class="h-11 w-11 rounded-md object-contain shadow-lg ring-1 ring-black/10 dark:ring-white/10"
                                />
                                <span>
                                    <span class="block text-sm font-black uppercase tracking-wide">CAFE Producciones</span>
                                    <span class="block text-xs text-zinc-500 dark:text-zinc-400">Acceso operativo</span>
                                </span>
                            </Link>
                        </div>

                        <div class="mb-8 space-y-2 text-center lg:text-left">
                            <p class="text-xs font-black uppercase tracking-[0.25em] text-[#a8322b]">Acceso privado</p>
                            <h1 class="text-3xl font-black tracking-tight">{{ title }}</h1>
                            <p class="text-sm leading-6 text-zinc-500 dark:text-zinc-400">{{ description }}</p>
                        </div>

                        <slot />
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
