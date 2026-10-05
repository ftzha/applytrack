<script setup lang="ts">
import { RouterView } from 'vue-router'
import AppToast from '@/components/AppToast.vue'
import { useToastStore } from '@/stores/toast'

const toastStore = useToastStore()
</script>

<template>
  <RouterView />

  <AppToast
    :show="toastStore.show"
    :message="toastStore.message"
    :type="toastStore.type"
    @close="toastStore.closeToast"
  />
</template>
