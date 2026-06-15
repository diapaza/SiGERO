<template>
  <AuthLayout title="Sign In" description="Enter your email and password to sign in!">
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-5">
      <BaseSocialButton provider="google" label="Sign in with Google" />
      <BaseSocialButton provider="x" label="Sign in with X" />
    </div>

    <BaseDivider />

    <form @submit.prevent="handleSubmit">
      <div class="space-y-5">
        <BaseFormField label="Email" label-for="email" required>
          <BaseInput
            id="email"
            v-model="email"
            type="email"
            name="email"
            placeholder="info@gmail.com"
          />
        </BaseFormField>

        <BaseFormField label="Password" label-for="password" required>
          <BasePasswordInput id="password" v-model="password" placeholder="Enter your password" />
        </BaseFormField>

        <div class="flex items-center justify-between">
          <BaseCheckbox v-model="keepLoggedIn" label="Keep me logged in" />
          <Link
            href="/reset-password"
            class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400"
          >
            Forgot password?
          </Link>
        </div>

        <BaseButton type="submit" variant="primary" class="w-full"> Sign In </BaseButton>
      </div>
    </form>

    <div class="mt-5">
      <p class="text-sm font-normal text-center text-gray-700 dark:text-gray-400 sm:text-start">
        Don't have an account?
        <Link href="/signup" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">
          Sign Up
        </Link>
      </p>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AuthLayout from '@/components/layout/AuthLayout.vue'
import BaseSocialButton from '@/components/base/BaseSocialButton.vue'
import BaseDivider from '@/components/base/BaseDivider.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseCheckbox from '@/components/base/BaseCheckbox.vue'
import BaseFormField from '@/components/base/BaseFormField.vue'
import BasePasswordInput from '@/components/base/BasePasswordInput.vue'

const email = ref('')
const password = ref('')
const keepLoggedIn = ref(false)

const handleSubmit = () => {
  // TODO: Implement sign-in logic
}
</script>
