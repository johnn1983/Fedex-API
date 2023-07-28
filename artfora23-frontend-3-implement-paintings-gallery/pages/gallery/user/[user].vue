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
import { useUserStore } from '~/store/user'
import { SORT_LATEST } from '~/types/gallerySettings'
import MainContainer from '~/components/Layout/MainContainer.vue'

const title = ref('')
const description = ref('')
const products = useProductsStore()
const user = useUserStore()
const route = useRoute()
const { items } = storeToRefs(products)

await useAsyncData('products',async () => {
  products.updateFilter({
    categories: null,
    status: null,
    user_id: null,
    order_by: SORT_LATEST,
    username: route.params.user
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