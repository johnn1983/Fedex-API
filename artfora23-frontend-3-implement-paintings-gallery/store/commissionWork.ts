import { defineStore } from 'pinia'
import { CommissionWork } from '~/types/commissionWork'
import axios from 'axios'

export const useCommissionWorkState = defineStore('commission-work', {
  actions: {
    async send(userId: number, data: CommissionWork) {
      return await axios.post(`/users/${userId}/commission`, data)
    }
  }
})