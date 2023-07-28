<template>
  <div
    v-if="route.name === 'gallery-pending' && product.status === STATUS_PENDING && getUserRole === ROLE_ADMIN"
    class="gallery-item-image-container-info gallery-item-image-container-admin"
  >
    <h4>PENDING</h4>
    <p>"{{ product.title }}"</p>
    <a href="#" class="link">see details</a>

    <div class="gallery-item-image-container-admin-buttons">
      <button
        :disabled="store.pendingRequestsCount"
        class="button"
        @click.prevent="moderateProduct(product.id, STATUS_APPROVED)"
      >APPROVE</button>

      <button
        :disabled="store.pendingRequestsCount"
        class="button"
        @click.prevent="moderateProduct(product.id, STATUS_REJECTED)"
      >DECLINE</button>
    </div>
  </div>

  <div
    v-else
    class="gallery-item-image-container-info"
  >
    <h4>"{{ product.title }}"</h4>

    <nuxt-link
      :to="`/gallery/author/${product.author}`"
      class="gallery-item-image-container-info-link"
    >
      by {{ product.author }}
    </nuxt-link>

    <nuxt-link
      :to="`/gallery/user/${product.user.username}`"
      class="gallery-item-image-container-info-link"
    >
      Uploaded by {{ product.user.username }}
    </nuxt-link>
    <p></p>
  </div>
</template>

<script setup lang="ts">
import { Product } from '~/types/products'
import { ROLE_ADMIN, STATUS_APPROVED, STATUS_PENDING, STATUS_REJECTED } from '~/types/constants'
import { useUserStore } from '~/store/user'
import { useProductsStore } from '~/store/products'
import { useStore } from '~/store'
import { storeToRefs } from 'pinia'
import { useRoute } from 'vue-router'

interface Props {
  product: Product
}
const props = defineProps<Props>()
const store = useStore()
const userStore = useUserStore()
const productsStore = useProductsStore()
const { getUserRole } = storeToRefs(userStore)
const route = useRoute()

async function moderateProduct(id: number, status: string) {
  await productsStore.update(id, { status: status })

  await productsStore.fetchAll()

  await productsStore.getPendingCount()
}
</script>