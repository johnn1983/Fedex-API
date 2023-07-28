<template>
  <div class="search-bar">
    <div
      :class="{ 'search-bar-content-expanded': isExpanded }"
      class="search-bar-content"
    >
      <div v-show="isExpanded" class="search-bar-content-search-bar">
        <input
          v-model="search"
          @input="findProducts"
          class="search-bar-content-input"
          type="text"
        />

        <UiKitSearchFilter
          :options="filters"
          v-model="selectedFilter"
          @changed="selectFilter"
        />
      </div>

      <div
        :class="{'search-bar-content-icon-expanded' : isExpanded}"
        @click="toggleExpanded"
        class="search-bar-content-icon"
      >
        <search-icon class="search-icon" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useProductsStore } from '~/store/products'
import SearchIcon from '~/assets/svg/search.svg'

const isExpanded = ref(false)
const products = useProductsStore()
const search = ref('')
let timer: ReturnType<typeof setTimeout> | null = null
const filters = ref([
  { title:'All', key: null },
  { title: 'Artist', key: 'author' },
  { title: 'Title', key: 'title' },
  { title: 'User', key: 'user.username' },
  { title: 'Tagname', key: 'user.tagname'}
])
const selectedFilter = ref(null)

const findProducts = () => {

  if (timer) {
    clearTimeout(timer)
  }

  timer = setTimeout(() => {

    if (search.value.length < 3) {
      products.updateFilter({ query: null })
      products.fetchAll()
      return
    }

    products.updateFilter({ query: search.value, query_by: selectedFilter.value })
    products.fetchAll()

  }, 1000)
}

function selectFilter() {
  products.updateFilter({ query: search.value, query_by: selectedFilter.value })
  products.fetchAll()
}

const toggleExpanded = () => {
  isExpanded.value = !isExpanded.value

  if (!isExpanded.value) {
    search.value = ''
    selectedFilter.value = null
    products.updateFilter({ query: null, query_by: null })
    products.fetchAll()
  }
}

</script>

