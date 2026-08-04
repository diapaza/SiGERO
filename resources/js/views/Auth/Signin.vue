<template>
  <AuthLayout title="Iniciar Sesión" description="Ingresa tu usuario y contraseña para continuar.">
    <Alert
      v-if="showCredentialsError"
      variant="error"
      title="Credenciales incorrectas"
      message="Por favor, verifica tu usuario y contraseña e intenta de nuevo."
      class="mb-5"
    />

    <form @submit.prevent="handleSubmit">
      <div class="space-y-5">
        <BaseFormField
          label="Usuario"
          label-for="username"
          :error="showCredentialsError ? '' : form.errors.username"
          required
        >
          <BaseInput
            id="username"
            v-model="form.username"
            type="text"
            name="username"
            placeholder="Ingresa tu usuario"
            :state="form.errors.username && !showCredentialsError ? 'error' : 'default'"
            @input="handleUsernameInput"
            @blur="validateSingleField('username')"
          >
            <template #prepend>
              <UserIcon :size="20" class="fill-dark-500 dark:fill-gray-400" />
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
            :state="form.errors.password ? 'error' : 'default'"
            @input="validateSingleField('password')"
            @blur="validateSingleField('password')"
          >
            <template #prepend>
              <LockIcon :size="22" class="text-dark-500 dark:text-gray-400" />
            </template>
          </BasePasswordInput>
        </BaseFormField>

        <BaseButton type="submit" variant="primary" class="w-full" :disabled="form.processing">
          {{ form.processing ? 'Ingresando...' : 'Iniciar Sesión' }}
        </BaseButton>
      </div>
    </form>
  </AuthLayout>
</template>

/** * Página de inicio de sesión. * * Vista renderizada por `AuthController@create`. Valida
`username` y * `password` en el frontend y envía la petición a `AuthController@login`. * Muestra un
aviso de credenciales incorrectas cuando el servidor rechaza el * intento. */
<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthLayout from '@/components/layout/AuthLayout.vue'
import Alert from '@/components/shared/Alert.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseFormField from '@/components/base/BaseFormField.vue'
import BasePasswordInput from '@/components/base/BasePasswordInput.vue'
import UserIcon from '@/icons/UserIcon.vue'
import LockIcon from '@/icons/LockIcon.vue'
import { useValidation } from '@/composables/useValidation'

const form = useForm({
  username: '',
  password: '',
})

const { validate, validateSingleField } = useValidation(form, 'login', {
  username: 'usuario',
  password: 'contraseña',
})

const showCredentialsError = ref(false)

const handleUsernameInput = () => {
  showCredentialsError.value = false
  validateSingleField('username')
}

const handleSubmit = () => {
  showCredentialsError.value = false
  if (!validate()) return

  form.post(route('login'), {
    onFinish: () => form.reset('password'),
    onError: () => {
      showCredentialsError.value = true
    },
  })
}
</script>
