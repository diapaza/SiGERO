<template>
  <div>
    <div class="mb-4 flex items-center gap-3">
      <BaseLabel label-for="data-table-search" label="Search" />
      <input
        id="data-table-search"
        v-model="search"
        type="text"
        placeholder="Search..."
        class="h-11 w-full max-w-sm rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
      />
    </div>
    <BaseDataTable v-model:global-filter="search" :columns="columns" :data="data" :page-size="5" />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BaseLabel from '@/components/base/BaseLabel.vue'
import BaseDataTable from '@/components/base/BaseDataTable.vue'
import { columns } from './data-table-demo'
import type { User } from './data-table-demo'

const search = ref('')

const data: User[] = [
  {
    name: 'Lindsey Curtis',
    email: 'lindsey@example.com',
    role: 'Web Designer',
    status: 'Active',
    registered: 'Jan 15, 2025',
    avatar: '/images/user/user-17.jpg',
  },
  {
    name: 'Kaiya George',
    email: 'kaiya@example.com',
    role: 'Project Manager',
    status: 'Pending',
    registered: 'Feb 3, 2025',
    avatar: '/images/user/user-18.jpg',
  },
  {
    name: 'Zain Geidt',
    email: 'zain@example.com',
    role: 'Content Writer',
    status: 'Active',
    registered: 'Mar 12, 2025',
    avatar: '/images/user/user-19.jpg',
  },
  {
    name: 'Abram Schleifer',
    email: 'abram@example.com',
    role: 'Digital Marketer',
    status: 'Cancel',
    registered: 'Apr 28, 2025',
    avatar: '/images/user/user-20.jpg',
  },
  {
    name: 'Carla George',
    email: 'carla@example.com',
    role: 'Front-end Developer',
    status: 'Active',
    registered: 'May 7, 2025',
    avatar: '/images/user/user-21.jpg',
  },
  {
    name: 'Bryce Galvan',
    email: 'bryce@example.com',
    role: 'UX Designer',
    status: 'Pending',
    registered: 'Jun 19, 2025',
    avatar: '/images/user/user-22.jpg',
  },
  {
    name: 'Maya Patel',
    email: 'maya@example.com',
    role: 'Data Analyst',
    status: 'Active',
    registered: 'Jul 8, 2025',
    avatar: '/images/user/user-23.jpg',
  },
  {
    name: 'Ethan Chen',
    email: 'ethan@example.com',
    role: 'Backend Developer',
    status: 'Active',
    registered: 'Aug 14, 2025',
    avatar: '/images/user/user-24.jpg',
  },
  {
    name: 'Sophia Rivera',
    email: 'sophia@example.com',
    role: 'Product Manager',
    status: 'Pending',
    registered: 'Sep 2, 2025',
    avatar: '/images/user/user-25.jpg',
  },
  {
    name: "Liam O'Brien",
    email: 'liam@example.com',
    role: 'DevOps Engineer',
    status: 'Cancel',
    registered: 'Sep 20, 2025',
    avatar: '/images/user/user-26.jpg',
  },
  {
    name: 'Emma Watson',
    email: 'emma@example.com',
    role: 'UI Designer',
    status: 'Active',
    registered: 'Oct 11, 2025',
    avatar: '/images/user/user-27.jpg',
  },
  {
    name: 'Noah Kim',
    email: 'noah@example.com',
    role: 'Full Stack Developer',
    status: 'Active',
    registered: 'Nov 5, 2025',
    avatar: '/images/user/user-28.jpg',
  },
  {
    name: 'Ava Martinez',
    email: 'ava@example.com',
    role: 'Marketing Lead',
    status: 'Pending',
    registered: 'Dec 1, 2025',
    avatar: '/images/user/user-29.jpg',
  },
  {
    name: 'James Wilson',
    email: 'james@example.com',
    role: 'QA Engineer',
    status: 'Active',
    registered: 'Dec 22, 2025',
    avatar: '/images/user/user-30.jpg',
  },
]
</script>
