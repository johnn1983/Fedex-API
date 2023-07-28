<template>
  <div
    v-if="isShown"
    class="ui-kit-modal"
  >
    <div class="ui-kit-modal-background" @click="closeByClickOutsideAction()"></div>

    <ui-kit-box
      :title="title"
      :with-header="withHeader"
      :with-footer="withFooter"
      :close-btn-as-home-link="closeBtnAsHomeLink"
      @close="close()"
      class="ui-kit-modal-content"
    >

      <template v-slot:customHeader>
        <slot name="customHeader"></slot>
      </template>

      <template v-slot:content>
        <slot name="content"/>
      </template>

      <template v-slot:footer>
        <slot name="buttons"/>
      </template>
    </ui-kit-box>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Props {
  title?: string,
  closeByClickOutside?: boolean,
  withFooter?: boolean,
  withHeader?: boolean
  closeBtnAsHomeLink?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  closeByClickOutside: false,
  withFooter: true,
  withHeader: true,
  closeBtnAsHomeLink: false
})
const isShown = ref(false)

function open() {
  // @ts-ignore
  document.querySelector('body').style.overflow = 'hidden'
  isShown.value = true
}

function close() {
  // @ts-ignore
  document.querySelector('body').style.overflow = 'auto'
  isShown.value = false
}

function closeByClickOutsideAction() {
  if (props.closeByClickOutside) {
    close()
  }
}

defineExpose({ open, close })
</script>