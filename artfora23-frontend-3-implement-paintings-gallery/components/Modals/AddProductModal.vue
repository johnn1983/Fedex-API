<template>
  <ui-kit-modal
  title="Add Product"
  :with-footer="false"
  ref="addProductModal"
  >
  <template v-slot:content>
    <form @submit.prevent="uploadProduct()">
      <div class="add-product">
        <div class="add-product-upload">
          <label for="uploadImage" class="add-product-upload-add">
            DROP IMAGE(S) HERE <br>
            OR CLICK TO BROWSE <br>
            SHOULD BE AT LEAST 1920 X 586 PIXELS <br>
            <input
              id="uploadImage"
              @change="addFiles($event)"
              accept="image/bmp, image/png, image/jpeg"
              type="file"
              ref="file"
              multiple
            >
          </label>

          <div class="add-product-upload-images">
            <vue-draggable-next :list="files" @change="sortFiles()" class="add-product-upload-images-draggable">
              <div
                v-if="files.length !== 0"
                v-for="(image, index) in files"
                :key="image.id"
                class="add-product-upload-images-item"
              >
                <minus-icon class="minus-icon" @click="removeFile(index)" />
                <img :src="getImageUrl(image, ImageTemplate.SmallThumbnail)" alt="upload-image">
              </div>
            </vue-draggable-next>
          </div>

          <span
            v-show="fileError"
            class="form-error error"
            v-html="fileError"
          ></span>
        </div>

        <div class="add-product-categories">
          <ui-kit-selector
            v-model="selectedCategory"
            @changed="removeChoiceSub()"
            :options="categoriesSelectorItems"
            :title="'CATEGORY'"
            :disabled="store.pendingRequestsCount"
          />

          <div v-show="selectedCategory" class="add-product-categories-sub">
            <ui-kit-check-box
              v-for="sub in currentSubCategories"
              v-model="selectedSubCategories"
              :name="'subCategory' + sub.id"
              :value="sub.id"
              :title="sub.title"
              type="radio"
            />
          </div>

            <span
              v-for="(message, key) in { required: 'Category is required.'}"
              v-show="v$.product.category_id.$error"
              v-html="message"
              :key="key"
              class="form-error error"
            ></span>
        </div>

        <ui-kit-input
          v-model="product.title"
          :errors="v$.product.title"
          :error-messages="{ required: 'Title is required'}"
          :server-errors="serverErrors"
          :disabled="store.pendingRequestsCount"
          placeholder="TITLE"
        />

        <ui-kit-input
          v-model="product.author"
          :errors="v$.product.author"
          :error-messages="{ required: 'Author is required'}"
          :server-errors="serverErrors"
          :disabled="store.pendingRequestsCount"
          placeholder="CREDIT ARTIST/OWNER"
        />

        <ui-kit-text-area
          v-model="product.description"
          :errors="v$.product.description"
          :error-messages="{ required: 'Description is required'}"
          :server-errors="serverErrors"
          :disabled="store.pendingRequestsCount"
          placeholder="DESCRIPTION"
        />

        <ui-kit-check-box v-model="product.is_ai_safe" class="add-product-ai-safe-checkboxes">
          AI safe (the best we can do)
          <span
            @click="emit('openAiSafeDescription')"
            class="link"
          >read more</span>
        </ui-kit-check-box>

        <ui-kit-input
          v-model="product.tags"
          :errors="v$.product.tags"
          :error-messages="{ required: 'Tags are required' }"
          :disabled="product.is_ai_safe"
          placeholder="ADD TAGS, SEPARATE BY COMMA"
        />

        <div class="add-product-visibility-level">
          <ui-kit-check-box
            v-model="product.visibility_level"
            :value="COMMON_VISIBILITY_LEVEL"
            :disabled="store.pendingRequestsCount"
            title="For all users, does not contain explicit material"
            type="radio"
          />

          <ui-kit-check-box
            v-model="product.visibility_level"
            :value="NUDITY_VISIBILITY_LEVEL"
            :disabled="store.pendingRequestsCount"
            title="Can contain nudity but only for educational use"
            type="radio"
          />

          <ui-kit-check-box
            v-model="product.visibility_level"
            :value="EROTIC_VISIBILITY_LEVEL"
            :disabled="store.pendingRequestsCount"
            title="Can contain nudity and erotic material"
            type="radio"
          />

          <ui-kit-check-box
            v-model="product.visibility_level"
            :value="PORNO_VISIBILITY_LEVEL"
            :disabled="store.pendingRequestsCount"
            title="Can contain pornographic or other explicit material"
            type="radio"
          />
          <span
            v-for="(message, key) in { required: 'Visibility is required. '}"
            v-show="v$.product.visibility_level.$error"
            v-html="message"
            :key="key"
            class="form-error error"
          ></span>
        </div>

        <div class="ui-kit-modal-content-buttons">
          <button
            :disabled="store.pendingRequestsCount"
            class="button full-width"
            type="submit"
          >SEND FOR APPROVAL</button>
        </div>
      </div>
    </form>
  </template>
  </ui-kit-modal>
