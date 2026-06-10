<template>
  <BaseModal v-model:is-open="isOpen" :title="title" :size="size" @close="handleClose">
    <template #body>
      <form class="space-y-4" @submit.prevent="onSubmit">
        <slot name="form-fields">
          <div class="grid grid-cols-1 gap-4">
            <div>
              <BaseLabel label-for="name" label="Name" />
              <BaseInput
                id="name"
                v-model="form.name"
                type="text"
                class-name="w-full"
                :disabled="loading"
              />
            </div>

            <div>
              <BaseLabel label-for="email" label="Email" />
              <BaseInput
                id="email"
                v-model="form.email"
                type="email"
                class-name="w-full"
                :disabled="loading"
              />
            </div>
          </div>
        </slot>
      </form>
    </template>

    <template #actions>
      <BaseButton type="button" variant="outline" :disabled="loading" @click="handleClose">
        Cancel
      </BaseButton>
      <BaseButton type="button" variant="primary" :disabled="loading" @click="onSubmit">
        <span v-if="!loading">Save</span>
        <span v-else>Saving...</span>
      </BaseButton>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { reactive, computed, watch } from 'vue'
import BaseModal from '@/components/base/BaseModal.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseLabel from '@/components/base/BaseLabel.vue'

const props = defineProps<{
  isOpen: boolean
  title?: string
  size?: 'sm' | 'md' | 'lg' | 'xl' | 'fullscreen'
  loading?: boolean
  initialData?: Record<string, any>
}>()

const emits = defineEmits<{
  (e: 'update:isOpen', val: boolean): void
  (e: 'submit', payload: any): void
  (e: 'close'): void
}>()

const isOpen = computed({
  get: () => props.isOpen,
  set: (val) => emits('update:isOpen', val),
})

const form = reactive({ ...props.initialData })

watch(
  () => props.initialData,
  (v) => {
    Object.assign(form, v || {})
  },
)

const onSubmit = async () => {
  emits('submit', { ...form })
}

const handleClose = () => {
  for (const k in form) {
    ;(form as Record<string, unknown>)[k] = props.initialData?.[k] ?? ''
  }
  emits('update:isOpen', false)
  emits('close')
}
</script>
