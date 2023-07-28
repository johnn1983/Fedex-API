<template>
  <div
    v-show="!isCollapsed"
    class="ui-kit-dropdown"
    ref="menuDropdownRef"
  >
    <div class="ui-kit-dropdown-title">
      <label>{{ title }}</label>

      <close-icon @click="close()" class="close-icon" />
    </div>

    <div class="ui-kit-dropdown-content">
      <slot></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import CloseIcon from '~/assets/svg/close.svg'

interface Props {
  title: string
}

const isCollapsed = ref(true)
const props = defineProps<Props>()
const menuDropdownRef = ref<InstanceType<typeof HTMLElement>>()

const handleClickOutside = (event: any) => {
  if (menuDropdownRef.value!.contains(event.target)) {
    return
  }

  close()
}

function open() {
  isCollapsed.value = false

  nextTick(() => document.addEventListener('click', handleClickOutside, { capture: true }))
}

function close() {
  isCollapsed.value = true

  nextTick(() => document.removeEventListener('click', handleClickOutside, { capture: true }))
}

defineExpose({ open, close })
</script>