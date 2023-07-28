import { defineStore } from 'pinia'
import { JUSTIFIED_GALLERY_VIEW_TYPE, } from '~/types/products'
import { SORT_LATEST } from '~/types/gallerySettings'

export const useSettingsGalleryStore = defineStore('settingsGallery', {
  persist: true,

  state: (): any => ({
    viewType: JUSTIFIED_GALLERY_VIEW_TYPE,
    order_by: SORT_LATEST
  }),

  actions: {
    setData(type: string, sort: string) {
      this.viewType = type
      this.order_by = sort
    }
  }
})
