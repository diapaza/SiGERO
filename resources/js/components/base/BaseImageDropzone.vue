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

/** * Zona de arrastre y subida de imágenes reutilizable. * * Permite seleccionar o arrastrar una
imagen, la sube al servidor mediante * `uploadUrl` y expone la ruta almacenada a través de
`v-model`. Incluye * vista previa con opción de eliminar y validación de tamaño y tipo. */
<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import { toast } from 'vue-sonner'
import UploadIcon from '@/icons/UploadIcon.vue'
import TrashIcon from '@/icons/TrashIcon.vue'

const props = withDefaults(
  defineProps<{
    /** Ruta de la imagen subida, relativa a storage (v-model). */
    modelValue?: string | null
    /** Endpoint al que se envía la imagen (multipart/form-data, campo `foto`). */
    uploadUrl: string
    /** Tamaño máximo permitido del archivo en megabytes. */
    maxFileSize?: number
    /** Tipos MIME aceptados por el input de archivo. */
    acceptedFiles?: string
  }>(),
  {
    modelValue: null,
    maxFileSize: 0.5,
    acceptedFiles: 'image/jpeg,image/png,image/gif,image/webp',
  },
)

// Emite:
const emits = defineEmits<{
  /** Actualiza la ruta de la imagen subida (v-model). */
  (e: 'update:modelValue', value: string | null): void
  /** Se emite tras una subida exitosa con la ruta de la imagen. */
  (e: 'uploaded', path: string): void
  /** Se emite cuando se elimina la imagen. */
  (e: 'removed'): void
}>()

const fileInputRef = ref<HTMLInputElement | null>(null)
const isDragOver = ref(false)
let dragCounter = 0

/** Abre el selector de archivos nativo del navegador. */
function openFilePicker() {
  fileInputRef.value?.click()
}

/** Marca la zona como activa al entrar el archivo arrastrado. */
function onDragEnter() {
  dragCounter++
  isDragOver.value = true
}

/** Mantiene activa la zona mientras el archivo pasa sobre ella. */
function onDragOver() {
  isDragOver.value = true
}

/** Desactiva la zona cuando el archivo sale de ella (con contador). */
function onDragLeave() {
  dragCounter--
  if (dragCounter <= 0) {
    dragCounter = 0
    isDragOver.value = false
  }
}

/** Maneja el soltar del archivo arrastrado y delega en `handleFile`. */
function onDrop(event: DragEvent) {
  dragCounter = 0
  isDragOver.value = false

  const files = event.dataTransfer?.files
  if (files && files.length > 0) {
    handleFile(files[0])
  }
}

/** Maneja la selección de archivo desde el input nativo. */
function onFileInputChange(event: Event) {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  if (file) {
    handleFile(file)
  }
  input.value = ''
}

/** Valida el archivo y lo sube al servidor, emitiendo la ruta resultante. */
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

/** Elimina la imagen actual y emite los eventos `removed` y `update:modelValue`. */
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
