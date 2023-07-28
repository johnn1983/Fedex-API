import { defineNuxtPlugin } from '#imports'
import { createNuxtPersistedState } from 'pinia-plugin-persistedstate/nuxt'
import { useCookie } from '#app'

/**
 * The name of this plugin is started from underscore because it MUST be called before axios plugin.
 * Otherwise, store will not have the access to auth.token
 **/
export default defineNuxtPlugin(nuxtApp => {
  nuxtApp.$pinia.use(createNuxtPersistedState(useCookie))
})