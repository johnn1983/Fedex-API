<template>
  <main class="home-page"></main>
</template>

<script setup lang="ts">
import { ref } from '@vue/reactivity'
import { useHead } from '@vueuse/head'
import { onMounted } from 'vue'
import { useAuthStore } from '~/store/auth'
import { useUserStore } from '~/store/user'
import { navigateTo } from '#imports'
import { useRoute } from 'vue-router'
import { VerifyData } from '~/types/auth'

const title = ref('')
const description = ref('')
const authStore = useAuthStore()
const userStore = useUserStore()
const route = useRoute()

const verifyData: VerifyData = reactive({
  token: route.query.token as string || ''
})

const redirect = route.query.redirect || '/'

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
    startVerifyEmail()
  } else {
    navigateTo('/')
  }
})

async function startVerifyEmail() {
  try {
    await authStore.verifyEmail(verifyData)
    await userStore.fetch()

    navigateTo(redirect as string)
  } catch (e) {
    alert('Something went wrong! Please try again later.')

    navigateTo('/')
  }
}
</script>