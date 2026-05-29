import type { NavItem, SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { BriefcaseBusiness, CalendarDays, ClipboardList, FileText, Home, KeyRound, LayoutGrid, Newspaper, PartyPopper, ShieldCheck, Users, Wrench } from 'lucide-vue-next';
import { computed } from 'vue';

export function usePortalNavigation() {
    const page = usePage<SharedData>();
    const permissions = computed(() => page.props.auth.user?.effective_permissions ?? []);
    const can = (permission: string) => permissions.value.includes('*') || permissions.value.includes(permission);

    const mainNavItems = computed<NavItem[]>(() => [
        {
            title: 'Panel general',
            href: '/dashboard',
            icon: LayoutGrid,
        },
        ...(can('forms.manage')
            ? [
                  {
                      title: 'Encuestas',
                      href: '/surveys',
                      icon: ClipboardList,
                  },
              ]
            : []),
        ...(can('employee.requests.create') || can('employee.requests.manage')
            ? [
                  {
                      title: 'Solicitudes RRHH',
                      href: '/employee-requests',
                      icon: FileText,
                  },
              ]
            : []),
        ...(can('event.requests.create') || can('event.requests.view-own') || can('event.requests.manage') || can('event.requests.tasks.view-assigned')
            ? [
                  {
                      title: can('event.requests.manage')
                          ? 'Solicitudes de eventos'
                          : can('event.requests.tasks.view-assigned') && !can('event.requests.view-own')
                            ? 'Tareas de eventos'
                            : 'Mis eventos',
                      href: '/event-requests',
                      icon: PartyPopper,
                  },
              ]
            : []),
        ...(can('event.requests.manage')
            ? [
                  {
                      title: 'Etapas de eventos',
                      href: '/admin/event-request-stages',
                      icon: ClipboardList,
                  },
              ]
            : []),
        ...(can('appointments.create') || can('appointments.manage')
            ? [
                  {
                      title: 'Citas',
                      href: '/appointments',
                      icon: CalendarDays,
                  },
              ]
            : []),
        ...(can('events.view') || can('events.manage')
            ? [
                  {
                      title: 'Eventos',
                      href: '/events',
                      icon: BriefcaseBusiness,
                  },
              ]
            : []),
        ...(can('tools.manage')
            ? [
                  {
                      title: 'Herramientas',
                      href: '/tools',
                      icon: Wrench,
                  },
              ]
            : []),
        ...(can('news.manage')
            ? [
                  {
                      title: 'Noticias',
                      href: '/admin/news',
                      icon: Newspaper,
                  },
              ]
            : []),
        ...(can('users.manage')
            ? [
                  {
                      title: 'Usuarios',
                      href: '/access/users',
                      icon: Users,
                  },
              ]
            : []),
        ...(can('roles.manage')
            ? [
                  {
                      title: 'Roles',
                      href: '/access/roles',
                      icon: ShieldCheck,
                  },
              ]
            : []),
        ...(can('permissions.manage')
            ? [
                  {
                      title: 'Permisos',
                      href: '/access/permissions',
                      icon: KeyRound,
                  },
              ]
            : []),
    ]);

    const footerNavItems: NavItem[] = [
        {
            title: 'Volver al sitio',
            href: '/',
            icon: Home,
        },
    ];

    const isActive = (href: string) => {
        const path = page.url.split('?')[0];

        return path === href || (href !== '/' && path.startsWith(href));
    };

    return {
        mainNavItems,
        footerNavItems,
        isActive,
    };
}
