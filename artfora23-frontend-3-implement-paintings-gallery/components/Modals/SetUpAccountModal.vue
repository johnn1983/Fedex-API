<template>
  <ui-kit-modal
    :with-header="false"
    :with-footer="false"
    ref="setUpAccountModal"
  >

    <template v-slot:customHeader>
      <header class="account-settings-header">

        <close-icon
          @click="close()"
          class="close-icon ui-kit-box-tools-link account-settings-header-close"
        />

        <label
          for="uploadBackground"
          class="account-settings-header-upload-background flex-center"
        >
          <span v-if="!backgroundImage">
            DROP YOUR HEADER AND PROFILE IMAGE <br>
          IN THE RESPECTIVE FIELDS <br>
          OR CLICK TO BROWSE <br>
          (AT LEAST 1200 X 1200 PIXELS ) <br>
          </span>
          <img :src="getImageUrl(backgroundImage, ImageTemplate.FullSize)" alt="background Image" v-if="backgroundImage">
          <input
            id="uploadBackground"
            @change="addFile"
            accept="image/bmp, image/png, image/jpeg"
            type="file"
            ref="file"
          >
        </label>

        <label for="uploadAvatar" class="account-settings-header-upload-avatar">
          <img :src="getUserAvatar(avatar, ImageTemplate.SmallThumbnail)" alt="avatar Image" v-if="avatar">
          <input
            id="uploadAvatar"
            @change="addFile"
            accept="image/bmp, image/png, image/jpeg"
            type="file"
            ref="file"
          >
        </label>

      </header>
    </template>

    <template v-slot:content>
      <form @submit.prevent="uploadProduct" class="account-settings-form">

        <ui-kit-input
          v-model="user.username"
          :errors="v$.user.username"
          :error-messages="{ required: 'Username is required'}"
          :disabled="store.pendingRequestsCount"
          placeholder="USERNAME"
        />

        <ui-kit-input
          v-model="user.email"
          :errors="v$.user.email"
          :error-messages="{ required: 'email is required', email: 'Please enter valid email address.' }"
          :disabled="store.pendingRequestsCount"
          placeholder="EMAIL ADDRESS"
        />

        <ui-kit-text-area
          v-model="user.description"
          :disabled="store.pendingRequestsCount"
          placeholder="USER DESCRIPTION"
        />

        <ui-kit-selector
          v-model="user.country"
          :options="countries"
          :title="'Country'"
          :disabled="store.pendingRequestsCount"
          :withSearch="true"
        />

        <ui-kit-input
          v-model="user.external_link"
          :disabled="store.pendingRequestsCount"
          placeholder="EXTERNAL LINKS"
        />

        <div class="account-settings-visibility-level">
          <ui-kit-check-box
            v-model="user.product_visibility_level"
            :value="COMMON_VISIBILITY_LEVEL"
            :disabled="store.pendingRequestsCount"
            title="For all users, does not contain explicit material"
            type="radio"
          />

          <ui-kit-check-box
            v-model="user.product_visibility_level"
            :value="NUDITY_VISIBILITY_LEVEL"
            :disabled="store.pendingRequestsCount"
            title="Can contain nudity but only for educational use"
            type="radio"
          />

          <ui-kit-check-box
            v-model="user.product_visibility_level"
            :value="EROTIC_VISIBILITY_LEVEL"
            :disabled="store.pendingRequestsCount"
            title="Can contain nudity and erotic material"
            type="radio"
          />

          <ui-kit-check-box
            v-model="user.product_visibility_level"
            :value="PORNO_VISIBILITY_LEVEL"
            :disabled="store.pendingRequestsCount"
            title="Can contain pornographic or other explicit material"
            type="radio"
          />
        </div>

        <span v-if="error" class="form-error error">
            {{ error }}
        </span>

        <div class="account-settings-divider"></div>

        <p class="ui-kit-box-content-small-text">
          <span class="ui-kit-box-content-success-text">
            If you want to change your password, please <br>
            logout <a href="#" class="link" @click.prevent="logout">here</a> and use the "Reset password" function <br>
            on the "Login" page.
          </span>
        </p>

        <div class="ui-kit-modal-content-buttons">
          <button
            :disabled="store.pendingRequestsCount"
            class="button full-width"
            type="submit"
          >UPDATE SETTINGS</button>
        </div>
      </form>
    </template>
  </ui-kit-modal>
</template>

<script setup lang="ts">
import { ref } from '@vue/reactivity'
import {
  COMMON_VISIBILITY_LEVEL,
  EROTIC_VISIBILITY_LEVEL,
  NUDITY_VISIBILITY_LEVEL,
  PORNO_VISIBILITY_LEVEL,
  ImageTemplate
} from '~/types/constants'
import { useStore } from '~/store'
import { useUserStore } from '~/store/user'
import { storeToRefs } from 'pinia'
import { useMediaStore } from '~/store/media'
import { useAuthStore } from '~/store/auth'
import { required, email } from '@vuelidate/validators'
import { navigateTo } from '#app'
import UiKitModal from '~/components/UiKit/UiKitModal.vue'
import CloseIcon from '~/assets/svg/close.svg'
import UiKitInput from '~/components/UiKit/UiKitInput.vue'
import useMedia from '~/composable/media'
import axios from 'axios'
import UiKitSelector from '~/components/UiKit/UiKitSelector.vue'
import useVuelidate from '@vuelidate/core'

const setUpAccountModal = ref<InstanceType<typeof UiKitModal>>(null)
const store = useStore()
const userStore = useUserStore()
const authStore = useAuthStore()
const currentProfile = storeToRefs(userStore)
const mediaStore = useMediaStore()
const { getUserAvatar, getImageUrl } = useMedia()
const countries = ref([{ title: currentProfile.country, key: currentProfile.country }])
const backgroundImage = ref(currentProfile.background_image)
const avatar = ref(currentProfile.avatar_image)
const error = ref('')
const user = reactive({
  username: currentProfile.username,
  email: currentProfile.email,
  description: currentProfile.description,
  external_link: currentProfile.external_link,
  product_visibility_level: currentProfile.product_visibility_level,
  background_image_id: currentProfile.background_image_id,
  avatar_image_id: currentProfile.avatar_image_id,
  country: currentProfile.country
})

const v$ = useVuelidate({
  user: {
    username: { required },
    email: { email, required }
  }
}, { user })

async function addFile(event: any) {
  const media = event.target.files || event.dataTransfer.files

  if (!media.length) {
    return
  }

  const response = await mediaStore.upload(media[0], media[0].name)

  if (event.target.id === 'uploadBackground') {
    backgroundImage.value = response.data
    user.background_image_id = response.data.id

    return
  }

  if (event.target.id === 'uploadAvatar') {
    avatar.value = response.data
    user.avatar_image_id = response.data.id

    return
  }

}

async function uploadProduct() {

  v$.value.$touch()

  if (v$.value.$error) {
    return
  }

  try {

    await userStore.updateProfile(user).then(close)

  } catch (e: any) {
    if (e.response && !e.response.data.errors) {
      error.value = 'Something went wrong! Please try again later.'

      return
    }
  }
}

function logout() {
  authStore.logout()
  navigateTo('/')
  close()
}

async function open() {
  setUpAccountModal.value?.open()
  if (countries.value.length <= 1) {
    const response = await axios.get('https://restcountries.com/v2/all')
    response.data.forEach((country: object) => countries.value.push({ title: country.name, key: country.name  }))
  }
}

function close() {
  setUpAccountModal.value?.close()
}

defineExpose({ open, close })
</script>
