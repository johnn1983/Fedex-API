<template>
  <div>
    <fieldset class="form-group">
      <label
        v-if="title"
        :for="name"
        class="form-title"
      >
        {{ title }}
      </label>

      <textarea
        :name="name"
        :value="modelValue"
        :cy-name="name"
        :class="{ 'form-control-filled': modelValue }"
        autocomplete="off"
        class="form-control form-control-textarea"
        @input="onChanged"
      />

      <span
        v-if="placeholder"
        :for="name"
        class="form-label"
      >
          {{ placeholder }}
      </span>

      <span v-if="errors.$error" class="form-errors-list">
        <span
          v-for="(message, key) in errorMessages"
          v-show="errors[key].$invalid"
          :key="key"
          class="form-error error"
          v-html="message"
        ></span>
      </span>
    </fieldset>
  </div>
</template>

<script lang="ts" setup>
import { PropType } from "@vue/runtime-core"
import { defineEmits } from 'vue'
import { InputValue } from '~/types/uiKit'

interface Props {
  modelValue: PropType<InputValue>,
  placeholder: string,
  mask?: string,
  title?: string,
  name?: string,
  type?: string,
  max?: number,
  min?: number,
  step?: number,
  timeout?: number,
  errors?: object,
  errorMessages?: object
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  min: 0,
  step: 1,
  timeout: 0,
  errors: () => ({ $error: false }),
  errorMessages: () => ({})
})

const emit = defineEmits(['update:modelValue'])

let timeout: any = null

function input($event: any) {
    let value = props.mask ? $event : $event.target.value

    if (props.type === 'number') {
      value = parseFloat(value)
    }

    emit('update:modelValue', value)
  }

function onChanged(value: any) {
  if (props.timeout) {
    clearTimeout(timeout)

    timeout = setTimeout(() => input(value), props.timeout)
  } else {
    input(value)
  }
}
</script>
