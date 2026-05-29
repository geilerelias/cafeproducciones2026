<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { useInitials } from '@/composables/useInitials';
import { usePortalNavigation } from '@/composables/usePortalNavigation';
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Menu } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);
const { getInitials } = useInitials();
const { mainNavItems, footerNavItems, isActive } = usePortalNavigation();

const activeItemStyles = (href: string) =>
    isActive(href) ? 'bg-[#fff1ee] text-[#7f241f] dark:bg-[#b44136]/20 dark:text-[#f0c8be]' : '';
</script>

<template>
    <div class="border-b border-sidebar-border/80 bg-background">
        <div class="mx-auto flex h-16 max-w-[100vw] items-center gap-3 px-4 lg:px-6">
            <div class="lg:hidden">
                <Sheet>
                    <SheetTrigger as-child>
                        <Button variant="ghost" size="icon" class="h-9 w-9 shrink-0">
                            <Menu class="h-5 w-5" />
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="left" class="w-[min(100vw-2rem,320px)] p-0">
                        <SheetHeader class="border-b border-zinc-200 p-4 text-left dark:border-white/10">
                            <SheetTitle class="sr-only">Menu del portal</SheetTitle>
                            <Link :href="route('dashboard')" class="flex items-center gap-2">
                                <AppLogo />
                            </Link>
                        </SheetHeader>
                        <nav class="flex max-h-[calc(100vh-5rem)] flex-col gap-1 overflow-y-auto p-3">
                            <Link
                                v-for="item in mainNavItems"
                                :key="item.title"
                                :href="item.href"
                                class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-semibold transition hover:bg-zinc-100 dark:hover:bg-white/5"
                                :class="activeItemStyles(item.href)"
                            >
                                <component :is="item.icon" class="h-4 w-4 shrink-0" />
                                {{ item.title }}
                            </Link>
                            <div class="my-2 border-t border-zinc-200 dark:border-white/10"></div>
                            <Link
                                v-for="item in footerNavItems"
                                :key="item.title"
                                :href="item.href"
                                class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-semibold transition hover:bg-zinc-100 dark:hover:bg-white/5"
                            >
                                <component :is="item.icon" class="h-4 w-4 shrink-0" />
                                {{ item.title }}
                            </Link>
                        </nav>
                    </SheetContent>
                </Sheet>
            </div>

            <Link :href="route('dashboard')" class="shrink-0">
                <AppLogo />
            </Link>

            <div class="hidden min-w-0 flex-1 lg:block">
                <NavigationMenu class="max-w-full">
                    <NavigationMenuList class="flex flex-wrap gap-1">
                        <NavigationMenuItem v-for="item in mainNavItems" :key="item.title">
                            <Link :href="item.href">
                                <NavigationMenuLink
                                    :class="[navigationMenuTriggerStyle(), activeItemStyles(item.href), 'h-9 cursor-pointer px-3 text-sm font-semibold']"
                                >
                                    <component :is="item.icon" class="mr-2 h-4 w-4" />
                                    {{ item.title }}
                                </NavigationMenuLink>
                            </Link>
                        </NavigationMenuItem>
                    </NavigationMenuList>
                </NavigationMenu>
            </div>

            <div class="ml-auto flex shrink-0 items-center gap-2">
                <Link
                    v-for="item in footerNavItems"
                    :key="item.title"
                    :href="item.href"
                    class="hidden rounded-md px-3 py-2 text-sm font-semibold text-zinc-600 transition hover:text-zinc-950 md:inline-flex dark:text-zinc-300 dark:hover:text-white"
                >
                    {{ item.title }}
                </Link>

                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="ghost" size="icon" class="relative size-10 w-auto rounded-full p-1">
                            <Avatar class="size-8 overflow-hidden rounded-full">
                                <AvatarImage v-if="auth.user?.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                                <AvatarFallback class="rounded-full bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                                    {{ getInitials(auth.user?.name ?? '') }}
                                </AvatarFallback>
                            </Avatar>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        <UserMenuContent v-if="auth.user" :user="auth.user" />
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>

        <div v-if="props.breadcrumbs.length > 0" class="border-t border-sidebar-border/70 px-4 py-2 lg:px-6">
            <Breadcrumb>
                <BreadcrumbList>
                    <template v-for="(item, index) in props.breadcrumbs" :key="index">
                        <BreadcrumbItem>
                            <BreadcrumbLink v-if="index < props.breadcrumbs.length - 1" :href="item.href">
                                {{ item.title }}
                            </BreadcrumbLink>
                            <BreadcrumbPage v-else>{{ item.title }}</BreadcrumbPage>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator v-if="index < props.breadcrumbs.length - 1" />
                    </template>
                </BreadcrumbList>
            </Breadcrumb>
        </div>
    </div>
</template>
