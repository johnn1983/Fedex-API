import type { RouterConfig } from '@nuxt/schema'
import qs from 'qs'

// https://router.vuejs.org/api/interfaces/routeroptions.html
export default <RouterConfig> {
  parseQuery: (query: any): object => qs.parse(query),
  stringifyQuery: (query: any): string => qs.stringify(query)
}