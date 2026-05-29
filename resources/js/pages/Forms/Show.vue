<script setup lang="ts">
import MarketingLayout from '@/layouts/MarketingLayout.vue';
import { useForm } from '@inertiajs/vue3';

type Field = {
    label: string;
    key: string;
    type: 'text' | 'number' | 'date' | 'select' | 'textarea';
    required: boolean;
    options: string[];
};

type FormDefinition = {
    title: string;
    slug: string;
    description: string | null;
    fields: Field[];
    submit_label: string;
    success_message: string;
};

const props = defineProps<{ formDefinition: FormDefinition }>();

const responseForm = useForm({
    answers: props.formDefinition.fields.reduce(
        (carry, field) => ({
            ...carry,
            [field.key]: '',
        }),
        {} as Record<string, string>,
    ),
});

const submit = () => {
    responseForm.post(route('forms.public.submit', props.formDefinition.slug), {
        preserveScroll: true,
        onSuccess: () => responseForm.reset(),
    });
};
</script>

<template>
    <MarketingLayout :title="formDefinition.title" :description="formDefinition.description || 'Formulario publico de CAFE Producciones.'">
        <section class="bg-zinc-950 py-20 text-white">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <p class="text-sm font-black uppercase tracking-[0.2em] text-[#f0c8be]">Formulario</p>
                <h1 class="mt-4 text-4xl font-black sm:text-5xl">{{ formDefinition.title }}</h1>
                <p v-if="formDefinition.description" class="mt-5 text-lg leading-8 text-zinc-300">{{ formDefinition.description }}</p>
            </div>
        </section>

        <section class="mx-auto max-w-3xl px-4 py-14 sm:px-6 lg:px-8">
            <form class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm" @submit.prevent="submit">
                <div class="grid gap-4">
                    <label v-for="field in formDefinition.fields" :key="field.key" class="grid gap-2 text-sm font-bold">
                        {{ field.label }}
                        <textarea v-if="field.type === 'textarea'" v-model="responseForm.answers[field.key]" rows="5" class="rounded-md border border-zinc-300 px-3 py-3 font-normal outline-none focus:border-[#a8322b]" :required="field.required"></textarea>
                        <select v-else-if="field.type === 'select'" v-model="responseForm.answers[field.key]" class="rounded-md border border-zinc-300 px-3 py-3 font-normal outline-none focus:border-[#a8322b]" :required="field.required">
                            <option value="">Selecciona una opcion</option>
                            <option v-for="option in field.options" :key="option" :value="option">{{ option }}</option>
                        </select>
                        <input v-else v-model="responseForm.answers[field.key]" :type="field.type" class="rounded-md border border-zinc-300 px-3 py-3 font-normal outline-none focus:border-[#a8322b]" :required="field.required" />
                        <span v-if="responseForm.errors[`answers.${field.key}`]" class="text-xs font-bold text-red-700">{{ responseForm.errors[`answers.${field.key}`] }}</span>
                    </label>

                    <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-black text-white disabled:opacity-60" :disabled="responseForm.processing">
                        {{ responseForm.processing ? 'Enviando...' : formDefinition.submit_label }}
                    </button>
                </div>
            </form>
        </section>
    </MarketingLayout>
</template>
