<script setup lang="ts">
defineProps<{
  show: boolean
  message: string
  type?: 'success' | 'error'
}>()

defineEmits<{
  close: []
}>()
</script>

<template>
  <Teleport to="body">
    <div
      v-if="show"
      class="toast"
      :class="`toast-${type ?? 'success'}`"
      role="status"
    >
      <span>{{ message }}</span>

      <button
        type="button"
        class="toast-close"
        aria-label="Close notification"
        @click="$emit('close')"
      >
        ×
      </button>
    </div>
  </Teleport>
</template>

<style scoped>
.toast {
  position: fixed;
  right: 24px;
  bottom: 24px;
  z-index: 1100;

  display: flex;
  align-items: center;
  gap: 16px;

  width: min(380px, calc(100vw - 32px));
  padding: 14px 16px;
  border-radius: 10px;

  color: #fff;
  box-shadow: 0 10px 30px rgb(0 0 0 / 15%);
}

.toast-success {
  background: #15803d;
}

.toast-error {
  background: #b91c1c;
}

.toast-close {
  margin-left: auto;
  padding: 0;
  border: 0;
  background: transparent;
  color: inherit;
  font-size: 20px;
  cursor: pointer;
}

@media (max-width: 640px) {
  .toast {
    right: 16px;
    bottom: 16px;
  }
}
</style>
