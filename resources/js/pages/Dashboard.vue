<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import AdminDashboard from '@/components/dashboard/AdminDashboard.vue'
import MemberDashboard from '@/components/dashboard/MemberDashboard.vue'

defineProps<{
  role: 'admin' | 'member'
  stats?: {
    total_members: number
    active_users: number
    morosos: number
  }
  myMemberData?: any
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Inicio', href: dashboard().url },
]
</script>

<template>
  <Head title="Inicio" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4">
      <AdminDashboard
        v-if="role === 'admin' && stats"
        :stats="stats"
      />
      <MemberDashboard
        v-else
        :member="myMemberData"
      />
    </div>
  </AppLayout>
</template>
