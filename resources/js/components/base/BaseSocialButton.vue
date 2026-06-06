<template>
  <BaseButton variant="secondary" class="w-full" v-bind="$attrs">
    <GoogleIcon v-if="provider === 'google'" />
    <XLogoIcon v-else-if="provider === 'x'" />
    <FacebookIcon v-else-if="provider === 'facebook'" />
    <LinkedInIcon v-else-if="provider === 'linkedin'" />
    {{ label }}
  </BaseButton>
</template>

<script setup lang="ts">
import BaseButton from './BaseButton.vue'
import GoogleIcon from '@/icons/GoogleIcon.vue'
import XLogoIcon from '@/icons/XLogoIcon.vue'
import FacebookIcon from '@/icons/FacebookIcon.vue'
import LinkedInIcon from '@/icons/LinkedInIcon.vue'

defineProps<{
  provider: 'google' | 'x' | 'facebook' | 'linkedin'
  label: string
}>()
</script>
