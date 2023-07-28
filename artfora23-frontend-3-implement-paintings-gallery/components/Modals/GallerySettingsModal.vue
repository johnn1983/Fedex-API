<template>
  <ui-kit-modal title="Gallery settings" ref="gallerySettings">
    <template v-slot:content>
      <div class="gallery-settings">

        <div class="gallery-settings-item">
          <h2 class="gallery-settings-item-title">SORT BY: </h2>

          <ui-kit-switch v-model="selectedSort" :options="optionsSort" />
        </div>

        <div class="gallery-settings-middle-divider"></div>

        <div class="gallery-settings-item">

          <ui-kit-check-box
            v-model="GalleryViewType"
            :value="JUSTIFIED_GALLERY_VIEW_TYPE"
            :show-checkbox="false"
            type="radio"
          >
            <JustifiedGalleryIcon
              :class="{ 'gallery-settings-item-view-active': GalleryViewType === JUSTIFIED_GALLERY_VIEW_TYPE }"
              class="gallery-settings-item-view"
            />
          </ui-kit-check-box>

          <ui-kit-check-box
            v-model="GalleryViewType"
            :value="SQUARE_GALLERY_VIEW_TYPE"
            :show-checkbox="false"
            type="radio"
          >
            <SquareGalleryIcon
              :class="{ 'gallery-settings-item-view-active': GalleryViewType === SQUARE_GALLERY_VIEW_TYPE }"
              class="gallery-settings-item-view"
            />
          </ui-kit-check-box>

          <ui-kit-check-box
            v-model="GalleryViewType"
            :value="DETAILS_GALLERY_VIEW_TYPE"
            :show-checkbox="false"
            type="radio"
          >
            <DetailsGalleryIcon
              :class="{ 'gallery-settings-item-view-active': GalleryViewType === DETAILS_GALLERY_VIEW_TYPE }"
              class="gallery-settings-item-view"
            />
          </ui-kit-check-box>
        </div>
      </div>
    </template>
    <template v-slot:buttons>
      <button class="full-width button" @click="updateSettings()">SAVE SETTINGS</button>
    </template>
  </ui-kit-modal>
</template>

<script setup lang="ts">
import { DETAILS_GALLERY_VIEW_TYPE, JUSTIFIED_GALLERY_VIEW_TYPE, SQUARE_GALLERY_VIEW_TYPE } from '~/types/products'
import { SORT_LATEST, SORT_RANDOM } from '~/types/gallerySettings'
import { ref } from '@vue/reactivity'
import { useSettingsGalleryStore } from '~/store/gallerySettings'
import { useProductsStore } from '~/store/products'
import { OptionItem } from '~/types/uiKit'
import UiKitModal from '~/components/UiKit/UiKitModal.vue'
import UiKitSwitch from '~/components/UiKit/UiKitSwitch.vue'
import JustifiedGalleryIcon from '~/assets/svg/justified-gallery.svg'
import SquareGalleryIcon from '~/assets/svg/square-gallery.svg'
import DetailsGalleryIcon from '~/assets/svg/details-gallery.svg'

const gallerySettingsStore = useSettingsGalleryStore()
const products = useProductsStore()
const gallerySettings = ref<InstanceType<typeof UiKitModal>>(null)
const selectedSort = ref(gallerySettingsStore.order_by)
const GalleryViewType = ref(gallerySettingsStore.viewType)
const optionsSort: OptionItem[] = [
  { title: 'LATEST', key: SORT_LATEST },
  { title: 'RANDOM', key: SORT_RANDOM }
]

async function updateSettings() {
  gallerySettingsStore.setData(GalleryViewType.value, selectedSort.value)

  products.updateFilter({ order_by: selectedSort.value })

  await products.fetchAll()

  close()
}

function open() {
  gallerySettings.value?.open()
}

function close() {
  gallerySettings.value?.close()
}

defineExpose({ open, close })
</script>