<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  status: string
}>()

// Convert values such as "interview" into "Interview"
const label = computed(() => {
  return props.status
    .replaceAll('_', ' ')
    .replace(/\b\w/g, (letter) => letter.toUpperCase())
})

// Apply a CSS class based on the application's current status
const statusClass = computed(() => {
  return `status-${props.status.toLowerCase()}`
})
</script>

<template>
  <span class="status-badge" :class="statusClass">
    {{ label }}
  </span>
</template>

<style scoped>
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  background: #f3f4f6;
  color: #4b5563;
}

.status-interested {
  background: #eff6ff;
  color: #1d4ed8;
}

.status-applied {
  background: #eef2ff;
  color: #4338ca;
}

.status-interview {
  background: #fff7ed;
  color: #c2410c;
}

.status-offer {
  background: #ecfdf5;
  color: #047857;
}

.status-rejected {
  background: #fef2f2;
  color: #b91c1c;
}

.status-withdrawn {
  background: #f3f4f6;
  color: #6b7280;
}
</style>
