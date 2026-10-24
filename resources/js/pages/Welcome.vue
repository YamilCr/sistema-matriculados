<script setup lang="ts">
import { dashboard, login } from '@/routes';
import { demoAccounts } from '@/lib/demoAccounts';
import { Head, Link } from '@inertiajs/vue3';

const features = [
    { icon: '👥', title: 'Padrón de matriculados', text: 'Alta, edición, baja y búsqueda con foto, DNI, domicilio y estado de cuenta.' },
    { icon: '🔐', title: 'Roles y permisos', text: 'Admin, Staff y Matriculado, cada uno con su propio panel y alcance.' },
    { icon: '📍', title: 'Provincias y ciudades', text: 'Tablas maestras con ABM y selects dependientes provincia → ciudad.' },
    { icon: '📊', title: 'Reportes y exportación', text: 'Estadísticas por estado y ubicación, con exportación a CSV y Excel.' },
];

</script>

<template>
    <Head title="Bienvenido" />
    <div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-[#0a0a0a] dark:text-slate-100">
        <header class="mx-auto flex max-w-5xl items-center justify-between px-6 py-5">
            <span class="text-sm font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                Sistema de Matriculados
            </span>
            <nav>
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                >
                    Ir al panel
                </Link>
                <Link
                    v-else
                    :href="login()"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                >
                    Iniciar sesión
                </Link>
            </nav>
        </header>

        <main class="mx-auto max-w-5xl px-6 pb-16">
            <section class="py-12 text-center sm:py-20">
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">
                    Gestión de matrículas profesionales
                </h1>
                <p class="mx-auto mt-5 max-w-2xl text-lg text-slate-600 dark:text-slate-400">
                    Demo de un sistema web para administrar el padrón de un colegio profesional:
                    matriculados, usuarios, estados de cuenta, ubicaciones y reportes.
                    Hecho con Laravel, Inertia, Vue 3 y Tailwind.
                </p>
                <div class="mt-8 flex justify-center gap-3">
                    <Link
                        :href="$page.props.auth.user ? dashboard() : login()"
                        class="rounded-lg bg-indigo-600 px-6 py-3 font-semibold text-white shadow hover:bg-indigo-700"
                    >
                        Probar la demo
                    </Link>
                    <a
                        href="https://github.com/YamilCr/sistema-matriculados"
                        target="_blank"
                        rel="noopener"
                        class="rounded-lg border border-slate-300 px-6 py-3 font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                    >
                        Ver código
                    </a>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2">
                <div
                    v-for="f in features"
                    :key="f.title"
                    class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="mb-3 text-3xl">{{ f.icon }}</div>
                    <h3 class="font-bold">{{ f.title }}</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ f.text }}</p>
                </div>
            </section>

            <section v-if="$page.props.demo" class="mt-10 rounded-2xl border border-indigo-200 bg-indigo-50 p-6 dark:border-indigo-900 dark:bg-indigo-950/40">
                <h2 class="font-bold">Cuentas de prueba</h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Entrá con cualquiera de estas cuentas para recorrer el sistema según cada rol.
                </p>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase text-slate-500">
                            <tr>
                                <th class="py-2 pr-4">Rol</th>
                                <th class="py-2 pr-4">Email</th>
                                <th class="py-2 pr-4">Contraseña</th>
                                <th class="py-2">Qué puede hacer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="a in demoAccounts" :key="a.email" class="border-t border-indigo-100 dark:border-indigo-900">
                                <td class="py-2 pr-4 font-semibold">{{ a.role }}</td>
                                <td class="py-2 pr-4 font-mono">{{ a.email }}</td>
                                <td class="py-2 pr-4 font-mono">{{ a.password }}</td>
                                <td class="py-2">{{ a.note }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</template>
