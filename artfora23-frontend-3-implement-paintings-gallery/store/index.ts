// @ts-ignore
import { defineStore } from 'pinia'
import type { RootState } from '~/types/state'
import { ContactForm } from '~/types/contactForm'
import axios from 'axios'

export const useStore = defineStore({
  id: 'root',

  state: (): RootState => ({
    title: 'Usssa events management',
    pendingRequestsCount: 0
  }),

  actions: {
    async send(data: ContactForm) {
      return await axios.post('/contact-us', data)
    }
  }
})