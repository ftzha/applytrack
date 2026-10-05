import { ref } from 'vue'
import { defineStore } from 'pinia'

type ToastType = 'success' | 'error'

export const useToastStore = defineStore('toast', () => {
  const show = ref(false)
  const message = ref('')
  const type = ref<ToastType>('success')

  let timer: ReturnType<typeof setTimeout> | null = null

  function openToast(
    toastMessage: string,
    toastType: ToastType = 'success',
  ) {
    if (timer) {
      clearTimeout(timer)
    }

    message.value = toastMessage
    type.value = toastType
    show.value = true

    timer = setTimeout(() => {
      show.value = false
      timer = null
    }, 3000)
  }

  function closeToast() {
    show.value = false

    if (timer) {
      clearTimeout(timer)
      timer = null
    }
  }

  return {
    show,
    message,
    type,
    openToast,
    closeToast,
  }
})
