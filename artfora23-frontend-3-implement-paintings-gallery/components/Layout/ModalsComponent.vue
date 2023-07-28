<template>
  <div>
    <add-product
      @open-ai-safe-description="openAiSafeDescriptionModal"
      ref="addProductModalRef"
    />

    <ai-safe-description-modal
      ref="aiSafeDescriptionModalRef"
    />

    <set-up-account-modal ref="setUpAccountModalRef" />

    <sign-up-modal
      ref="signUpModalRef"
      @open-log-in-modal="openLogInModal"
    />

    <log-in-modal
      ref="logInModalRef"
      @open-sign-up-modal="openSignUpModal"
      @open-two-factor-auth-modal="twoFactorAuthModalRef.open()"
      @open-reset-password-modal="resetPasswordModalRef.open()"
    />

    <two-factor-auth-modal ref="twoFactorAuthModalRef" />

    <reset-password-modal
      ref="resetPasswordModalRef"
      @open-sign-up-modal="openSignUpModal"
    />

    <gallery-settings-modal
      ref="gallerySettingsModalRef"
      @open-gallery-settings-modal="openGallerySettingsModal"
    />

    <enter-new-password-modal
      ref="enterNewPasswordModalRef"
      @open-log-in-modal="openLogInModal"
    />

    <contact-us-modal ref="contactUsModalRef" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '~/store/auth'
import { storeToRefs } from 'pinia'
import SignUpModal from '~/components/Modals/SignUpModal.vue'
import AddProduct from '~/components/Modals/AddProductModal.vue'
import UiKitModal from '~/components/UiKit/UiKitModal.vue'
import LogInModal from '~/components/Modals/LogInModal.vue'
import TwoFactorAuthModal from '~/components/Modals/TwoFactorAuthModal.vue'
import ResetPasswordModal from '~/components/Modals/ResetPasswordModal.vue'
import EnterNewPasswordModal from '~/components/Modals/EnterNewPasswordModal.vue'
import ContactUsModal from '~/components/Modals/ContactModal.vue'
import GallerySettingsModal from '~/components/Modals/GallerySettingsModal.vue'
import SetUpAccountModal from '~/components/Modals/SetUpAccountModal.vue'
import AiSafeDescriptionModal from '~/components/Modals/AiSafeDescriptionModal.vue'

const authStore = useAuthStore()
const { isAwaitingTokenConfirmation } = storeToRefs(authStore)

const signUpModalRef = ref<InstanceType<typeof SignUpModal>>(null)
const addProductModalRef = ref<InstanceType<typeof UiKitModal>>(null)
const logInModalRef = ref<InstanceType<typeof LogInModal>>(null)
const twoFactorAuthModalRef = ref<InstanceType<typeof TwoFactorAuthModal>>(null)
const resetPasswordModalRef = ref<InstanceType<typeof ResetPasswordModal>>(null)
const enterNewPasswordModalRef = ref<InstanceType<typeof EnterNewPasswordModal>>(null)
const contactUsModalRef = ref<InstanceType<typeof ContactUsModal>>(null)
const gallerySettingsModalRef = ref<InstanceType<typeof GallerySettingsModal>>(null)
const setUpAccountModalRef = ref<InstanceType<typeof SetUpAccountModal>>(null)
const aiSafeDescriptionModalRef = ref<InstanceType<typeof AiSafeDescriptionModal>>(null)

const router = useRouter()
const route = useRoute()

onMounted(() => {
  if (process.client && route.name === 'enter-new-password') {
    enterNewPasswordModalRef.value.open()
  }
})

router.beforeEach((to, from, next) => {
  signUpModalRef.value.close()
  logInModalRef.value.close()
  twoFactorAuthModalRef.value.close()
  addProductModalRef.value.close()
  resetPasswordModalRef.value.close()
  enterNewPasswordModalRef.value.close()
  contactUsModalRef.value.close()
  gallerySettingsModalRef.value.close()
  setUpAccountModalRef.value.close()
  aiSafeDescriptionModalRef.value.close()

  next()
})

function openLogInModal() {
  if (isAwaitingTokenConfirmation.value) {
    twoFactorAuthModalRef.value.open()
    return
  }

  logInModalRef.value.open()
}

function openSetUpAccountModal() {
  setUpAccountModalRef.value.open()
}

function openSignUpModal() {
  if (isAwaitingTokenConfirmation.value) {
    twoFactorAuthModalRef.value.open()
    return
  }

  signUpModalRef.value.open()
}

function openGallerySettingsModal() {
  gallerySettingsModalRef.value.open()
}

function openAddProductModal() {
  addProductModalRef.value.open()
}

function openAiSafeDescriptionModal() {
  aiSafeDescriptionModalRef.value.open()
}

function openContactUsModal() {
  contactUsModalRef.value.open()
}

defineExpose({ openAddProductModal, openSignUpModal, openContactUsModal, openGallerySettingsModal, openSetUpAccountModal })
</script>