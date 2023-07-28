<template>
  <div>
    <div class="ui-kit-tabs">
      <a
        v-for="(tab, index) in tabs"
        @click.prevent="selectedIndex = index"
        :key="index"
        :class="{ active: index === selectedIndex }"
        class="text ui-kit-tabs-item"
      >
        {{ tab.props.title }}
      </a>
    </div>

    <div class="ui-kit-tabs-content">
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  onBeforeMount,
  onMounted,
  onBeforeUpdate,
  provide,
  reactive,
  useSlots,
} from 'vue'
import { ref } from '@vue/reactivity'
import UiKitTab from '~/components/UiKit/UiKitTab.vue'

const selectedIndex = ref<number>(0)
const tabs = ref([])
const counter = ref<number>(0)

const slots = useSlots()

provide('SelectedTab', selectedIndex)
provide('TabsCounter', counter)

const selectTab = (index: number) => selectedIndex.value = index

const update = () => {
  if (slots.default) {
    tabs.value = slots.default().filter((child) => child.type.name === 'UiKitTab')
  }
}

onBeforeMount(() => update())
onBeforeUpdate(() => update())

onMounted(() => {
  selectTab(0)
})

tabs.value = slots.default().filter((child) => child.type.name === 'UiKitTab');
</script>