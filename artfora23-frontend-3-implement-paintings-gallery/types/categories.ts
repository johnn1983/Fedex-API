import { Entity } from '~/types/index'


export declare interface SubCategory extends Entity {
    title: string
    parent_id: number | null
}

export declare interface Category extends SubCategory {
    id: number
    parent: null
    children: SubCategory[] | []
}
