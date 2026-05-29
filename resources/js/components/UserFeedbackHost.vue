<script setup lang="ts">
import UserFeedbackDialog from '@/components/UserFeedbackDialog.vue';
import { useFlashFeedback } from '@/composables/useUserFeedback';

useFlashFeedback();
</script>

<template>
    <slot />
    <UserFeedbackDialog />
</template>
