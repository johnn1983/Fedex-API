<template>
  <div class="profile-menu">
    <div
      v-if="isAuthorized"
      @click="emit('openAddProductModal')"
      class="new-product"
    >
      <plus-icon class="plus-icon"/>
    </div>
    <div
      @click="menuDropdownRef.open()"
      class="icon-button"
    >
      <img
        :src="getUserAvatar(userAvatar, ImageTemplate.SmallThumbnail)"
        alt="user_avatar"
      >
    </div>

    <ui-kit-dropdown title="Menu" ref="menuDropdownRef">
      <div v-if="isAuthorized" class="ui-kit-dropdown-content-item">
        <span
          @click="openSetUpAccountModal"
          class="ui-kit-dropdown-content-item-btn"
        >My Settings</span>
      </div>

      <div v-if="!isAuthorized" class="ui-kit-dropdown-content-item">
        <span
          @click="openSignUpModal"
          class="ui-kit-dropdown-content-item-btn"
        >Login/Sign up</span>
      </div>

      <div class="ui-kit-dropdown-content-item">
        <span @click="openSettingsGallery" class="ui-kit-dropdown-content-item-btn">Gallery settings</span>
      </div>

      <div class="ui-kit-dropdown-content-item">
        <span
          @click="openContactUsModal"
          class="ui-kit-dropdown-content-item-btn"
        >Contact us</span>
      </div>

      <div v-if="isAuthorized" class="ui-kit-dropdown-content-item">
        <span
          class="ui-kit-dropdown-content-item-btn"
          @click="logout"
        >Logout</span>
      </div>
    </ui-kit-dropdown>

  </div>
</template>

<script setup lang="ts">
import { ref, onBeforeMount } from 'vue'
import { useAuthStore } from '~/store/auth'
import { useUserStore } from '~/store/user'
import { storeToRefs } from 'pinia'
import { useProductsStore } from '~/store/products'
import { useAsyncData } from '#app'
import { navigateTo } from '#imports'
import useMedia from '~/composable/media'
import UiKitDropdown from '~/components/UiKit/UiKitDropdown.vue'
import PlusIcon from '~/assets/svg/plus.svg'
import { ImageTemplate, ROLE_ADMIN } from '~/types/constants'

const emit = defineEmits(['openAddProductModal', 'openSignUpModal', 'openContactUsModal', 'openGallerySettingsModal', 'openSetUpAccountModal'])

const menuDropdownRef = ref<InstanceType<typeof UiKitDropdown>>(null)

const authStore = useAuthStore()
const userStore = useUserStore()
const { isAuthorized } = storeToRefs(authStore)
const { userAvatar } = storeToRefs(userStore)
const { getUserAvatar } = useMedia()
const { getUserRole } = storeToRefs(userStore)
const productStore = useProductsStore()

function logout() {
  authStore.logout()
  userStore.clearProfile()
  menuDropdownRef.value.close()
  navigateTo('/')
}

function openSettingsGallery() {
  menuDropdownRef.value.close()
  emit('openGallerySettingsModal')
}

function openSetUpAccountModal() {
  menuDropdownRef.value.close()
  emit('openSetUpAccountModal')
}

function openSignUpModal() {
  menuDropdownRef.value.close()
  emit('openSignUpModal')
}

function openContactUsModal() {
  menuDropdownRef.value.close()
  emit('openContactUsModal')
}

onBeforeMount(() => {
  if (isAuthorized.value) {
    useAsyncData('fetch-profile',async () => {
      await userStore.fetch()

      if (getUserRole.value === ROLE_ADMIN) {
        await productStore.getPendingCount()
      }
    })
  }
})
</script>
