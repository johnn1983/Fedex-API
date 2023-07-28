<template>
  <ui-kit-modal
    :with-footer="false"
    :title="modalTitle"
    :close-btn-as-home-link="route.name === 'enter-new-password'"
    ref="resetPasswordModal"
    class="auth-modal"
  >
    <template v-slot:content>
      <p class="ui-kit-box-content-small-text">
        Don't have an account?
        <span
          class="link"
          @click="openSignUpModal"
        >Sign up here!</span>
      </p>

      <form v-if="!success" @submit.prevent="resetPassword">
        <ui-kit-input
          v-model="resetData.login"
          :errors="v$.resetData.login"
          :error-messages="{ required: 'Please enter email. ', email: 'Please enter valid email address. ' }"
          :server-errors="serverErrors"
          :disabled="store.pendingRequestsCount"
          placeholder="EMAIL ADDRESS"
          name="login"
        />

        <span v-if="error" class="form-errors-list">
          <span class="form-error error">
            Either your email address seems to be wrong. Please enter valid email address.
          </span>
        </span>

        <div class="ui-kit-modal-content-buttons">
          <button
            :disabled="store.pendingRequestsCount"
            class="button full-width"
            type="submit"
          >
            Reset password
          </button>
        </div>
      </form>

      <div v-else>
        <p class="ui-kit-box-content-small-text">
          <span class="ui-kit-box-content-success-text">
            We have sent you an email with a link where you can reset your password.
          </span>
        </p>

        <div class="ui-kit-modal-content-buttons">
          <button
            class="button full-width"
            @click="close"
          >
            Close
          </button>
        </div>
      </div>
    </template>
  </ui-kit-modal>
</template>

<script setup lang="ts">
import { ref } from '@vue/reactivity'
import { required, email } from '@vuelidate/validators'
import { useRoute } from 'vue-router'
import useVuelidate from '@vuelidate/core'
import { useAuthStore } from '~/store/auth'
import { useStore } from '~/store'
import type { ResetPasswordData } from '~/types/auth'
import UiKitModal from '~/components/UiKit/UiKitModal.vue'
import UiKitInput from '~/components/UiKit/UiKitInput.vue'

const resetPasswordModal = ref<InstanceType<typeof UiKitModal>>(null)
const authStore = useAuthStore()
const store = useStore()
const route = useRoute()
const emit = defineEmits(['openSignUpModal'])

const resetData: ResetPasswordData = reactive({
  login: '',
})

let modalTitle = ref('Reset password')
let error = ref(false)
let serverErrors = ref({})
let success = ref(false)

const v$ = useVuelidate({
  resetData: {
    login: { required, email }
  }
}, { resetData })
async function resetPassword() {
  v$.value.$touch()

  if (v$.value.$error) {
    return
  }

  error.value = false
  serverErrors.value = {}

  try {
    await authStore.resetPassword(resetData)

    modalTitle.value = 'Request sent'
    success.value = true
  } catch (e) {
    if (e.response && !e.response.data.errors) {
      error.value = true

      return
    }

    serverErrors.value = e.response.data.errors
  }
}
function openSignUpModal() {
  close()
  emit('openSignUpModal')
}
function open() {
  modalTitle.value = 'Reset password'
  error.value = false
  serverErrors.value = {}
  success.value = false
  resetData.login = ''

  resetPasswordModal.value?.open()
}

function close() {
  resetPasswordModal.value?.close()
}

defineExpose({ open, close })
</script>