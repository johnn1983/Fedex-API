import { defineStore } from 'pinia'
import { User } from '~/types'
import axios from 'axios'
const initState = (): User => ({
  id: 0,
  username: '',
  tagname: '',
  email: '',
  password: '',
  phone: '',
  description: '',
  country: '',
  background_image_id: null,
  avatar_image_id: null,
  role_id: 0,
  external_link: null,
  email_verified_at: null,
  deleted_at: null,
  product_visibility_level: null,
  background_image: null,
  avatar_image: {
    id: 0,
    link: '',
    deleted_at: null
  },
  media: {
    id: 0,
    link: '',
    deleted_at: ''
  }
})
export const useUserStore = defineStore('user', {
  state: initState,

  getters: {
    userAvatar: (state) => state.avatar_image,

    getUserRole: (state) => state.role_id,
  },
  actions: {
    async fetch() {
      const response = await axios.get('/profile')

      this.$state = response.data
    },

    async updateProfile(data: {}) {
      await axios.put('/profile', data)

      await this.fetch()
    },

    clearProfile() {
      this.$state = initState()
    }
  }
})