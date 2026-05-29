import type { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { reactive, readonly, watch } from 'vue';

export type UserFeedbackType = 'success' | 'error' | 'warning' | 'info';

export type UserFeedbackPayload = {
    type: UserFeedbackType;
    title: string;
    message: string;
};

type UserFeedbackState = UserFeedbackPayload & {
    open: boolean;
};

const state = reactive<UserFeedbackState>({
    open: false,
    type: 'success',
    title: '',
    message: '',
});

export function useUserFeedback() {
    const open = (type: UserFeedbackType, title: string, message: string) => {
        state.type = type;
        state.title = title;
        state.message = message;
        state.open = true;
    };

    return {
        state: readonly(state),
        open,
        close: () => {
            state.open = false;
        },
        showSuccess: (title: string, message: string) => open('success', title, message),
        showError: (title: string, message: string) => open('error', title, message),
        showWarning: (title: string, message: string) => open('warning', title, message),
        showInfo: (title: string, message: string) => open('info', title, message),
    };
}

function resolveFlashFeedback(flash: SharedData['flash']): UserFeedbackPayload | null {
    if (!flash) {
        return null;
    }

    if (flash.feedback?.message) {
        return flash.feedback;
    }

    if (flash.success) {
        return {
            type: 'success',
            title: flash.success_title ?? 'Listo',
            message: flash.success,
        };
    }

    if (flash.error) {
        return {
            type: 'error',
            title: flash.error_title ?? 'No se pudo completar',
            message: flash.error,
        };
    }

    return null;
}

export function useFlashFeedback() {
    const page = usePage<SharedData>();
    const feedback = useUserFeedback();

    watch(
        () => page.props?.flash,
        (flash) => {
            const payload = resolveFlashFeedback(flash);

            if (!payload) {
                return;
            }

            feedback.open(payload.type, payload.title, payload.message);
        },
        { deep: true, immediate: true },
    );

    return feedback;
}

export function useFormFeedback() {
    const feedback = useUserFeedback();

    return {
        onSuccess: (title: string, message: string) => () => feedback.showSuccess(title, message),
        onError: (title: string, fallbackMessage = 'Revisa los campos marcados e intenta nuevamente.') => {
            return (errors: Record<string, string>) => {
                const firstError = Object.values(errors)[0];

                feedback.showError(title, firstError ?? fallbackMessage);
            };
        },
    };
}
