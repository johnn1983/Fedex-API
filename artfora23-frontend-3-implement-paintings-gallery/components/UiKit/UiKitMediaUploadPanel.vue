<template>
  <div>
    <div class="row">
      <no-ssr placeholder="Loading...">
        <photo-provider>
          <photo-consumer
            v-for="image in images"
            :key="image.id"
            :intro="getImageUrl(image)"
            :src="getImageUrl(image)"
            class="col"
          >
            <div class="thumbnail">
              <img :src="getImageUrl(image)" alt="image" />

              <trash-icon
                @click.stop="remove(image)"
                class="media-remove"
              />
            </div>

          </photo-consumer>

          <div
            v-for="video in videos"
            :key="video.id"
            class="PhotoConsumer col media-upload-container"
          >
            <video width="300" height="500" controls>
              <source :src="getImageUrl(video)" type="video/mp4">

              Your browser does not support the video tag.
            </video>

            <trash-icon
              @click.stop="remove(video)"
              class="media-remove"
            />
          </div>

          <div class="PhotoConsumer col media-upload-container">
            <div class="media-upload" @click="uploader.open()">
              +
            </div>
          </div>
        </photo-provider>
      </no-ssr>
    </div>

    <ui-kit-media-upload
      @uploaded="onUpload"
      name="image_uploader"
      ref="uploader"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from '@vue/reactivity'
import { Media } from '~/types'
import { PhotoProvider, PhotoConsumer } from 'vue3-photo-preview'
import useMedia from '~/composable/media'
import UiKitMediaUpload from '~/components/UiKit/UiKitMediaUpload.vue'
import TrashIcon from '~/assets/svg/trash.svg'
import { UiKitMediaUploadPanelModelValue } from '~/types/uiKit'
import 'vue3-photo-preview/dist/index.css'

interface Props {
  modelValue: UiKitMediaUploadPanelModelValue[],
  name: string
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: () => ([])
})

const { getImageUrl, isImage, isVideo } = useMedia()
const emit = defineEmits(['update:modelValue'])

const uploader = ref<InstanceType<typeof UiKitMediaUpload>>(null)

const images = computed(() => props.modelValue.filter((media) => isImage(media as Media)))
const videos = computed(() => props.modelValue.filter((media) => isVideo(media as Media)))

function onUpload(media: Media) {
  emit('update:modelValue', [...props.modelValue, media])
}

function remove(media: Media) {
  emit('update:modelValue', props.modelValue.filter((item) => (item as Media).id !== media.id))
}
</script>

<style lang="scss" scoped>
.thumbnail { width: 300px; height: 500px; border-radius: 5px; margin-bottom: 1rem; cursor: pointer; }

.media {
  &-upload-container { display: inline-block; position: relative; vertical-align: top; }
  &-upload {
    width: 300px; height: 500px; border: dashed 1px lightblue; cursor: pointer; display: flex; justify-content: center;
    align-items: center; font-size: 2rem; color: lightblue;
  }
  &-remove { display: none; }
  &-list-item:hover &-remove { display: block; }
  &-remove {
    position: absolute; right: 1rem; top: 1rem; fill: red; display: block; width: 30px; height: 30px; opacity: 0.5;

    &:hover { opacity: 1; }
  }
}
</style>
