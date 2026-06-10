<template>
  <AdminLayout>
    <PageBreadcrumb :page-title="currentPageTitle" />
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div class="space-y-6">
        <ComponentCard title="Default Inputs">
          <DefaultInputs />
        </ComponentCard>
        <ComponentCard title="Select Inputs">
          <SelectInput />
        </ComponentCard>
        <ComponentCard title="Select Search">
          <SelectSearch />
        </ComponentCard>
        <ComponentCard title="Inputs States">
          <TextArea />
        </ComponentCard>
        <ComponentCard title="Inputs States">
          <InputState />
        </ComponentCard>
      </div>
      <div class="space-y-6">
        <ComponentCard title="Inputs Group">
          <InputGroup />
        </ComponentCard>
        <ComponentCard title="File Input">
          <FileInput />
        </ComponentCard>
        <ComponentCard title="Checkboxes">
          <CheckboxInput />
        </ComponentCard>
        <ComponentCard title="Dropzone">
          <Dropzone />
        </ComponentCard>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import DefaultInputs from '@/components/demos/forms/DefaultInputs.vue'
import ComponentCard from '@/components/shared/ComponentCard.vue'
import SelectInput from '@/components/demos/forms/SelectInput.vue'
import SelectSearch from '@/components/demos/forms/SelectSearch.vue'
import InputState from '@/components/demos/forms/InputState.vue'
import TextArea from '@/components/demos/forms/TextArea.vue'
import InputGroup from '@/components/demos/forms/InputGroup.vue'
import Dropzone from '@/components/demos/forms/Dropzone.vue'
import FileInput from '@/components/demos/forms/FileInput.vue'
import CheckboxInput from '@/components/demos/forms/CheckboxInput.vue'

const currentPageTitle = ref('Form Elements')
</script>
