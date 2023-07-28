<template>
  <main class="home-page"></main>
</template>

<script setup lang="ts">
import { ref } from '@vue/reactivity'
import { useHead } from '@vueuse/head'
import { onMounted } from 'vue'
import { useAuthStore } from '~/store/auth'
import { navigateTo } from '#imports'
import { useRoute } from 'vue-router'
import { CheckResetPasswordTokenData } from '~/types/auth'

const title = ref('')
const description = ref('')
const authStore = useAuthStore()
const route = useRoute()

const checkResetPasswordTokenData: CheckResetPasswordTokenData = reactive({
  token: route.query.token as string || ''
})

useHead({
  title: title,
  meta: [
    { name: 'description', content: description },
    { name: 'content-type', content: 'text/html; charset=UTF-8' },
    { name: 'viewport', content: 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' }
  ]
})

onMounted(() => {
  if (process.client && route.query.token) {
    startVerifyToken()
  } else {
    navigateTo('/')
  }
})

async function startVerifyToken() {
  try {
    await authStore.checkResetPasswordToken(checkResetPasswordTokenData)
  } catch (e) {
    alert('Something went wrong! Please try again later.')

    navigateTo('/')
  }
}
</script>