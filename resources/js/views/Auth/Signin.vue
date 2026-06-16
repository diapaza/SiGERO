<template>
  <AuthLayout title="Iniciar Sesión" description="Ingresa tu usuario y contraseña para continuar.">
    <form @submit.prevent="handleSubmit">
      <div class="space-y-5">
        <BaseFormField label="Usuario" label-for="username" :error="form.errors.username" required>
          <BaseInput
            id="username"
            v-model="form.username"
            type="text"
            name="username"
            placeholder="Ingresa tu usuario"
          >
            <template #prepend>
              <UserIcon :size="20" class="fill-gray-500 dark:fill-gray-400" />
            </template>
          </BaseInput>
        </BaseFormField>

        <BaseFormField
          label="Contraseña"
          label-for="password"
          :error="form.errors.password"
          required
        >
          <BasePasswordInput
            id="password"
            v-model="form.password"
            placeholder="Ingresa tu contraseña"
          >
            <template #prepend>
              <LockIcon :size="18" class="fill-gray-500 dark:fill-gray-400" />
            </template>
          </BasePasswordInput>
        </BaseFormField>

        <BaseButton type="submit" variant="primary" class="w-full" :disabled="form.processing">
          {{ form.processing ? 'Ingresando...' : 'Iniciar Sesión' }}
        </BaseButton>
      </div>
    </form>

    <div class="mt-5">
      <p class="text-sm font-normal text-center text-gray-700 dark:text-gray-400 sm:text-start">
        ¿No tienes cuenta?
        <Link href="/signup" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">
          Registrarse
        </Link>
      </p>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AuthLayout from '@/components/layout/AuthLayout.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseFormField from '@/components/base/BaseFormField.vue'
import BasePasswordInput from '@/components/base/BasePasswordInput.vue'
import UserIcon from '@/icons/UserIcon.vue'
import LockIcon from '@/icons/LockIcon.vue'

const form = useForm({
  username: '',
  password: '',
})

const handleSubmit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>
