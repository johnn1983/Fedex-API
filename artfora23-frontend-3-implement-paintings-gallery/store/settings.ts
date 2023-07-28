// @ts-ignore
import { defineStore } from 'pinia'
import type { SettingsState } from '~/types/state'
import type { Setting } from '~/types'
import axios from 'axios'

export const useSettingsStore = defineStore('settings', {
  state: (): SettingsState => ({
    items: {
      data: [],
      total: 0,
      current_page: 1
    }
  }),

  getters: {
    getByName: (state: any) => {
      return (name: string): Setting | undefined => {
        return state.items.data.find((setting: Setting) => setting.name === name)
      }
    },
    contactUsEmail() {
      const setting = this.getByName('Contact us email')

      if (!setting) {
        return ''
      }

      // @ts-ignore
      return setting.value.email
    }
  },

  actions: {
    async fetchSettings() {
      const response = await axios.get('/settings')

      // @ts-ignore
      this.items = response.data
    },

    async update(name: string, value: object) {
      return await axios.put(`/settings/${name}`, value)
    }
  }
})