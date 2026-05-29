import { onMounted, ref } from 'vue';

export type PortalLayout = 'sidebar' | 'header';

const STORAGE_KEY = 'ui:portalLayout';
const DEFAULT_LAYOUT: PortalLayout = 'sidebar';

export function initializePortalLayout() {
    if (typeof document === 'undefined') {
        return;
    }

    const layout = (localStorage.getItem(STORAGE_KEY) as PortalLayout | null) || DEFAULT_LAYOUT;
    document.documentElement.dataset.portalLayout = layout;
}

export function usePortalLayout() {
    const portalLayout = ref<PortalLayout>(DEFAULT_LAYOUT);

    onMounted(() => {
        initializePortalLayout();
        portalLayout.value = (localStorage.getItem(STORAGE_KEY) as PortalLayout | null) || DEFAULT_LAYOUT;
    });

    function updatePortalLayout(value: PortalLayout) {
        portalLayout.value = value;
        localStorage.setItem(STORAGE_KEY, value);

        if (typeof document !== 'undefined') {
            document.documentElement.dataset.portalLayout = value;
        }
    }

    return {
        portalLayout,
        updatePortalLayout,
    };
}
