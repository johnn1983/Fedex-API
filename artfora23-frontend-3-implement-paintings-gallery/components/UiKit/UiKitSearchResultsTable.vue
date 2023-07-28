<template>
  <div class="table">
    <div class="table-header row">
      <div v-for="headerField in fields" :class="headerField.class" class="col">
        {{ headerField.name }}
      </div>
    </div>

    <div
      v-for="(entity, entityIndex) in entities"
      :class="{ even: entityIndex % 2, odd: !(entityIndex % 2) }"
      :cy-name="`${name}-row`"
      @click="onClick(entity)"
      class="row table-row"
    >
      <div
        v-for="(value, valueIndex) in entity.line"
        :class="fields[valueIndex].class"
        class="col"
        :cy-name="`${fields[valueIndex].name}-cell`"
      >
        {{ value }}
      </div>
    </div>

    <div v-if="!entities.length" class="mock-container col">
      <div class="mock-label">{{ noEntitiesLabel }}</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { SimpleTableRowItem, SimpleTableHeaderItem } from '~/types/uiKit'
import { useRouter } from 'vue-router'
import { defineEmits } from 'vue'

interface Props {
  fields: SimpleTableHeaderItem[],
  entities: SimpleTableRowItem[],
  noEntitiesLabel: string,
  name?: string
}

const props = withDefaults(defineProps<Props>(), {
  fields: () => ([]),
  entities: () => ([]),
  noEntitiesLabel: 'Nothing found',
})

const router = useRouter()
const emit = defineEmits(['open-modal']);

function onClick (entity) {
  if (entity.to) {
    router.push(entity.to)
    return
  }

  if (entity.event) {
    emit(entity.event.name, entity.event.payload)
    return
  }
}
</script>