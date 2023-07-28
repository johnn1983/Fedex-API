<template>
  <ui-kit-modal
    :with-footer="false"
    title="AI safe"
    ref="aiSafeDescription"
  >
    <template v-slot:content>
      <div class="ai-safe-description">
        <p>In an attempt to hide your images from server
          crawling bots, we will:</p>
        <ul>
          <li>Add rubbish tags.</li>
          <li>Add copyright.</li>
          <li>Add invisible watermark.</li>
        </ul>
        <p>Which hopefully decrease the likelyhood that your
          images will be found and (mis)used.</p>
        <p>The downside is that your images can't be used
          when we promote ARTfora and you may miss
          an opportunity to get your images promoted.</p>
        <a
          href="https://en.wikipedia.org/wiki/Artificial_intelligence_art"
          target="_blank"
          class="link"
        >Read more about AI art</a>

        <br>

        <a
          href="https://invisiblewatermark.net/how-invisible-watermarks-work.html"
          target="_blank"
          class="link"
        >Read more about invisible watermark</a>
      </div>
      <div class="ui-kit-modal-content-buttons">
        <button class="button full-width" @click="aiSafeDescription.close()">CLOSE</button>
      </div>
    </template>
  </ui-kit-modal>
</template>

<script setup lang="ts">
import { ref } from '@vue/reactivity'
import UiKitModal from '~/components/UiKit/UiKitModal.vue'

const aiSafeDescription = ref<InstanceType<typeof UiKitModal>>(null)

function open() {
  aiSafeDescription.value?.open()
}

function close() {
  aiSafeDescription.value?.close()
}

defineExpose({ open, close })
</script>