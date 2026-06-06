<template>
    <AdminLayout>
        <PageBreadcrumb :pageTitle="currentPageTitle" />
        
        <div class="grid grid-cols-1 gap-5 sm:gap-6 lg:grid-cols-2">
            <!-- Basic Modal Example -->
            <ComponentCard title="Modal Básico">
                <p class="text-sm text-slate-500 mb-4">
                    Este es un ejemplo de un modal funcional. Puedes abrirlo haciendo clic en el botón y cerrarlo haciendo
                    clic fuera del modal o en el botón de cerrar.
                </p>
                <BaseButton size="sm" variant="primary" @click="openBasicModal = true"> Abrir modal </BaseButton>
                <BaseModal v-model:isOpen="openBasicModal" title="Modal Funcional" size="md">
                    <template #body>
                        <div class="space-y-4">
                            <p class="text-sm text-slate-600 dark:text-slate-400">Este modal se cierra al pulsar el botón o al hacer clic fuera.</p>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Puedes personalizar el contenido con cualquier componente Vue.</p>
                        </div>
                    </template>
                    <template #actions>
                        <button
                            type="button"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50"
                            @click="openBasicModal = false"
                        >
                            Cerrar
                        </button>
                    </template>
                </BaseModal>
            </ComponentCard>

            <!-- Form Modal Example -->
            <ComponentCard title="Modal con Formulario">
                <p class="text-sm text-slate-500 mb-4">
                    Ejemplo de modal con formulario CRUD. El modal se cierra después de enviar el formulario.
                </p>
                <BaseButton size="sm" variant="primary" @click="openFormModal = true"> Crear Usuario </BaseButton>
                <FormModal
                    v-model:isOpen="openFormModal"
                    title="Crear Usuario"
                    :initialData="{ name: '', email: '' }"
                    @submit="handleFormSubmit"
                />
            </ComponentCard>
        </div>

    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import ComponentCard from '@/components/shared/ComponentCard.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseModal from '@/components/base/BaseModal.vue'
import FormModal from '@/components/shared/FormModal.vue'


const currentPageTitle = ref('Modal')
const openBasicModal = ref(false)
const openFormModal = ref(false)

const handleFormSubmit = async (data) => {
  // Aquí irá tu lógica para enviar a Laravel (axios/fetch)
  try {
    console.log('Enviando:', data)
    // Simular envío
    await new Promise((r) => setTimeout(r, 800))
    openFormModal.value = false
    // Mostrar toast de éxito (opcional)
  } catch (e) {
    console.error('Error:', e)
  }
}
</script>

<style></style>
