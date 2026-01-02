<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'

const { auth } = usePage().props as {
  auth: {
    user: {
      name: string
    }
  }
}

defineProps<{
  member: {
    registration_number?: string
    account_status_id: number
    account_status: {
      name: string
    }
    location: {
      name: string
    }
  }
}>()
</script>

<template>
    <Head title="Mi Cuenta" />

    <div class="p-8 bg-green-50 min-h-screen">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-green-900">Portal del Matriculado</h1>
            <p class="text-gray-600">Bienvenido, {{ auth.user.name }}</p>
        </header>

        <div class="max-w-2xl bg-white p-8 rounded-2xl shadow-lg border border-green-100">
            <h2 class="text-xl font-bold border-b pb-4 mb-4">Mis Datos Profesionales</h2>
            
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">N° de Matrícula:</span>
                    <span class="font-mono font-bold text-black">{{ member.registration_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Estado de Cuenta:</span>
                    <span :class="member.account_status_id === 1 ? 'text-green-600' : 'text-red-600'" class="font-bold uppercase">
                        {{ member.account_status.name }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm ">Localidad:</span>
                    <span class="font-bold text-black">{{ member.location.name }}</span>
                </div>
            </div>

            <div class="mt-8 p-4 bg-blue-50 rounded-lg text-blue-800 text-sm">
                <strong>Aviso:</strong> Si necesita modificar sus datos, por favor contacte a la administración.
            </div>
        </div>
    </div>
</template>