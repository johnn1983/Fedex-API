export declare interface SimpleTableHeaderItem {
  class: string
  name: string
}

export declare interface SimpleTableRowItem {
  to: string | null
  line: string[]
}

export declare interface UiKitBoxOption {
  title: string
  event: string
}

export declare interface UiKitCustomButton {
  class: string
  event: string
}

export declare interface UiKitMediaUploadPanelModelValue {}

export declare type InputValue = string | number | null

export declare interface OptionItem {
  title: string
  key: string | number
  payload?: any
}