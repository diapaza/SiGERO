<template>
  <AuthLayout title="Crear Cuenta" description="Ingresa tus datos para registrarte.">
    <form @submit.prevent="handleSubmit">
      <div class="space-y-5">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div class="sm:col-span-1">
            <BaseLabel label-for="fname" label="Nombre" required />
            <BaseInput
              id="fname"
              v-model="firstName"
              type="text"
              name="fname"
              placeholder="Ingresa tu nombre"
            />
          </div>
          <div class="sm:col-span-1">
            <BaseLabel label-for="lname" label="Apellido" required />
            <BaseInput
              id="lname"
              v-model="lastName"
              type="text"
              name="lname"
              placeholder="Ingresa tu apellido"
            />
          </div>
        </div>

        <div>
          <BaseLabel label-for="email" label="Correo electrónico" required />
          <BaseInput
            id="email"
            v-model="email"
            type="email"
            name="email"
            placeholder="Ingresa tu correo electrónico"
          />
        </div>

        <div>
          <BaseLabel label-for="password" label="Contraseña" required />
          <BasePasswordInput id="password" v-model="password" placeholder="Ingresa tu contraseña" />
        </div>

        <BaseButton type="submit" variant="primary" class="w-full"> Registrarse </BaseButton>
      </div>
    </form>

    <div class="mt-5">
      <p class="text-sm font-normal text-center text-gray-700 dark:text-gray-400 sm:text-start">
        ¿Ya tienes una cuenta?
        <Link href="/signin" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">
          Iniciar sesión.
        </Link>
      </p>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AuthLayout from '@/components/layout/AuthLayout.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseLabel from '@/components/base/BaseLabel.vue'
import BasePasswordInput from '@/components/base/BasePasswordInput.vue'

const firstName = ref('')
const lastName = ref('')
const email = ref('')
const password = ref('')

const handleSubmit = () => {
  // TODO: Implement sign-up logic
}
</script>
