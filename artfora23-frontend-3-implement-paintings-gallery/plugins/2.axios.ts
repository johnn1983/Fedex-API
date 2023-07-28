import { defineNuxtPlugin, useRuntimeConfig } from '#app'
import { AuthState } from '~/types'
import { navigateTo } from '#imports'
import axios, {AxiosRequestConfig} from 'axios'
import { useAuthStore } from '~/store/auth'
import { useStore } from '~/store'
// @ts-ignore
import { isJwtExpired } from 'jwt-check-expiration'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()

  axios.defaults.baseURL = config.public.API_BASE_URL

  const authStore = useAuthStore()
  const rootStore = useStore()

  addLoaderInterceptors(rootStore)

  refreshTokenInterceptor(authStore)

  redirectToLoginPage(authStore)
})

const addLoaderInterceptors = (store: any) => {
  axios.interceptors.request.use((config) => {
    store.pendingRequestsCount++

    return config
  }, (error) => Promise.reject(error))

  axios.interceptors.response.use((response) => {
    store.pendingRequestsCount--

    return response
  }, (error) => {
    store.pendingRequestsCount--

    return Promise.reject(error)
  })
}

let refreshTokenPromise: Promise<any> | null = null;

const refreshTokenInterceptor = (store: any) => {
  axios.interceptors.request.use(async (config) => {
      const token = await getToken(config, store)

      config.headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
      }

      return config
    },
    error => {
      Promise.reject(error)
    })
}

const redirectToLoginPage = (store: AuthState) => {
  axios.interceptors.response.use(
    (response) => response,
    (error) => {
      if (!error.response) {
        return Promise.reject(error)
      }

      if ([401, 403].includes(error.response.status)) {
        store.token = null

        navigateTo('/')
      }

      return Promise.reject(error)
    })
}

const getToken = async (config: AxiosRequestConfig, store: any): Promise<string | null> => {
  let token = store.token

  if (!token || !config) {
    return null
  }

  if (!isJwtExpired(token) || isRefreshTokenRequest(config)) {
    return token
  }

  return null
}

const isRefreshTokenRequest = (config: AxiosRequestConfig) => {
  if (!config) {
    return false
  }

  return config.url?.includes('refresh')
}