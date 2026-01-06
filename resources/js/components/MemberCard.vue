<script setup lang="ts">
// Solo recibimos los datos que necesitamos para mostrar la tarjeta
defineProps<{
  userName: string;
  userImage?: string;
  member: {
    registration_number?: string;
    image?: string;
    account_status_id: number;
    account_status: { name: string };
    location: { name: string };
  }
}>()
</script>

<template>
    <div class="w-full max-w-2xl">
        <header class="mb-8 flex flex-col items-center text-center">
            <div class="relative w-32 h-32 mb-4 rounded-full border-4 border-white shadow-xl overflow-hidden bg-gray-200 flex items-center justify-center">
                <img v-if="member.image" :src="'/storage/' + member.image" class="w-full h-full object-cover" />
                <img v-else-if="userImage" :src="'/storage/' + userImage" class="w-full h-full object-cover" />
                <div v-else class="text-gray-400 font-bold text-4xl uppercase">
                    {{ userName.charAt(0) }}
                </div>
            </div>
            <h1 class="text-3xl font-bold text-green-900">Portal del Matriculado</h1>
            <p class="text-gray-600 italic">Bienvenido, {{ userName }}</p>
        </header>

        <div class="bg-white p-8 rounded-2xl shadow-lg border border-green-100 w-full">
            <h2 class="text-xl font-bold border-b pb-4 mb-6 text-center text-gray-800">Mis Datos Profesionales</h2>
            <div class="space-y-6">
                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                    <span class="text-gray-500 font-medium">N° de Matrícula:</span>
                    <span class="font-mono font-bold text-black text-lg">{{ member.registration_number }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                    <span class="text-gray-500 font-medium">Estado de Cuenta:</span>
                    <span :class="member.account_status_id === 1 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'" 
                          class="font-bold uppercase px-3 py-1 rounded-full text-sm">
                        {{ member.account_status.name }}
                    </span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                    <span class="text-gray-500 font-medium">Localidad:</span>
                    <span class="font-bold text-black">{{ member.location.name }}</span>
                </div>
            </div>
        </div>
    </div>
</template>