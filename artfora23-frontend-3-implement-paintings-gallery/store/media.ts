// @ts-ignore
import { defineStore } from 'pinia'
import axios, { AxiosResponse } from 'axios'
import { Media } from '~/types';

export const useMediaStore = defineStore({
  id: 'media',

  actions: {
    upload(blob: Blob, fileName: string): Promise<AxiosResponse<Media>> {
      const request = {
        method: 'post',
        url: '/media',
        data: new FormData(),
        headers: {
          'content-type': 'multipart/form-data'
        }
      }

      request.data.append('file', blob, fileName)

      return axios.request(request)
    }
  }
})