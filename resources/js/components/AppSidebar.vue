<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BriefcaseBusiness, CalendarDays, ClipboardList, FileText, Home, KeyRound, LayoutGrid, Newspaper, ShieldCheck, Users, Wrench } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const permissions = computed(() => page.props.auth.user?.effective_permissions ?? []);
const can = (permission: string) => permissions.value.includes('*') || permissions.value.includes(permission);

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Panel general',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    ...(can('forms.manage') ? [{
        title: 'Encuestas',
        href: '/surveys',
        icon: ClipboardList,
    }] : []),
    ...(can('employee.requests.create') || can('employee.requests.manage') ? [{
        title: 'Solicitudes',
        href: '/employee-requests',
        icon: FileText,
    }] : []),
    ...(can('appointments.create') || can('appointments.manage') ? [{
        title: 'Citas',
        href: '/appointments',
        icon: CalendarDays,
    }] : []),
    ...(can('events.view') || can('events.manage') ? [{
        title: 'Eventos',
        href: '/events',
        icon: BriefcaseBusiness,
    }] : []),
    ...(can('tools.manage') ? [{
        title: 'Herramientas',
        href: '/tools',
        icon: Wrench,
    }] : []),
    ...(can('news.manage') ? [{
        title: 'Noticias',
        href: '/admin/news',
        icon: Newspaper,
    }] : []),
    ...(can('users.manage') ? [{
        title: 'Usuarios',
        href: '/access/users',
        icon: Users,
    }] : []),
    ...(can('roles.manage') ? [{
        title: 'Roles',
        href: '/access/roles',
        icon: ShieldCheck,
    }] : []),
    ...(can('permissions.manage') ? [{
        title: 'Permisos',
        href: '/access/permissions',
        icon: KeyRound,
    }] : []),
]);

const footerNavItems: NavItem[] = [
    {
        title: 'Volver al sitio',
        href: '/',
        icon: Home,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
