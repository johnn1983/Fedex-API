import { defineStore } from 'pinia'
import { CategoriesState } from '~/types'
import axios, {AxiosResponse} from 'axios'
import { Paginated } from '~/types/search'
import { Category } from '~/types/categories'

export const useCategoriesStore = defineStore('categories', {
  state: (): CategoriesState => ({
    items: [],
    filters: {
      all: 1,
      with: ['children'],
      only_parents: 1
    }
  }),

  actions: {
    async fetch() {
      const response: AxiosResponse<Paginated<Category>> = await axios.get('/categories', { params: this.filters })

      this.items = response.data.data
    }
  },
})