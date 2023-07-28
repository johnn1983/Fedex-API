<template>
  <ui-kit-modal
    :title="success ? 'Message sent' : 'Contact'"
    :with-footer="false"
    class="contact-modal"
    ref="contactForm"
  >

    <template v-slot:content>
      <form v-if="!success" @submit.prevent="sendForm" class="contact-modal-submit">
        <ui-kit-input
          v-model="contactFormData.name"
          :errors="v$.contactFormData.name"
          :error-messages="{ required: 'Please enter username.' }"
          :disabled="store.pendingRequestsCount"
          type="text"
          placeholder="YOUR NAME"
        />

        <ui-kit-input
          v-model="contactFormData.email"
          :errors="v$.contactFormData.email"
          :error-messages="{ required: 'Please enter email.', email: 'Please enter valid email address.' }"
          :disabled="store.pendingRequestsCount"
          type="mail"
          placeholder="YOUR EMAIL ADDRESS"
        />

        <ui-kit-text-area
          v-model="contactFormData.text"
          :errors="v$.contactFormData.text"
          :error-messages="{ required: 'Please enter text.'}"
          :disabled="store.pendingRequestsCount"
          placeholder="YOUR MESSAGE"
        />

        <div>
          <div id="mtcaptcha" class="mtcaptcha"></div>
          <span
            v-show="v$.contactFormData.mtcaptcha_token.$error"
            class="form-error error"
          >Captcha is required</span>
        </div>

        <div class="ui-kit-modal-content-buttons">
          <button
            :disabled="store.pendingRequestsCount"
            class="button full-width"
            type="submit"
          >SEND MESSAGE</button>
        </div>
      </form>

      <div v-else class="contact-modal-submit">
        <div>
          <p class="contact-modal-submit-title">YOUR NAME:</p>
          <span>{{ contactFormData.name }}</span>
        </div>
        <div>
          <p class="contact-modal-submit-title">YOUR EMAIL ADDRESS:</p>
          <span>{{ contactFormData.email }}</span>
        </div>
        <div>
          <p class="contact-modal-submit-title">YOUR MESSAGE:</p>
          <span>{{ contactFormData.text }}</span>
        </div>
        <p class="ui-kit-box-content-small-text">
          <span class="ui-kit-box-content-success-text">
            We have also sent you a copy of the message to your email address.
          </span>
        </p>
        <div class="ui-kit-modal-content-buttons">
          <button
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
import { ref, reactive, onMounted } from 'vue'
import { required, email } from '@vuelidate/validators'
import { useStore } from '~/store/index'
import UiKitModal from '~/components/UiKit/UiKitModal.vue'
import useVuelidate from '@vuelidate/core'

const contactForm = ref<InstanceType<typeof UiKitModal>>(null)
const contactFormData = reactive({
  name: '',
  email: '',
  text: '',
  mtcaptcha_token: ''
})

const store = useStore()
const success = ref(false)

const v$ = useVuelidate({
  contactFormData: {
    name: { required },
    email: { required, email },
    text: { required },
    mtcaptcha_token: { required }
  }
}, { contactFormData })

function refreshModal() {
  contactFormData.name = ''
  contactFormData.email = ''
  contactFormData.text = ''
  success.value = false
}

async function sendForm() {

  contactFormData.mtcaptcha_token = document.getElementsByClassName('mtcaptcha-verifiedtoken')[0].value

  v$.value.$touch()

  if (v$.value.$error) {
    return
  }

  await store.send(contactFormData)

  success.value = true
}

function open() {
  refreshModal()
  contactForm.value?.open()
  window.mtcaptchaConfig.renderQueue.push("mtcaptcha")
}

function close() {
  contactForm.value?.close()
}

defineExpose({ open, close })
</script>