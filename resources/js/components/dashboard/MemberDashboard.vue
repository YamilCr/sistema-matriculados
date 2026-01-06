<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import MemberCard from '@/components/MemberCard.vue'; // Importamos el hijo

const page = usePage()
const user = computed(() => page.props.auth.user as any)

defineProps<{
  member: any // Definición simplificada aquí porque el hijo ya tiene el detalle
}>()
</script>

<template>
    <Head title="Mi Cuenta" />
    <div class="p-8 bg-green-50 min-h-screen flex flex-col items-center">
        <MemberCard 
            :user-name="user.name" 
            :user-image="user.image" 
            :member="member" 
        />
        
        <div class="mt-6 max-w-2xl w-full text-center">
             <p class="text-sm text-gray-400">Último acceso: {{ new Date().toLocaleDateString() }}</p>
        </div>
    </div>
</template>