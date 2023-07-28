<template>
  <ui-kit-modal :name="name" :title="title" ref="modal">
    <template v-slot:content>
      <div class="cropper-container">
        <Cropper
          v-show="image"
          :src="image"
          ref="cropper"
          class="cropper-content"
        />

        <video
          v-if="video"
          width="320"
          height="240"
          controls
        >
          <source :src="getImageUrl(video)" type="video/mp4">

          Your browser does not support the video tag.
        </video>

        <label
          v-show="!image && !video"
          class="flex-center"
        >
          Please upload media
        </label>
      </div>
    </template>

    <template v-slot:buttons>
      <div class="flex-right">
        <input
          @change="loadImage($event)"
          id="imageUploader"
          ref="imageUploader"
          accept="image/*"
          class="hidden"
          type="file"
        />

        <input
          @change="loadVideo($event)"
          id="videoUploader"
          ref="videoUploader"
          accept="video/*"
          class="hidden"
          type="file"
        />

        <button
          v-if="!noImage"
          :disabled="!!store.pendingRequestsCount"
          class="button button-info"
          type="button"
          @click="imageUploader.click()"
        >
          Upload Photo
        </button>

        <button
            v-if="!noVideo"
          :disabled="!!store.pendingRequestsCount"
          class="button button-info"
          type="button"
          @click="videoUploader.click()"
        >
          Upload Video
        </button>

        <button
          v-if="image || video"
          :disabled="!!store.pendingRequestsCount"
          @click="save()"
          class="button button-success"
          type="button"
        >
          Save
        </button>

        <button
          @click="reset()"
          class="button button-default"
          type="button"
        >
          Cancel
        </button>
      </div>
    </template>
  </ui-kit-modal>
</template>

<script setup lang="ts">
import { Cropper } from 'vue-advanced-cropper';
import { ref } from '@vue/reactivity'
import { useMediaStore } from '~/store/media'
import { useStore } from '~/store'
import { AxiosResponse } from 'axios'
import { Media } from '~/types'
import UiKitModal from './UiKitModal.vue'
import useMedia from '~/composable/media'
import 'vue-advanced-cropper/dist/style.css'

interface Props {
  title?: string,
  name: string,
  noImage?: boolean,
  noVideo?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Upload image',
  noImage: false,
  noVideo: false
})

const emit = defineEmits([ 'uploaded' ])
const { getImageUrl } = useMedia()

const store = useStore()
const mediaStore = useMediaStore()

const error = ref('')
const image = ref(null)
const video = ref('')
const imageName = ref(null)
const imageUploader = ref<InstanceType<typeof HTMLInputElement>>(null)
const videoUploader = ref<InstanceType<typeof HTMLInputElement>>(null)
const modal = ref<InstanceType<typeof UiKitModal>>(null)
const cropper = ref<InstanceType<typeof Cropper>>(null)

function reset() {
  modal.value?.close();

  image.value = null;
  imageName.value = null;
}

function loadImage(event: any) {
  if (event.target.files && event.target.files[0]) {
    const reader = new FileReader()

    imageName.value = event.target.files[0].name
    reader.onload = (e: any) => image.value = e.target.result

    reader.readAsDataURL(event.target.files[0])
  }
}

async function loadVideo(event: any) {
  if (event.target.files && event.target.files[0]) {
    const response = await mediaStore.upload(event.target.files[0], event.target.files[0].name)

    video.value = response.data
  }
}

function save() {
  if (video.value) {
    emit('uploaded', video.value)

    return reset()
  }

  const { canvas } = cropper.value.getResult()

  if (canvas) {
    canvas.toBlob((blob: Blob | null): void => {
      mediaStore.upload(blob, imageName.value)
        .then((response: AxiosResponse<Media>) => emit('uploaded', response.data))
        .then(() => reset())
    }, 'image/jpeg')
  }
}

function open() {
  modal.value?.open()
}

defineExpose({ open })
</script>

<style scoped>
.cropper-container {
  min-width: 30vw;
  max-width: 80vw;
  overflow: hidden;
  min-height: 50vh;
  max-height: 80vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.cropper-content {
  max-height: 50vh;
  min-width: 300px;
}
</style>
