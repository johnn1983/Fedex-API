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
import { storeToRefs } from 'pinia'
import { ref } from '@vue/reactivity'
import { useAsyncData } from '#app'
import { STATUS_APPROVED } from '~/types/constants'
import { useSettingsGalleryStore } from '~/store/gallerySettings'
import MainContainer from '~/components/Layout/MainContainer.vue'

const title = ref('')
const description = ref('')
const products = useProductsStore()
const { items } = storeToRefs(products)
const gallerySettings = useSettingsGalleryStore()
const { order_by } = storeToRefs(gallerySettings)

await useAsyncData('products',async () => {
  products.updateFilter({
    categories: null,
    status: STATUS_APPROVED,
    user_id: null,
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