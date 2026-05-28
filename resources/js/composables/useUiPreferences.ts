import { onMounted, ref } from 'vue';

type AccentTone = 'cafe' | 'vino' | 'grafito' | 'arena';
type MenuPlacement = 'top' | 'floating';
type MenuAlignment = 'left' | 'center' | 'right';

export const accentPresets = {
    cafe: {
        label: 'Café',
        base: '#a8322b',
        soft: '#f0c8be',
        border: '#d7a097',
        foreground: '#ffffff',
    },
    vino: {
        label: 'Vino',
        base: '#7f241f',
        soft: '#ead1cc',
        border: '#b67c73',
        foreground: '#ffffff',
    },
    grafito: {
        label: 'Grafito',
        base: '#3f3a38',
        soft: '#e5e1df',
        border: '#b9b1ad',
        foreground: '#ffffff',
    },
    arena: {
        label: 'Arena',
        base: '#6b625d',
        soft: '#f1ece8',
        border: '#c9bdb6',
        foreground: '#ffffff',
    },
} as const satisfies Record<AccentTone, { label: string; base: string; soft: string; border: string; foreground: string }>;

function applyAccentTone(value: AccentTone) {
    if (typeof document === 'undefined') {
        return;
    }

    const preset = accentPresets[value];
    const root = document.documentElement;

    root.style.setProperty('--brand-accent', preset.base);
    root.style.setProperty('--brand-accent-soft', preset.soft);
    root.style.setProperty('--brand-accent-border', preset.border);
    root.style.setProperty('--brand-accent-foreground', preset.foreground);
}

function applyMenuPlacement(value: MenuPlacement) {
    if (typeof document === 'undefined') {
        return;
    }

    document.documentElement.dataset.menuPlacement = value;
}

function applyMenuAlignment(value: MenuAlignment) {
    if (typeof document === 'undefined') {
        return;
    }

    document.documentElement.dataset.menuAlignment = value;
}

export function initializeUiPreferences() {
    const savedAccent = (localStorage.getItem('ui:accent') as AccentTone | null) || 'cafe';
    const savedPlacement = (localStorage.getItem('ui:menuPlacement') as MenuPlacement | null) || 'top';
    const savedAlignment = (localStorage.getItem('ui:menuAlignment') as MenuAlignment | null) || 'center';

    applyAccentTone(savedAccent);
    applyMenuPlacement(savedPlacement);
    applyMenuAlignment(savedAlignment);
}

export function useUiPreferences() {
    const accentTone = ref<AccentTone>('cafe');
    const menuPlacement = ref<MenuPlacement>('top');
    const menuAlignment = ref<MenuAlignment>('center');

    onMounted(() => {
        initializeUiPreferences();
        accentTone.value = (localStorage.getItem('ui:accent') as AccentTone | null) || 'cafe';
        menuPlacement.value = (localStorage.getItem('ui:menuPlacement') as MenuPlacement | null) || 'top';
        menuAlignment.value = (localStorage.getItem('ui:menuAlignment') as MenuAlignment | null) || 'center';
    });

    function updateAccentTone(value: AccentTone) {
        accentTone.value = value;
        localStorage.setItem('ui:accent', value);
        applyAccentTone(value);
    }

    function updateMenuPlacement(value: MenuPlacement) {
        menuPlacement.value = value;
        localStorage.setItem('ui:menuPlacement', value);
        applyMenuPlacement(value);
    }

    function updateMenuAlignment(value: MenuAlignment) {
        menuAlignment.value = value;
        localStorage.setItem('ui:menuAlignment', value);
        applyMenuAlignment(value);
    }

    return {
        accentTone,
        menuPlacement,
        menuAlignment,
        accentPresets,
        updateAccentTone,
        updateMenuPlacement,
        updateMenuAlignment,
    };
}