</template>

<script setup lang="ts">
import { computed, ref, reactive } from 'vue'
import { useCategoriesStore } from '~/store/categories'
import { storeToRefs } from 'pinia'
import { VueDraggableNext } from 'vue-draggable-next'
import { useStore } from '~/store'
import { useMediaStore } from '~/store/media'
import { useProductsStore } from '~/store/products'
import {
  COMMON_VISIBILITY_LEVEL,
  EROTIC_VISIBILITY_LEVEL,
  ImageTemplate,
  NUDITY_VISIBILITY_LEVEL,
  PORNO_VISIBILITY_LEVEL
} from '~/types/constants'
import UiKitModal from '~/components/UiKit/UiKitModal.vue'
import MinusIcon from '~/assets/svg/minus.svg'
import useMedia from '~/composable/media'
import useVuelidate from '@vuelidate/core'
import { required } from '@vuelidate/validators'

const addProductModal = ref<InstanceType<typeof UiKitModal>>(null)
const file = ref<InstanceType<typeof HTMLInputElement>>(null)
const files = ref([])
const store = useStore()
const categoriesStore = useCategoriesStore()
const mediaStore = useMediaStore()
const productStore = useProductsStore()
const { getImageUrl } = useMedia()
const { items } = storeToRefs(categoriesStore)
const selectedCategory = ref(null)

const emit = defineEmits(['openAiSafeDescription'])

const currentSubCategories = computed(() => {
  return selectedCategory.value ? items.value.find((item) => item.id === selectedCategory.value).children : []
})

const selectedSubCategories = ref(null)
const selectCategory = computed(() => selectedSubCategories.value ?? selectedCategory.value)
const fileError = ref('')
const error = ref('')
const serverErrors = ref({})

const categoriesSelectorItems = computed(() => items.value.map((category) => ({
  title: category.title,
  key: category.id,
  payload: category
})))

const product = reactive({
  price: 0,
  category_id: selectCategory,
  media: [],
  author: '',
  title: '',
  description: '',
  tags: '',
  visibility_level: null,
  is_ai_safe: false
})

const v$ = useVuelidate({
  product: {
    price: { required },
    category_id: { required },
    author: { required },
    title: { required },
    description: { required },
    tags: { required },
    visibility_level: { required },
    is_ai_safe: {}
  }
}, { product })

async function addFiles(event: any) {
  const media = event.target.files || event.dataTransfer.files

  if (!media.length) {
    return
  }

  for (const item of media) {

    try {
      const response = await mediaStore.upload(item, item.name)

      files.value.push(response.data)
      product.media.push(response.data.id)
    } catch (e) {
      fileError.value = e.response.data.errors.file
    }
  }
}

function removeFile(index: number) {
  files.value.splice(index, 1)
  product.media.splice(index, 1)
}

function sortFiles() {
  product.media = [...files.value]
}

function removeChoiceSub() {
  selectedSubCategories.value = null
}

function clearProductFields() {
  selectedCategory.value = null
  selectedSubCategories.value = null
  files.value = []

  product.price = 0
  product.media = []
  product.author = ''
  product.title = ''
  product.description = ''
  product.tags = ''
  product.visibility_level = null
  product.is_ai_safe = false
}

async function uploadProduct() {

  if (product.media.length < 1) {
    fileError.value = 'Media is required. '
    return
  }

  // this is a temporary solution to add tags if ai_safe = true, until the backend is ready, should be removed in the future
  if (product.is_ai_safe) {
    product.tags = 'aiSafe'
  }

  v$.value.$touch()

  if (v$.value.$error) {
    return
  }

  error.value = ''
  serverErrors.value = {}
  fileError.value = ''

  try {
    await productStore.create(product).then(close)

  } catch (e) {
    if (e.response && !e.response.data.errors) {
      error.value = 'Something went wrong! Please try again later.'

      return
    }

    serverErrors.value = e.response.data.errors
  }
}

function open() {
  clearProductFields()
  addProductModal.value?.open()
}

function close() {
  addProductModal.value?.close()
}

defineExpose({ open, close })
</script>
