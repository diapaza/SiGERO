<template>
  <BaseModal
    :is-open="isOpen"
    title="Edit Personal Information"
    size="lg"
    content-class="rounded-3xl"
    @update:is-open="emit('update:isOpen', $event)"
    @close="handleClose"
  >
    <template #body>
      <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
        Update your details to keep your profile up-to-date.
      </p>
      <div class="custom-scrollbar h-[458px] overflow-y-auto">
        <div>
          <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
            Social Links
          </h5>
          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
            <div>
              <BaseLabel label="Facebook" />
              <BaseInput v-model="formData.facebook" type="text" />
            </div>
            <div>
              <BaseLabel label="X.com" />
              <BaseInput v-model="formData.twitter" type="text" />
            </div>
            <div>
              <BaseLabel label="Linkedin" />
              <BaseInput v-model="formData.linkedin" type="text" />
            </div>
            <div>
              <BaseLabel label="Instagram" />
              <BaseInput v-model="formData.instagram" type="text" />
            </div>
          </div>
        </div>
        <div class="mt-7">
          <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
            Personal Information
          </h5>
          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
            <div class="col-span-2 lg:col-span-1">
              <BaseLabel label="First Name" />
              <BaseInput v-model="formData.firstName" type="text" />
            </div>
            <div class="col-span-2 lg:col-span-1">
              <BaseLabel label="Last Name" />
              <BaseInput v-model="formData.lastName" type="text" />
            </div>
            <div class="col-span-2 lg:col-span-1">
              <BaseLabel label="Email Address" />
              <BaseInput v-model="formData.email" type="text" />
            </div>
            <div class="col-span-2 lg:col-span-1">
              <BaseLabel label="Phone" />
              <BaseInput v-model="formData.phone" type="text" />
            </div>
            <div class="col-span-2">
              <BaseLabel label="Bio" />
              <BaseInput v-model="formData.bio" type="text" />
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #actions>
      <BaseButton variant="outline" @click="emit('update:isOpen', false)"> Close </BaseButton>
      <BaseButton variant="primary" @click="handleSave"> Save Changes </BaseButton>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue'
import BaseModal from '@/components/base/BaseModal.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseLabel from '@/components/base/BaseLabel.vue'

interface ProfileFormData {
  firstName: string
  lastName: string
  email: string
  phone: string
  bio: string
  facebook: string
  twitter: string
  linkedin: string
  instagram: string
}

const props = defineProps<{
  isOpen: boolean
  initialData?: Partial<ProfileFormData>
}>()

const emit = defineEmits<{
  (e: 'update:isOpen', value: boolean): void
  (e: 'save', data: ProfileFormData): void
}>()

const defaultData: ProfileFormData = {
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  bio: '',
  facebook: '',
  twitter: '',
  linkedin: '',
  instagram: '',
}

const formData = reactive<ProfileFormData>({ ...defaultData })

watch(
  () => props.initialData,
  (newData) => {
    if (newData) {
      Object.assign(formData, { ...defaultData, ...newData })
    }
  },
  { immediate: true },
)

const handleSave = () => {
  emit('save', { ...formData })
  emit('update:isOpen', false)
}
</script>
