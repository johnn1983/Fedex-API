<template>
  <div class="categories">

    <div class="categories-body">

      <nuxt-link
        v-if="isAuthorized && getUserRole === ROLE_USER"
        to="/gallery/my-images"
        class="categories-body-item categories-body-item-parents"
      >
        My images
      </nuxt-link>

      <nuxt-link
        v-if="isAuthorized && getUserRole === ROLE_ADMIN"
        to="/gallery/pending"
        class="categories-body-item categories-body-item-parents"
      >
        Pending ({{ pendingCount }})
      </nuxt-link>

      <nuxt-link
        @click="clearSubCategories()"
        class="categories-body-item categories-body-item-parents"
        to="/"
      >
        All
      </nuxt-link>

      <nuxt-link
        v-for="(category) in items"
        @click="clearSubCategories"
        :to="`/gallery/${category.id}`"
        class="categories-body-item categories-body-item-parents"
      >
        {{ category.title }}
      </nuxt-link>

    </div>

    <div class="categories-body" v-if="subCategories">
      <label
        v-for="item in subCategories"
        :class="{'categories-body-item-active': selectedSubCategories.some((el) => el === item.id)}"
        :for="item.title"
        class="categories-body-item categories-body-item-children"
      >
        {{ item.title }}
        <input
          :id="item.title"
          v-model="selectedSubCategories"
          @change="selectSubCategory()"
          :value="item.id"
          type="checkbox"
        >
      </label>
    </div>

    <div class="categories-body" v-if="$route.path === '/gallery/my-images'">

      <label
        :class="{'categories-body-item-active': !selectedStatus}"
        for="all"
        class="categories-body-item categories-body-item-children"
      >
        All
        <input
          id="all"
          v-model="selectedStatus"
          @change="selectAllStatuses()"
          type="radio"
        >
      </label>

      <label
        v-for="status in statuses"
        :class="{'categories-body-item-active': selectedStatus === status}"
        :for="status"
        class="categories-body-item categories-body-item-children"
      >
        {{ status }}
        <input
          :id="status"
          v-model="selectedStatus"
          @change="selectStatus()"
          :value="status"
          type="radio"
        >
      </label>

    </div>
  </div>
</template>
<script setup lang="ts">
import { computed, ref } from '@vue/reactivity'
import { useCategoriesStore } from '~/store/categories'
import { storeToRefs } from 'pinia'
import { useAsyncData } from '#app'
import { useUserStore } from '~/store/user'
import { useAuthStore } from '~/store/auth'
import { useProductsStore } from '~/store/products'
import { ROLE_ADMIN, ROLE_USER, STATUS_APPROVED, STATUS_PENDING, STATUS_REJECTED } from '~/types/constants'
import { Category } from '~/types/categories'

const categoriesStore = useCategoriesStore()
const userStore = useUserStore()
const { getUserRole } = storeToRefs(userStore)
const authStore = useAuthStore()
const { isAuthorized } = storeToRefs(authStore)
const productStore = useProductsStore()
const { items } = storeToRefs(categoriesStore)
const { pendingCount } = storeToRefs(productStore)
const route = useRoute()
const selectedSubCategories = ref([])
const subCategories = computed(() => items.value.find((category: Category) => category.id === Number(route.params.id))?.children ?? null)
const statuses = ref([ STATUS_APPROVED, STATUS_PENDING, STATUS_REJECTED ])
const selectedStatus = ref('')
function clearSubCategories() {
  selectedStatus.value = ''
  selectedSubCategories.value = []
}
function selectSubCategory() {

  if (selectedSubCategories.value.length === 0) {
    productStore.updateFilter({ categories: [route.params.id] })
    productStore.fetchAll()

    return
  }

  productStore.updateFilter({ categories: selectedSubCategories.value })
  productStore.fetchAll()
}

function selectAllStatuses() {
  selectedStatus.value = ''
  productStore.updateFilter({ status: null })
  productStore.fetchAll()
}

function selectStatus() {
  productStore.updateFilter({ status: selectedStatus.value })
  productStore.fetchAll()
}

await useAsyncData('categories', async () => await categoriesStore.fetch())

</script>