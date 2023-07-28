<template>
  <div>
    <div class="form-group" :cy-name="name">
      <a
        :class="{ 'fa-chevron-down': !isExpanded, 'fa-chevron-up': isExpanded }"
        @click="isExpanded = !isExpanded"
        class="form-arrow fa"
        cy-name="collapse-button"
      ></a>

      <input
        v-model="query"
        :class="{ 'form-control-filled': query }"
        @focus="isExpanded = true"
        @blur="close()"
        ref="queryInput"
        type="text"
        class="form-control"
      />

      <label
        v-if="placeholder"
        :class="{ 'form-label-filled': query }"
        class="form-label"
      >
        {{ placeholderWithOptions }}
      </label>

      <ul
        class="form-dropdown"
        :class="{ 'form-dropdown-opened': isExpanded }"
      >
        <li
          v-if="allOptionsLabel && withAllOptionsLabel"
          @click="select(null)"
          class="form-dropdown-item"
        >
          {{ allOptionsLabel }}
        </li>

        <li
          v-for="item in filteredResults"
          @click="select(item)"
          class="form-dropdown-item"
        >
          <span v-if="modelValue.includes(item)" class="fas fa-check"></span>
          <span class="form-dropdown-item-label">{{ item }}</span>
        </li>
      </ul>

      <span v-if="errors.$error" class="form-errors-list">
        <span
          v-for="(message, key) in errorMessages"
          v-show="errors[key].$invalid"
          :key="key"
          class="form-error error"
          v-html="message"
        ></span>
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { InputValue } from '~/types/uiKit'
import { PropType } from '@vue/runtime-core'
import { computed, ref } from '@vue/reactivity'
import { defineEmits } from 'vue'

interface Props {
  modelValue: PropType<InputValue[]>,
  placeholder: string,
  name: string,
  allOptionsLabel?: string,
  withAllOptionsLabel?: boolean,
  options?: object,
  errors?: object,
  errorMessages?: object,
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: () => ([]),
  allOptionsLabel: 'All',
  withAllOptionsLabel: true,
  options: () => ([]),
  errors: () => ({ $error: false }),
  errorMessages: () => ({}),
})

const emit = defineEmits(['update:modelValue'])

let timeout: any = 100

let isExpanded = ref(false)
let query = ref('')
const queryInput = ref(null)

let filteredResults = computed(() => {
  if (!query.value) {
    return props.options
  }

  return props.options.filter((item: string) => item.includes(query.value))
})

let placeholderWithOptions = computed(() => {
  if (props.modelValue.length) {
    const optionsList = props.modelValue.join(', ');

    return `${props.placeholder} (${optionsList})`
  }

  return props.placeholder
})

function select(value: string | null) {
  clearTimeout(timeout)

  // @ts-ignore
  queryInput.value.focus()

  if (!value) {
    emit('update:modelValue', [])

    return;
  }

  if (props.modelValue.includes(value)) {
    emit('update:modelValue', props.modelValue.filter((item) => item !== value))
  } else {
    emit('update:modelValue', [...props.modelValue, value])
  }

  query.value = ''
}

function close() {
  clearTimeout(timeout)

  timeout = setTimeout(() => isExpanded.value = false, timeout)
}
</script>
