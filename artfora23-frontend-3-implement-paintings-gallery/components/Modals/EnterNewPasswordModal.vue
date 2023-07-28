<template>
  <ui-kit-modal
    :with-footer="false"
    :close-btn-as-home-link="true"
    title="New password"
    ref="enterNewPasswordModal"
    class="auth-modal"
  >
    <template v-slot:content>
      <p class="ui-kit-box-content-small-text">
        You have now reset your password.<br>
        Please type in your new password.
      </p>

      <form v-if="!success" @submit.prevent="resetPassword">
        <ui-kit-input
          v-model="restoreData.password"
          :errors="v$.restoreData.password"
          :error-messages="{
            required: 'Please enter password. ',
            minLength: 'Minimum 8 characters. ',
            containsUppercase: 'Minimum 1 capital letter. ',
            containsNumber: 'Minimum 1 number. '
          }"
          :server-errors="serverErrors"
          :disabled="store.pendingRequestsCount"
          placeholder="PASSWORD"
          name="password"
          type="password"
        />

        <ui-kit-input
          v-model="restoreData.confirm"
          :errors="v$.restoreData.confirm"
          :error-messages="{ required: 'Please repeat password. ', sameAs: 'Does not match the entered password. ' }"
          :server-errors="serverErrors"
          :disabled="store.pendingRequestsCount"
          placeholder="REPEAT PASSWORD"
          name="confirm"
          type="password"
        />

        <div class="ui-kit-modal-content-buttons">
          <button
            :disabled="store.pendingRequestsCount"
            class="button full-width"
            type="submit"
          >
            LOGIN
          </button>
        </div>
      </form>

      <div v-else>
        <p class="ui-kit-box-content-small-text">
          <span class="ui-kit-box-content-success-text">
            Your password has been successfully changed.
          </span>
        </p>

        <div class="ui-kit-modal-content-buttons">
          <button
            class="button full-width"
            @click="openSignUpModal"
          >
            LOGIN
          </button>
        </div>
      </div>
    </template>
  </ui-kit-modal>
</template>

<script setup lang="ts">
import { computed, ref } from '@vue/reactivity'
import { required, minLength, sameAs } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import { useAuthStore } from '~/store/auth'
import { useRoute } from 'vue-router'
import { useStore } from '~/store'
import type { RestorePasswordData } from '~/types/auth'
import UiKitModal from '~/components/UiKit/UiKitModal.vue'
import UiKitInput from '~/components/UiKit/UiKitInput.vue'

const enterNewPasswordModal = ref<InstanceType<typeof UiKitModal>>(null)
const authStore = useAuthStore()
const store = useStore()
const route = useRoute()
const emit = defineEmits(['openLogInModal'])

const restoreData: RestorePasswordData = reactive({
  password: '',
  confirm: '',
  token: route.query.token as string || ''
})

let error = ref(false)
let serverErrors = ref({})
let success = ref(false)

const v$ = useVuelidate({
  restoreData: {
    password: {
      required,
      minLength: minLength(8),
      containsUppercase: (value: string) => {
        return /[A-Z]/.test(value)
      },
      containsNumber: (value: string) => {
        return /[0-9]/.test(value)
      },
    },
    confirm: {
      required,
      sameAs: sameAs(computed(() => {
        return restoreData.password
      })),
    }
  }
}, { restoreData })
async function resetPassword() {
  v$.value.$touch()

  if (v$.value.$error) {
    return
  }

  error.value = false
  serverErrors.value = {}

  try {
    await authStore.restorePassword(restoreData)

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
  emit('openLogInModal')
}
function open() {
  error.value = false
  serverErrors.value = {}
  success.value = false

  enterNewPasswordModal.value?.open()
}

function close() {
  enterNewPasswordModal.value?.close()
}

defineExpose({ open, close })
</script>