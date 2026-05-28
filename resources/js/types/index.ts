import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: {
        location: string;
        url: string;
        port: null | number;
        defaults: Record<string, unknown>;
        routes: Record<string, string>;
    };
}

export interface User {
    id: number;
    name: string;
    identification_type?: string | null;
    identification_number?: string | null;
    identification_label?: string | null;
    phone?: string | null;
    email: string;
    role: 'trabajador' | 'cliente' | 'admin' | 'superadmin';
    roles?: string[];
    permissions?: string[];
    effective_role?: 'trabajador' | 'cliente' | 'admin' | 'superadmin';
    effective_permissions?: string[];
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
