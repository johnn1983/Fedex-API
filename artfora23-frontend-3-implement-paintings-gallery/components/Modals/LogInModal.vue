<template>
  <ui-kit-modal
    :with-footer="false"
    :close-btn-as-home-link="route.name === 'enter-new-password'"
    title="Login"
    ref="logInModal"
    class="auth-modal"
  >
    <template v-slot:content>
      <form @submit.prevent="logIn">
        <p class="ui-kit-box-content-small-text">
          Don't have an account?
          <span
            class="link"
            @click="openSignUpModal"
          >Sign up here!</span>
        </p>

        <ui-kit-input
          v-model="auth.login"
          :errors="v$.auth.login"
          :error-messages="{ required: 'Please enter email. ', email: 'Please enter valid email address. ' }"
          :server-errors="serverErrors"
          :disabled="store.pendingRequestsCount"
          placeholder="EMAIL ADDRESS"
          name="login"
        />

        <ui-kit-input
          v-model="auth.password"
          :errors="v$.auth.password"
          :error-messages="{ required: 'Please enter password. ' }"
          :server-errors="serverErrors"
          :disabled="store.pendingRequestsCount"
          placeholder="PASSWORD"
          name="password"
          type="password"
        />

        <span v-if="error" class="form-errors-list">
          <span class="form-error error">
            Either your email address or your password seems to be wrong. Try again or
            <span
              class="link"
              @click="openResetPasswordModal"
            >
              reset password here!
            </span>
          </span>
        </span>

        <p
          v-if="!error"
          class="ui-kit-box-content-small-text align-right"
        >
          Forgot your password?
          <span
            class="link"
            @click="openResetPasswordModal"
          >Reset here!</span>
        </p>

        <div class="ui-kit-modal-content-buttons">
          <button
            :disabled="store.pendingRequestsCount"
            class="button full-width"
            type="submit"
          >
            Login
          </button>
        </div>
      </form>
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
import type { LoginData } from '~/types/auth'
import UiKitModal from '~/components/UiKit/UiKitModal.vue'
import UiKitInput from '~/components/UiKit/UiKitInput.vue'

const logInModal = ref<InstanceType<typeof UiKitModal>>(null)
const authStore = useAuthStore()
const store = useStore()
const route = useRoute()
const emit = defineEmits(['openSignUpModal', 'openTwoFactorAuthModal', 'openResetPasswordModal'])

const auth: LoginData = reactive({
  login: '',
  password: ''
})

let error = ref(false)
let serverErrors = ref({})
let success = ref(false)

const v$ = useVuelidate({
  auth: {
    login: { required, email },
    password: { required },
  }
}, { auth })

async function logIn() {
  v$.value.$touch()

  if (v$.value.$error) {
    return
  }

  error.value = false
  serverErrors.value = {}

  try {
    await authStore.login(auth)

    success.value = true

    openTwoFactorAuthModal()
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

function openResetPasswordModal() {
  close()
  emit('openResetPasswordModal')
}

function openTwoFactorAuthModal() {
  close()
  emit('openTwoFactorAuthModal')
}

function open() {
  auth.login = ''
  auth.password = ''

  logInModal.value?.open()
}

function close() {
  logInModal.value?.close()
}

defineExpose({ open, close })
</script>