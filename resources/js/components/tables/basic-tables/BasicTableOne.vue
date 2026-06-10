<template>
  <BaseTable>
    <thead>
      <tr class="border-b border-gray-200 dark:border-gray-700">
        <th class="px-5 py-3 text-left w-3/11 sm:px-6">
          <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">User</p>
        </th>
        <th class="px-5 py-3 text-left w-2/11 sm:px-6">
          <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Project Name</p>
        </th>
        <th class="px-5 py-3 text-left w-2/11 sm:px-6">
          <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Team</p>
        </th>
        <th class="px-5 py-3 text-left w-2/11 sm:px-6">
          <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p>
        </th>
        <th class="px-5 py-3 text-left w-2/11 sm:px-6">
          <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Budget</p>
        </th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
      <tr
        v-for="(user, index) in users"
        :key="index"
        class="border-t border-gray-100 dark:border-gray-800"
      >
        <td class="px-5 py-4 sm:px-6">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 overflow-hidden rounded-full">
              <img :src="user.avatar" :alt="user.name" />
            </div>
            <div>
              <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                {{ user.name }}
              </span>
              <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
                {{ user.role }}
              </span>
            </div>
          </div>
        </td>
        <td class="px-5 py-4 sm:px-6">
          <p class="text-gray-500 text-theme-sm dark:text-gray-400">
            {{ user.project }}
          </p>
        </td>
        <td class="px-5 py-4 sm:px-6">
          <div class="flex -space-x-2">
            <div
              v-for="(member, memberIndex) in user.team"
              :key="memberIndex"
              class="w-6 h-6 overflow-hidden border-2 border-white rounded-full dark:border-gray-900"
            >
              <img :src="member" alt="team member" />
            </div>
          </div>
        </td>
        <td class="px-5 py-4 sm:px-6">
          <span
            :class="[
              'rounded-full px-2 py-0.5 text-theme-xs font-medium',
              {
                'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500':
                  user.status === 'Active',
                'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400':
                  user.status === 'Pending',
                'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-500':
                  user.status === 'Cancel',
              },
            ]"
          >
            {{ user.status }}
          </span>
        </td>
        <td class="px-5 py-4 sm:px-6">
          <p class="text-gray-500 text-theme-sm dark:text-gray-400">
            {{ user.budget }}
          </p>
        </td>
      </tr>
    </tbody>
  </BaseTable>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BaseTable from '@/components/base/BaseTable.vue'
import { teamMembers } from '@/data/users'

const users = ref(teamMembers)
</script>
