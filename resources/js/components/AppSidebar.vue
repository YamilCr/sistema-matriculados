<script setup lang="ts">
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
import { CreditCard, FileBarChart, Landmark, LayoutGrid, MapPin, ShieldCheck, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import report from '@/routes/report';
import admin from '@/routes/admin';
import users from '@/routes/users';
import provinces from '@/routes/provinces';
import cities from '@/routes/cities';
import accountstatuses from '@/routes/accountstatuses';

const ROLE_ADMIN = 1;
const ROLE_STAFF = 3;

const page = usePage();
const user = computed(() => page.props.auth.user);

const mainNavItems = computed(() => {
    const items: NavItem[] = [
        { title: 'Inicio', href: dashboard(), icon: LayoutGrid },
    ];

    const roleId = user.value?.role_id;

    // Admin y Staff: matriculados y reportes
    if (roleId === ROLE_ADMIN || roleId === ROLE_STAFF) {
        items.push(
            { title: 'Matriculados', href: admin.search(), icon: Users },
            { title: 'Reportes', href: report.index(), icon: FileBarChart },
        );
    }

    // Solo Admin: usuarios y tablas maestras
    if (roleId === ROLE_ADMIN) {
        items.push(
            { title: 'Usuarios', href: users.index(), icon: ShieldCheck },
            { title: 'Provincias', href: provinces.index(), icon: Landmark },
            { title: 'Ciudades', href: cities.index(), icon: MapPin },
            { title: 'Estados de cuenta', href: accountstatuses.index(), icon: CreditCard },
        );
    }

    return items;
});
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
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
