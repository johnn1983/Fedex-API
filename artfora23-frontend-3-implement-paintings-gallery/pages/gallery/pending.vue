<template>
  <main class="home-page">
    <main-container>
      <gallery :items="items" />
    </main-container>
  </main>
</template>

<script setup lang="ts">
import { useHead } from '@vueuse/head'
import { useProductsStore } from '~/store/products'
import { useAuthStore } from '~/store/auth'
import { useUserStore } from '~/store/user'
import { storeToRefs } from 'pinia'
import { ref } from '@vue/reactivity'
import { useAsyncData } from '#app'
import { onMounted } from 'vue'
import { ROLE_ADMIN, STATUS_PENDING } from '~/types/constants'
import { SORT_LATEST } from '~/types/gallerySettings'
import MainContainer from '~/components/Layout/MainContainer.vue'
import { navigateTo } from '#imports'

const title = ref('')
const description = ref('')
const products = useProductsStore()
const authStore = useAuthStore()
const userStore = useUserStore()
const { isAuthorized } = storeToRefs(authStore)
const { items } = storeToRefs(products)
const { getUserRole } = storeToRefs(userStore)

await useAsyncData('products',async () => {
  products.updateFilter({
    categories: null,
    status: STATUS_PENDING,
    user_id: null,
    order_by: SORT_LATEST,
    page: 1,
    username: null,
    author: null
  })

  await products.fetchAll()
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
  if (!isAuthorized.value || getUserRole.value !== ROLE_ADMIN) {
    navigateTo('/')
  }
})

</script>