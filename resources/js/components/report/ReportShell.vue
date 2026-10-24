<script setup lang="ts">
import Modal from '@/components/Modal.vue';

defineProps<{
    show: boolean;
    title: string;
    subtitle: string;
    maxWidth?: '2xl' | '3xl' | '4xl' | '5xl';
}>();
const emit = defineEmits<{ close: [] }>();
</script>

<template>
    <Modal :show="show" :max-width="maxWidth ?? '4xl'" @close="emit('close')">
        <div class="p-6">
            <div class="mb-6 flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ title }}</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ subtitle }}</p>
                </div>
                <button class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300" @click="emit('close')">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <slot />

            <div class="mt-6 flex justify-end gap-3">
                <slot name="actions" />
                <button
                    class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700 transition-colors hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    @click="emit('close')"
                >
                    Cerrar
                </button>
            </div>
        </div>
    </Modal>
</template>
