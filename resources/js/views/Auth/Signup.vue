<template>
  <AuthLayout title="Sign Up" description="Enter your email and password to sign up!">
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-5">
      <BaseSocialButton provider="google" label="Sign up with Google" />
      <BaseSocialButton provider="x" label="Sign up with X" />
    </div>

    <BaseDivider />

    <form @submit.prevent="handleSubmit">
      <div class="space-y-5">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div class="sm:col-span-1">
            <BaseLabel label-for="fname" label="First Name" required />
            <BaseInput
              id="fname"
              v-model="firstName"
              type="text"
              name="fname"
              placeholder="Enter your first name"
            />
          </div>
          <div class="sm:col-span-1">
            <BaseLabel label-for="lname" label="Last Name" required />
            <BaseInput
              id="lname"
              v-model="lastName"
              type="text"
              name="lname"
              placeholder="Enter your last name"
            />
          </div>
        </div>

        <div>
          <BaseLabel label-for="email" label="Email" required />
          <BaseInput
            id="email"
            v-model="email"
            type="email"
            name="email"
            placeholder="Enter your email"
          />
        </div>

        <div>
          <BaseLabel label-for="password" label="Password" required />
          <BaseInput
            id="password"
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Enter your password"
          >
            <template #append>
              <span class="cursor-pointer" @click="togglePasswordVisibility">
                <EyeOffIcon v-if="!showPassword" class="fill-gray-500 dark:fill-gray-400" />
                <EyeIcon v-else class="fill-gray-500 dark:fill-gray-400" />
              </span>
            </template>
          </BaseInput>
        </div>

        <div>
          <BaseCheckbox v-model="agreeToTerms">
            <p class="inline-block font-normal text-gray-500 dark:text-gray-400">
              By creating an account means you agree to the
              <span class="text-gray-800 dark:text-white/90">Terms and Conditions,</span>
              and our
              <span class="text-gray-800 dark:text-white">Privacy Policy</span>
            </p>
          </BaseCheckbox>
        </div>

        <BaseButton type="submit" variant="primary" class="w-full"> Sign Up </BaseButton>
      </div>
    </form>

    <div class="mt-5">
      <p class="text-sm font-normal text-center text-gray-700 dark:text-gray-400 sm:text-start">
        Already have an account?
        <router-link to="/signin" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">
          Sign In
        </router-link>
      </p>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AuthLayout from '@/components/layout/AuthLayout.vue'
import BaseSocialButton from '@/components/base/BaseSocialButton.vue'
import BaseDivider from '@/components/base/BaseDivider.vue'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseCheckbox from '@/components/base/BaseCheckbox.vue'
import BaseLabel from '@/components/base/BaseLabel.vue'
import EyeIcon from '@/icons/EyeIcon.vue'
import EyeOffIcon from '@/icons/EyeOffIcon.vue'

const firstName = ref('')
const lastName = ref('')
const email = ref('')
const password = ref('')
const showPassword = ref(false)
const agreeToTerms = ref(false)

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

const handleSubmit = () => {
  // TODO: Implement sign-up logic
}
</script>
