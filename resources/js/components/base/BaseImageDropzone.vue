<template>
  <div>
    <div v-if="modelValue" class="mb-3 flex items-start gap-4">
      <div class="relative">
        <img
          :src="'/storage/' + modelValue"
          alt="Vista previa"
          class="w-32 h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700"
        />
        <button
          type="button"
          class="absolute -top-2 -right-2 bg-error-500 text-white rounded-full p-1 hover:bg-error-600 transition-colors"
          @click="removeImage"
        >
          <TrashIcon :size="14" />
        </button>
      </div>
    </div>

    <div v-show="!modelValue" class="file-uploader">
      <div
        :class="[
          'dropzone rounded-xl bg-gray-50 p-7 lg:p-10 cursor-pointer',
          isDragOver
            ? 'border-brand-500 border-solid dark:border-brand-400'
            : 'border-gray-300 border-dashed hover:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:hover:border-brand-500',
        ]"
        @dragover.prevent="onDragOver"
        @dragenter.prevent="onDragEnter"
        @dragleave.prevent="onDragLeave"
        @drop.prevent="onDrop"
        @click="openFilePicker"
      >
        <input
          ref="fileInputRef"
          type="file"
          :accept="acceptedFiles"
          class="hidden"
          @change="onFileInputChange"
        />

        <div class="dz-message m-0!">
          <div class="mb-[22px] flex justify-center">
            <div
              class="flex h-[68px] w-[68px] items-center justify-center rounded-full bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-400"
            >
              <UploadIcon class="fill-current" />
            </div>
          </div>

          <h4 class="mb-3 font-semibold text-gray-800 text-theme-xl dark:text-white/90">
            Arrastra y suelta la imagen aquí
          </h4>
          <span
            class="mx-auto mb-5 block w-full max-w-[290px] text-sm text-gray-700 dark:text-gray-400"
          >
            Arrastra y suelta tus imágenes PNG, JPG, GIF, WebP aquí o selecciona un archivo
          </span>

          <span class="font-medium underline cursor-pointer text-theme-sm text-brand-500">
            Seleccionar archivo
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import { toast } from 'vue-sonner'
import UploadIcon from '@/icons/UploadIcon.vue'
import TrashIcon from '@/icons/TrashIcon.vue'

const props = withDefaults(
  defineProps<{
    modelValue?: string | null
    uploadUrl: string
    maxFileSize?: number
    acceptedFiles?: string
  }>(),
  {
    modelValue: null,
    maxFileSize: 0.5,
    acceptedFiles: 'image/jpeg,image/png,image/gif,image/webp',
  },
)

const emits = defineEmits<{
  (e: 'update:modelValue', value: string | null): void
  (e: 'uploaded', path: string): void
  (e: 'removed'): void
}>()

const fileInputRef = ref<HTMLInputElement | null>(null)
const isDragOver = ref(false)
let dragCounter = 0

function openFilePicker() {
  fileInputRef.value?.click()
}

function onDragEnter() {
  dragCounter++
  isDragOver.value = true
}

function onDragOver() {
  isDragOver.value = true
}

function onDragLeave() {
  dragCounter--
  if (dragCounter <= 0) {
    dragCounter = 0
    isDragOver.value = false
  }
}

function onDrop(event: DragEvent) {
  dragCounter = 0
  isDragOver.value = false

  const files = event.dataTransfer?.files
  if (files && files.length > 0) {
    handleFile(files[0])
  }
}

function onFileInputChange(event: Event) {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  if (file) {
    handleFile(file)
  }
  input.value = ''
}

async function handleFile(file: File) {
  const maxSizeBytes = props.maxFileSize * 1024 * 1024
  if (file.size > maxSizeBytes) {
    toast.error(`El archivo excede el tamaño máximo de ${props.maxFileSize}MB.`)
    return
  }

  if (!file.type.startsWith('image/')) {
    toast.error('Solo se permiten archivos de imagen.')
    return
  }

  const formData = new FormData()
  formData.append('foto', file)

  try {
    const response = await axios.post(props.uploadUrl, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    emits('update:modelValue', response.data.path)
    emits('uploaded', response.data.path)
  } catch {
    toast.error('Error al subir la imagen. Intente de nuevo.')
  }
}

function removeImage() {
  emits('update:modelValue', null)
  emits('removed')
}
</script>

<style>
.dropzone {
  border: 1px dashed #d0d5dd;
  transition: all 0.3s ease;
}

.dropzone:hover {
  border-color: #465fff;
}

.dropzone .dz-preview {
  margin: 10px;
}

.dropzone .dz-preview .dz-image {
  border-radius: 8px;
}

.dropzone .dz-preview .dz-details {
  padding: 1em;
}

.dropzone .dz-preview .dz-progress {
  height: 10px;
}

.dropzone .dz-preview .dz-progress .dz-upload {
  background: #4f46e5;
}

.dark .dropzone {
  background-color: #111827;
  border-color: #374151;
}

.dark .dropzone:hover {
  border-color: #6366f1;
}
</style>
