<script setup lang="ts">
import { onBeforeUnmount, watch } from 'vue'

const props = defineProps<{
  open: boolean
  title: string
  message: string
  confirmLabel?: string
  cancelLabel?: string
  loading?: boolean
}>()

const emit = defineEmits<{
  confirm: []
  cancel: []
}>()

function handleKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape' && !props.loading) {
    emit('cancel')
  }
}

watch(
  () => props.open,
  (open) => {
    if (open) {
      window.addEventListener('keydown', handleKeydown)
    } else {
      window.removeEventListener('keydown', handleKeydown)
    }
  },
)

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
  <Teleport to="body">
    <div
      v-if="open"
      class="modal-backdrop"
      @click.self="$emit('cancel')"
    >
      <div
        class="confirm-modal"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="'confirm-modal-title'"
      >
        <h2 id="confirm-modal-title">
          {{ title }}
        </h2>

        <p>
          {{ message }}
        </p>

        <div class="modal-actions">
          <button
            type="button"
            class="btn btn-secondary"
            :disabled="loading"
            @click="$emit('cancel')"
          >
            {{ cancelLabel ?? 'Cancel' }}
          </button>

          <button
            type="button"
            class="btn btn-danger"
            :disabled="loading"
            @click="$emit('confirm')"
          >
            {{ loading ? 'Deleting...' : (confirmLabel ?? 'Confirm') }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 1000;

  display: flex;
  align-items: center;
  justify-content: center;

  padding: 20px;
  background: rgb(0 0 0 / 45%);
}

.confirm-modal {
  width: 100%;
  max-width: 440px;
  padding: 24px;
  border-radius: 12px;
  background: #fff;
}

.confirm-modal h2 {
  margin: 0 0 12px;
}

.confirm-modal p {
  margin: 0;
  color: #6b7280;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
}
</style>
