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

      <client-only>
        <vue-editor
          :name="name"
          :model-value="modelValue"
          :placeholder="placeholder"
          :cy-name="name"
          @update:model-value="$emit('update:modelValue', $event)"
          class="form-control"
          ref="quillEditor"
          theme="snow"
        />
      </client-only>

      <span v-if="errors.$error" class="form-errors-list">
        <span
          v-for="(message, key) in errorMessages"
          v-show="errors[key].$invalid"
          v-html="message"
          :key="key"
          class="form-error error"
        ></span>
      </span>
    </fieldset>
  </div>
</template>

<script lang="ts" setup>
import { PropType } from '@vue/runtime-core'
import { defineEmits } from 'vue'
import { InputValue } from '~/types/uiKit'
import { VueEditor } from 'vue3-editor'

interface Props {
  modelValue: PropType<InputValue>,
  placeholder: string,
  title?: string,
  name?: string,
  errors?: object,
  errorMessages?: object
}

const props = withDefaults(defineProps<Props>(), {
  errors: () => ({ $error: false }),
  errorMessages: () => ({})
})

const emit = defineEmits(['update:modelValue'])

let timeout: any = null
</script>
