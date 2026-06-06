<template>
  <BaseDataTable :columns="columns" :data="data" />
</template>

<script setup lang="ts">
import BaseDataTable from '@/components/base/BaseDataTable.vue'
import { columns } from './data-table-demo'
import type { User } from './data-table-demo'

const data: User[] = [
  { name: 'Lindsey Curtis', email: 'lindsey@example.com', role: 'Web Designer', status: 'Active', registered: 'Jan 15, 2025', avatar: '/images/user/user-17.jpg' },
  { name: 'Kaiya George', email: 'kaiya@example.com', role: 'Project Manager', status: 'Pending', registered: 'Feb 3, 2025', avatar: '/images/user/user-18.jpg' },
  { name: 'Zain Geidt', email: 'zain@example.com', role: 'Content Writer', status: 'Active', registered: 'Mar 12, 2025', avatar: '/images/user/user-19.jpg' },
  { name: 'Abram Schleifer', email: 'abram@example.com', role: 'Digital Marketer', status: 'Cancel', registered: 'Apr 28, 2025', avatar: '/images/user/user-20.jpg' },
  { name: 'Carla George', email: 'carla@example.com', role: 'Front-end Developer', status: 'Active', registered: 'May 7, 2025', avatar: '/images/user/user-21.jpg' },
  { name: 'Bryce Galvan', email: 'bryce@example.com', role: 'UX Designer', status: 'Pending', registered: 'Jun 19, 2025', avatar: '/images/user/user-22.jpg' },
]
</script>
