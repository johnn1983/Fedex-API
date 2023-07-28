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
import MainContainer from '~/components/Layout/MainContainer.vue'
import {SORT_LATEST} from "~/types/gallerySettings";

const title = ref('')
const description = ref('')
const products = useProductsStore()
const user = useUserStore()
const { id } = storeToRefs(user)
const { items } = storeToRefs(products)

watch(id, (newUserId) => {
  products.updateFilter({
    categories: null,
    status: null,
    user_id: newUserId,
    order_by: SORT_LATEST,
    query_by: null,
    query: null
  })

  products.fetchAll()
})

await useAsyncData('products',async () => {
  products.updateFilter({
    categories: null,
    status: null,
    user_id: id,
    order_by: SORT_LATEST,
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