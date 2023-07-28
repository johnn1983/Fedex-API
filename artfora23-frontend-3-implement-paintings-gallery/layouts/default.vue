<template>
  <div class="page-container">
    <the-header
      v-show="$route.name !== 'products-id'"
      @open-sign-up-modal="modalsComponentRef.openSignUpModal()"
      @open-add-product-modal="modalsComponentRef.openAddProductModal()"
      @open-contact-us-modal="modalsComponentRef.openContactUsModal()"
      @open-gallery-settings-modal="modalsComponentRef.openGallerySettingsModal()"
      @open-set-up-account-modal="modalsComponentRef.openSetUpAccountModal()"
    />

    <slot/>

    <modals-component ref="modalsComponentRef" />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRuntimeConfig } from '#app'
import TheHeader from '~/components/Layout/TheHeader.vue'
import ModalsComponent from '~/components/Layout/ModalsComponent.vue'

const config = useRuntimeConfig()

useHead({
  script: [
    { children: `var mtcaptchaConfig = { "sitekey": "${config.public.MTCAPTCHA_SITE_KEY}", "widgetSize": "mini", "render": "explicit" }` },
    {
      src: 'https://service.mtcaptcha.com/mtcv1/client/mtcaptcha.min.js',
      async: true
    },
    {
      src: 'https://service2.mtcaptcha.com/mtcv1/client/mtcaptcha2.min.js',
      async: true
    }
  ]
})


const modalsComponentRef = ref<InstanceType<typeof ModalsComponent>>(null)
</script>

<style lang="scss">
  @import '~/assets/scss/index.scss';
</style>