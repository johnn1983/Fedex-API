<template>
  <ui-kit-modal
    :with-footer="false"
    :close-btn-as-home-link="route.name === 'enter-new-password'"
    title="Sign Up"
    ref="signUpModal"
    class="auth-modal"
  >
    <template v-slot:content>
      <form v-if="!success" @submit.prevent="signUp">
        <p class="ui-kit-box-content-small-text">
          Already have an account?
          <span
            class="link"
            @click="openLogInModal"
          >Login here!</span>
        </p>

        <ui-kit-input
          v-model="auth.username"
          :errors="v$.auth.username"
          :error-messages="{ required: 'Please enter username. ' }"
          :server-errors="serverErrors"
          :disabled="globalStore.pendingRequestsCount"
          placeholder="USERNAME"
          name="username"
        />

        <ui-kit-input
          v-model="auth.tagname"
          :errors="v$.auth.tagname"
          :error-messages="{ required: 'Please enter @tagname. ' }"
          :server-errors="serverErrors"
          :attention-messages="{ notChanged: 'Can not be changed later. ' }"
          :disabled="globalStore.pendingRequestsCount"
          placeholder="@TAGNAME"
          name="tagname"
          prefix="@"
        />

        <ui-kit-input
          v-model="auth.email"
          :errors="v$.auth.email"
          :error-messages="{ required: 'Please enter email. ', email: 'Please enter valid email address. ' }"
          :server-errors="serverErrors"
          :disabled="globalStore.pendingRequestsCount"
          placeholder="EMAIL ADDRESS"
          name="email"
        />

        <ui-kit-input
          v-model="auth.password"
          :errors="v$.auth.password"
          :error-messages="{
            required: 'Please enter password. ',
            minLength: 'Minimum 8 characters. ',
            containsUppercase: 'Minimum 1 capital letter. ',
            containsNumber: 'Minimum 1 number. '
          }"
          :server-errors="serverErrors"
          :disabled="globalStore.pendingRequestsCount"
          placeholder="PASSWORD"
          name="password"
          type="password"
        />

        <ui-kit-input
          v-model="auth.confirm"
          :errors="v$.auth.confirm"
          :error-messages="{ required: 'Please repeat password. ', sameAs: 'Does not match the entered password. ' }"
          :server-errors="serverErrors"
          :disabled="globalStore.pendingRequestsCount"
          placeholder="REPEAT PASSWORD"
          name="confirm"
          type="password"
        />

        <span v-if="error" class="form-errors-list">
          <span
            class="form-error error"
            v-html="error"
          ></span>
        </span>

        <div class="ui-kit-modal-content-buttons">
          <button
            :disabled="globalStore.pendingRequestsCount"
            class="button full-width"
            type="submit"
          >
            Send verification email
          </button>
        </div>
      </form>

      <div v-else>
        <p class="ui-kit-box-content-small-text">
          <span class="ui-kit-box-content-success-text">
            We have sent you an email with a link to verify your email address.
          </span>
        </p>

        <div class="ui-kit-modal-content-buttons">
          <button
            :disabled="globalStore.pendingRequestsCount"
            @click="close"
            class="button full-width"
          >
            Close
          </button>
        </div>
      </div>
    </template>
  </ui-kit-modal>
</template>

<script setup lang="ts">
import { ref, computed } from '@vue/reactivity'
import { required, email, sameAs, minLength } from '@vuelidate/validators'
import { useAuthStore } from '~/store/auth'
import { useStore } from '~/store'
import { useRoute, useRouter } from 'vue-router'
import useVuelidate from '@vuelidate/core'
import type { SignUpData } from '~/types/auth'
import UiKitModal from '~/components/UiKit/UiKitModal.vue'
import UiKitInput from '~/components/UiKit/UiKitInput.vue'

const signUpModal = ref<InstanceType<typeof UiKitModal>>(null)
const authStore = useAuthStore()
const globalStore = useStore()
const route = useRoute()
const router = useRouter()
const emit = defineEmits(['openLogInModal'])

const auth: SignUpData = reactive({
  email: '',
  password: '',
  confirm: '',
  username: '',
  tagname: '',
  redirect_after_verification: '/?open-set-up-modal=true'
})

let error = ref('')
let serverErrors = ref({})
let success = ref(false)

const v$ = useVuelidate({
  auth: {
    username: { required },
    tagname: { required },
    email: { required, email },
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
        return auth.password
      })),
    }
  }
}, { auth })

function refreshModal() {
    auth.email = ''
    auth.password = ''
    auth.confirm = ''
    auth.username = ''
    auth.tagname = ''
    auth.redirect_after_verification = '/?open-set-up-modal=true'
}

async function signUp() {
  v$.value.$touch()

  if (v$.value.$error) {
    return
  }

  const data = { ...auth }
  data.tagname = '@' + auth.tagname

  error.value = ''
  serverErrors.value = {}

  try {
    await authStore.signUp(data)

    success.value = true
  } catch (e) {
    if (e.response && !e.response.data.errors) {
      error.value = 'Something went wrong! Please try again later.'

      return
    }

    serverErrors.value = e.response.data.errors
  }
}

function openLogInModal() {
  close()
  emit('openLogInModal')
}

function open() {
  refreshModal()
  signUpModal.value?.open()
}

function close() {
  signUpModal.value?.close()
}

defineExpose({ open, close })
</script>