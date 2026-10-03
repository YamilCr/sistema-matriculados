<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { computed } from 'vue'; // Importamos computed para que sea reactivo
import { usePage } from '@inertiajs/vue3'; // Importante para acceder a auth
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import report from '@/routes/report';
import admin from '@/routes/admin';
import users from '@/routes/users';



const page = usePage();
const user = computed(() => page.props.auth.user);

const mainNavItems = computed(() => {
    const items: NavItem[] = [
        {
            title: 'Inicio',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    // Si el role_id es 1 (Admin) o 3 (Staff), agregamos los botones extra 
    if (user.value && (user.value.id === 1 || user.value.id === 3)) {
        items.push(
            {
                title: 'Matriculados',
                href: admin.search(), 
                icon: LayoutGrid,
            },
            {
                title: 'Reportes',
                href: report.index(), 
                icon: LayoutGrid,
            }   
        );
    }

    if (user.value && user.value.id === 1) {
        items.push(
            {
                title: 'Gestion de Usuarios',
                href: users.index(), 
                icon: LayoutGrid,
            }
        );
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
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
