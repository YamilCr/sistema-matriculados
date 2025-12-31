<script setup>
import { Head, Link } from '@inertiajs/vue3';

// Recibimos estadísticas desde el controlador para que se vea real
const props = defineProps({
    stats: Object,
    auth: Object
});
</script>

<template>
    <Head title="Panel de Administración" />

    <div class="p-8 bg-gray-100 min-h-screen">
        <header class="mb-8 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-blue-900">Panel de Control (Super Usuario)</h1>
            <span class="bg-blue-200 text-blue-800 px-3 py-1 rounded-full text-sm font-bold">
                Admin: {{ auth.user.name }}
            </span>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                <p class="text-gray-500 uppercase text-xs font-bold">Total Matriculados</p>
                <p class="text-3xl font-black">{{ stats.total_members }}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500">
                <p class="text-gray-500 uppercase text-xs font-bold">Morosos</p>
                <p class="text-3xl font-black">{{ stats.morosos }}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
                <p class="text-gray-500 uppercase text-xs font-bold">Usuarios Activos</p>
                <p class="text-3xl font-black">{{ stats.active_users }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm">
            <h2 class="text-xl font-bold mb-4">Gestión del Sistema</h2>
            <div class="flex flex-wrap gap-4">
                <Link :href="route('members.index')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ver Matriculados</Link>
                <Link :href="route('users.index')" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-black">Gestionar Usuarios</Link>
                <Link :href="route('locations.index')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Configurar Localidades</Link>
                <Link :href="route('account-statuses.index')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Estados de Cuenta</Link>
            </div>
        </div>
    </div>
</template>