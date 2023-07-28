<template>
  <div class="product-container">
    <div class="product-container-btn">

      <button class="product-container-btn-info" @click="toggleSidebar">
        <span v-show="!isShown">i</span>
        <arrow-left v-show="isShown" class="hide-icon" />
      </button>

      <button class="product-container-btn-close" @click="toBack()" >
        <close-icon/>
      </button>

    </div>

    <div class="product-container-images">
      <img
        v-for="(image, index) in item.media"
        v-show="index === currentImage"
        :src="getImageUrl(image, ImageTemplate.FullSize)"
        :alt="tags"
      >

      <div class="product-container-images-next" v-show="currentImage + 1 < item.media.length" @click="toNextImage()">
        <next-icon class="next-icon" />
      </div>

      <div class="product-container-images-prev" v-show="currentImage - 1 >= 0" @click="toPrevImage()">
        <next-icon class="prev-icon"/>
      </div>

    </div>

    <product-sidebar v-show="isShown" :product="item" />

  </div>
</template>

<script setup lang="ts">
import { useAsyncData, useRoute, useRouter} from '#app'
import { useProductsStore } from '~/store/products'
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import { ImageTemplate } from '~/types/constants'
import CloseIcon from '~/assets/svg/close.svg'
import ArrowLeft from '~/assets/svg/arrow-left.svg'
import ProductSidebar from '~/components/Products/ProductSidebar.vue'
import NextIcon from '~/assets/svg/next.svg'
import useMedia from '~/composable/media'
import randomWords from 'random-words'

const route = useRoute()
const router = useRouter()
const products = useProductsStore()
const { item } = storeToRefs(products)
const isShown = ref(false)
const currentImage = ref(0)
const { getImageUrl } = useMedia()

await useAsyncData('get-product', async () => {
  await products.fetch(route.params.id as string)
})

function toggleSidebar() {
  isShown.value = !isShown.value
}

function toBack() {
  router.go(-1)
}

function toNextImage() {
  currentImage.value = currentImage.value + 1
}

function toPrevImage() {
  currentImage.value = currentImage.value - 1
}

const tags = computed(() => item.value.is_ai_safe ? randomWords(10).join(', ') : item.value.tags)
</script>