<template>
  <main class="home-page">
    <main-container>
      <gallery :items="items" />
    </main-container>
  </main>
</template>

<script setup lang="ts">
import { ref } from '@vue/reactivity'
import { useHead } from '@vueuse/head'
import { useProductsStore } from '~/store/products'
import { storeToRefs } from 'pinia'
import { useAsyncData } from '#app'
import { onMounted } from 'vue'
import { navigateTo } from '#imports'
import Gallery from '~/components/Gallery/Gallery.vue'
import MainContainer from '~/components/Layout/MainContainer.vue'
import { useSettingsGalleryStore } from '~/store/gallerySettings'
import { STATUS_APPROVED } from '~/types/constants'

const title = ref('')
const description = ref('')
const products = useProductsStore()
const { items } = storeToRefs(products)
const route = useRoute()
const gallerySettings = useSettingsGalleryStore()
const { order_by } = storeToRefs(gallerySettings)

onMounted(async () => {
  if (!route.params.id) {
    navigateTo('/')
  }
})

await useAsyncData('products',async () => {
  products.updateFilter({
    categories: [route.params.id],
    status: STATUS_APPROVED,
    user_id: null,
    page: 1,
    order_by,
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

</script>