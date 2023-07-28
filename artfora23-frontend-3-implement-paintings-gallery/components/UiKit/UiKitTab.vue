<template>
  <div class="tab" v-show="isActive">
    <slot></slot>
  </div>
</template>

<script lang="ts">
export default { name: 'UiKitTab' }
</script>

<script setup lang="ts">
import { onBeforeMount, ref, inject, watch } from 'vue'
import type { Ref } from 'vue'

const index = ref(0)
const isActive = ref(false)

const selectedTab = inject<Ref<number>>('SelectedTab')
const counter = inject<Ref<number>>('TabsCounter')

watch(
  () => selectedTab?.value,
  () => {
    isActive.value = index.value === selectedTab?.value
  }
)

onBeforeMount(() => {
  index.value = counter!.value
  counter!.value++
  isActive.value = index.value === selectedTab?.value
})
</script>