<template>
  <div>
    <div
      :class="{
        'ui-kit-selector-active': isExpanded,
        'ui-kit-selector-sorting-mode': sortingMode
      }"
      @click="isExpanded = !isExpanded"
      class="ui-kit-selector"
    >
      <label
        v-if="!withSearch"
        :class="{ 'ui-kit-selector-title-filled': modelValue }"
        class="ui-kit-selector-title"
      >
        {{ title }}
      </label>

      <label v-if="selectedValue && !withSearch" class="ui-kit-selector-value">
        {{ selectedValue }}
      </label>

      <label v-if="withSearch" for="search" @click.prevent="isExpanded = false" class="ui-kit-selector-search">
        <input
          id="search"
          v-model="search"
          type="text"
          class="ui-kit-selector-search-input"
        >
      </label>

      <next-icon
        :class="{'ui-kit-selector-arrow-open': isExpanded}"
        class="ui-kit-selector-arrow"
      />

      <ul
        v-if="isExpanded"
        class="ui-kit-selector-dropdown"
      >
        <li
          v-for="option in optionsArray"
          :key="option.key"
          @click="onClick(option)"
          class="ui-kit-selector-dropdown-item"
        >
          {{ option.title }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from '@vue/reactivity'
import { defineProps, defineEmits } from 'vue'
import { useRouter } from 'vue-router'
import { OptionItem } from '~/types/uiKit'
import NextIcon from '~/assets/svg/next.svg'
import ArrowUpIcon from '~/assets/svg/arrow-up.svg'
import ArrowDownIcon from '~/assets/svg/arrow-down.svg'

interface Props {
  modelValue: string | number | null
  title: string
  options: OptionItem[]
  sortingMode?: boolean
  withSearch?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  options: () => ([]),
  sortingMode: false,
  withSearch: false
})

const emit = defineEmits(['update:modelValue', 'changed'])
const router = useRouter()

const optionsArray = ref(props.options)
const selectedOption = computed<OptionItem | undefined>(
    () => props.options.find((option) => option.key === props.modelValue)
)

const selectedValue = computed<string>(() => selectedOption.value ? selectedOption.value.title : '')

const search = computed({
  get() {
    return isExpanded.value || selectedValue.value.length ? selectedValue.value : props.title
  },
  set(searchValue) {
    if (searchValue.length === 0) {
      optionsArray.value = props.options
    }
    const regexp = new RegExp(searchValue, "i")
    optionsArray.value = props.options.filter((option) => regexp.test(option.title))

    selectedOption.value.title = searchValue
  }
})
function onClick (option: OptionItem) {
  emit('update:modelValue', option.key)

  emit('changed', option.payload)
}

const isExpanded = ref(false)
</script>