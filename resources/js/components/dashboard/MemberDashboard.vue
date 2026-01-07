<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import MemberCard from '@/components/MemberCard.vue';

const page = usePage()
const user = computed(() => page.props.auth.user as any)

defineProps<{
    member: any 
}>()

const lastAccess = new Date().toLocaleDateString('es-ES', { 
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
});
</script>

<template>
    <Head title="Mi Perfil Profesional" />
    
    <div class="min-h-screen transition-colors duration-500 bg-slate-50 dark:bg-[#050505] p-4 md:p-12 flex flex-col items-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-80 bg-emerald-700 dark:bg-emerald-900/40 -z-10 clip-path-slant opacity-90"></div>

        <MemberCard 
            :user-name="user.name" 
            :user-image="user.image" 
            :member="member" 
        />
        
        <footer class="mt-12 max-w-2xl w-full text-center border-t border-slate-200 dark:border-white/5 pt-8">
            <p class="text-[10px] font-bold text-slate-400 dark:text-emerald-500/50 uppercase tracking-[0.2em]">
                Sistema Federal de Colegiación
            </p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                Sesión activa: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ user.name }}</span><br>
                <span class="text-xs italic opacity-60">Acceso: {{ lastAccess }}</span>
            </p>
        </footer>
    </div>
</template>

<style scoped>
.clip-path-slant {
    clip-path: polygon(0 0, 100% 0, 100% 60%, 0% 100%);
}
</style>