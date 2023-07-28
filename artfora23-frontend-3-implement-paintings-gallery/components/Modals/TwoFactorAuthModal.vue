<template>
  <ui-kit-modal
    :with-footer="false"
    :close-btn-as-home-link="route.name === 'enter-new-password'"
    title="Safe login"
    ref="TwoFactorAuthModal"
    class="auth-modal"
  >
    <template v-slot:content>
      <form @submit.prevent="confirmTwoFactorAuth">
        <p class="ui-kit-box-content-small-text">
          Please type in the 2 factor authentication code which we have sent you by email:
        </p>

        <ui-kit-input
          v-model="auth.code"
          :errors="v$.auth.code"
          :error-messages="{ required: 'Please enter 2 factor authentication code. ' }"
          :server-errors="serverErrors"
          :disabled="store.pendingRequestsCount"
          placeholder="AUTHENTICATION CODE"
          name="code"
        />

        <span
          v-if="error && !successResend && !success"
          class="form-errors-list"
        >
          <span class="form-error error">
            No no no nooo good!<br>Please try again or
            <span
              class="link"
              @click="resendEmailCode"
            >
              resend authentication code.
            </span>
          </span>
        </span>

        <p v-if="successResend" class="ui-kit-box-content-small-text">
          <span class="ui-kit-box-content-success-text">
            The 2 factor authentication has been resent to your email address.
          </span>
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
import useVuelidate from '@vuelidate/core'
import { useAuthStore } from '~/store/auth'
import { useProductsStore } from '~/store/products'
import { useStore } from '~/store'
import { storeToRefs } from 'pinia'
import { useRoute } from 'vue-router'
import { useUserStore } from '~/store/user'
import { ROLE_ADMIN } from '~/types/constants'
import type { TwoFactorAuthData } from '~/types/auth'
import UiKitModal from '~/components/UiKit/UiKitModal.vue'
import UiKitInput from '~/components/UiKit/UiKitInput.vue'

const TwoFactorAuthModal = ref<InstanceType<typeof UiKitModal>>(null)
const store = useStore()
const authStore = useAuthStore()
const userStore = useUserStore()
const productsStore = useProductsStore()
const route = useRoute()
const { emailForTwoFactorAuth } = storeToRefs(authStore)
const { getUserRole } = storeToRefs(userStore)

const auth: TwoFactorAuthData = reactive({
  code: '',
  email: null
})

let error = ref(false)
let serverErrors = ref({})
let success = ref(false)
let successResend = ref(false)

const v$ = useVuelidate({
  auth: {
    code: { required },
    email: { required, email }
  }
}, { auth })

async function confirmTwoFactorAuth() {
  auth.email = emailForTwoFactorAuth.value

  v$.value.$touch()

  if (v$.value.$error) {
    return
  }

  error.value = false
  serverErrors.value = {}
  successResend.value = false

  try {
    await authStore.checkEmailTwoFactorAuth(auth)

    await userStore.fetch()

    if (getUserRole.value === ROLE_ADMIN) {
      await productsStore.getPendingCount()
    }

    success.value = true
    auth.code = ''

    close()
  } catch (e) {
    if (e.response && !e.response.data.errors) {
      error.value = true

      return
    }

    serverErrors.value = e.response.data.errors
  }
}

async function resendEmailCode() {
  v$.value.$touch()

  if (v$.value.$error) {
    return
  }

  error.value = false
  serverErrors.value = {}
  successResend.value = false

  try {
    await authStore.resendEmailTwoFactorAuthCode(auth)

    successResend.value = true
  } catch (e) {
    if (e.response && !e.response.data.errors) {
      error.value = true

      return
    }

    serverErrors.value = e.response.data.errors
  }
}

function open() {
  auth.code = ''
  TwoFactorAuthModal.value?.open()
}

function close() {
  TwoFactorAuthModal.value?.close()
}

defineExpose({ open, close })
</script>