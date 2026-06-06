<template>
  <BaseModal v-model:isOpen="isOpen" :title="title" :size="size" @close="handleClose">
    <template #body>
      <form @submit.prevent="onSubmit" class="space-y-4">
        <slot name="form-fields">
          <div class="grid grid-cols-1 gap-4">
            <div>
              <BaseLabel for="name" label="Nombre" />
              <BaseInput
                v-model="form.name"
                id="name"
                type="text"
                className="w-full"
                :disabled="loading"
              />
            </div>

            <div>
              <BaseLabel for="email" label="Email" />
              <BaseInput
                v-model="form.email"
                id="email"
                type="email"
                className="w-full"
                :disabled="loading"
              />
            </div>
          </div>
        </slot>
      </form>
    </template>

    <template #actions>
      <BaseButton type="button" variant="outline" @click="handleClose" :disabled="loading">
        Cancelar
      </BaseButton>
      <BaseButton type="button" variant="primary" @click="onSubmit" :disabled="loading">
        <span v-if="!loading">Guardar</span>
        <span v-else>Guardando...</span>
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
    // @ts-ignore
    form[k] = props.initialData?.[k] ?? ''
  }
  emits('update:isOpen', false)
  emits('close')
}
</script>
