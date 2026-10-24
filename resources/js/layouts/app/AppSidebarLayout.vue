<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { AppPageProps, BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { watch } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

// Toast global para los mensajes flash del backend (message / error).
const page = usePage<AppPageProps>();
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
});

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.message) Toast.fire({ icon: 'success', title: flash.message });
        if (flash?.error) Toast.fire({ icon: 'error', title: flash.error });
    },
);
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
