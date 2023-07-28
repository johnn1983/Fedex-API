export declare interface Paginated<T> {
  data: T[]
  total: number
  current_page: number

  from?: number
  to?: number

  last_page?: number
  per_page?: number

  path?: string
  next_page_url?: string
  prev_page_url?: string
}

export declare interface SearchFilters {
  page?: number
  per_page?: number
  order_by?: string | null,
  desc?: boolean | 0 | 1 | null
  with?: string[]
  with_count?: string[]
  all?: boolean | 0 | 1
}
