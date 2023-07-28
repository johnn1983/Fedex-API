import { defineStore } from 'pinia'
import {ProductStatus, ProductsState, Product} from '~/types/products'
import { STATUS_APPROVED, STATUS_PENDING } from '~/types/constants'
import axios from 'axios'
import {Paginated} from "~/types/search";

export const useProductsStore = defineStore('products', {
  state: (): ProductsState => ({
    items: {
      total: 0,
      current_page: 1,
      last_page: 1,
      data: []
    },
    item: {
      id: 1,
      title: '',
      author: '',
      price: 0,
      user_id: 0,
      category_id: 0,
      slug: '',
      tags: '',
      description: '',
      is_ai_safe: false,
      width: 0,
      height: 0,
      weight: 0,
      status: ProductStatus.Approved,
      deleted_at: null,
      media: [],
      user: {
        id: 0,
        username: '',
        tagname: '',
        email: '',
        phone: '',
        description: '',
        country: '',
        role_id: 0,
        external_link: null,
        background_image: {
          id: 1,
          deleted_at: null,
          created_at: '',
          link: '',
          name: ''
        },
        data: {},
        "2fa_type": 'email',
        media: []
      }
    },
    filters: {
      per_page: 20,
      page: 1,
      with: ['user', 'media'],
      desc: 1
    },
    loadingNextPage: false,
    pendingCount: 0
  }),

  actions: {
     async fetchAll() {
       this.$state.filters.page = 1

      const response = await axios.get('/products', { params: this.$state.filters })

      this.$state.items = response.data
    },

    async fetchNextPage() {
      this.$state.loadingNextPage = true

      const response = await axios.get('/products', { params: this.filters })

      const prevProducts = [...this.$state.items.data]
      this.$state.items = response.data
      this.$state.items.data = [...prevProducts, ...this.$state.items.data]

      this.$state.loadingNextPage = false
    },

    updateFilter(filter: any) {
       this.filters = Object.assign({}, this.filters, filter)
    },

    async fetch(id: string) {
      const response = await axios.get(`/products/${id}`, { params: this.$state.filters })

      this.$state.item = response.data
    },

    create(item: any) {
      return axios.post('/products', item)
    },

    update(id: number, filters: {}) {
       return axios.put(`/products/${id}`, { ...filters })
    },

    async getPendingCount() {
      const response = await axios.get('/products', {
        params: Object.assign({}, { status: STATUS_PENDING })
      })

      this.$state.pendingCount = response.data.total
    }
  }
})



