<template>
  <div class="switch-checkbox">

    <label
      v-for="option in options"
      :class="{ 'switch-checkbox-active': option.key === selectedOption }"
      :for="option.key"
      class="switch-checkbox-label"
    >
      <input
        :id="option.key"
        v-model="selectedOption"
        :value="option.key"
        class="hidden"
        type="radio"
      />
      {{ option.title }}
    </label>

  </div>
</template>

<script setup lang="ts">
import { OptionItem } from '~/types/uiKit'
import {computed} from "vue";

interface props {
  options: OptionItem[],
  modelValue: OptionItem
}
const props = defineProps<props>()
const emit = defineEmits(['update:modelValue'])

const selectedOption = computed({
  get() {
    return props.modelValue
  },
  set(newValue) {
    emit('update:modelValue', newValue)
  }
})

</script>