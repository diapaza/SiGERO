<template>
  <div
    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
  >
    <div class="max-w-full overflow-x-auto custom-scrollbar">
      <table class="min-w-full">
        <thead>
          <tr
            v-for="headerGroup in table.getHeaderGroups()"
            :key="headerGroup.id"
            class="border-b border-gray-200 dark:border-gray-700"
          >
            <th
              v-for="header in headerGroup.headers"
              :key="header.id"
              :class="[
                'px-5 py-3 text-left font-bold text-gray-500 text-theme-xs dark:text-gray-400',
                header.column.getCanSort() ? 'cursor-pointer select-none' : '',
              ]"
              @click="header.column.getToggleSortingHandler()?.($event)"
            >
              <div class="flex items-center gap-1">
                <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                <span v-if="header.column.getIsSorted()" class="text-gray-400">{{
                  header.column.getIsSorted() === 'asc' ? '▲' : '▼'
                }}</span>
              </div>
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr
            v-for="row in table.getRowModel().rows"
            :key="row.id"
            class="border-t border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.02]"
          >
            <td
              v-for="cell in row.getVisibleCells()"
              :key="cell.id"
              class="px-5 py-4 sm:px-6 text-gray-500 text-theme-sm dark:text-gray-400"
            >
              <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
            </td>
          </tr>
          <tr v-if="table.getRowModel().rows.length === 0">
            <td
              :colspan="table.getAllColumns().length"
              class="px-5 py-8 text-center text-gray-400 text-theme-sm dark:text-gray-500"
            >
              No results found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div
      v-if="table.getPageCount() > 1"
      class="flex flex-col gap-3 px-5 py-3 border-t border-gray-200 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between"
    >
      <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
        <span>Show</span>
        <select
          v-model="pageSizeModel"
          class="h-8 rounded-md border border-gray-300 bg-transparent px-2 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90"
        >
          <option v-for="size in pageSizeOptions" :key="size" :value="size">
            {{ size }}
          </option>
        </select>
        <span>entries</span>
      </div>

      <div class="flex items-center gap-1">
        <button
          :disabled="!table.getCanPreviousPage()"
          class="px-3 py-1 text-sm rounded-md text-gray-500 hover:bg-gray-100 disabled:text-gray-300 disabled:cursor-not-allowed dark:text-gray-400 dark:hover:bg-white/[0.05] dark:disabled:text-gray-600"
          @click="table.previousPage()"
        >
          ‹ Prev
        </button>

        <template v-for="(page, idx) in visiblePages" :key="idx">
          <button
            v-if="page === '...'"
            disabled
            class="px-2 py-1 text-sm text-gray-400 dark:text-gray-500"
          >
            ...
          </button>
          <button
            v-else
            :class="[
              'px-3 py-1 text-sm rounded-md min-w-[32px]',
              page === table.getState().pagination.pageIndex + 1
                ? 'bg-brand-500 text-white'
                : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/[0.05]',
            ]"
            @click="table.setPageIndex(page - 1)"
          >
            {{ page }}
          </button>
        </template>

        <button
          :disabled="!table.getCanNextPage()"
          class="px-3 py-1 text-sm rounded-md text-gray-500 hover:bg-gray-100 disabled:text-gray-300 disabled:cursor-not-allowed dark:text-gray-400 dark:hover:bg-white/[0.05] dark:disabled:text-gray-600"
          @click="table.nextPage()"
        >
          Next ›
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts" generic="T">
import { ref, computed } from 'vue'
import {
  useVueTable,
  getCoreRowModel,
  getSortedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  FlexRender,
  type ColumnDef,
  type SortingState,
  type PaginationState,
} from '@tanstack/vue-table'

interface Props {
  columns: ColumnDef<T>[]
  data: T[]
  globalFilter?: string
  pageSize?: number
  pageSizeOptions?: number[]
}

interface Emits {
  (e: 'update:globalFilter', value: string): void
}

const props = withDefaults(defineProps<Props>(), {
  globalFilter: '',
  pageSize: 10,
  pageSizeOptions: () => [5, 10, 20, 50],
})
const emit = defineEmits<Emits>()

const sorting = ref<SortingState>([])
const pagination = ref<PaginationState>({ pageIndex: 0, pageSize: props.pageSize })

const filterModel = computed({
  get: () => props.globalFilter,
  set: (val) => emit('update:globalFilter', val),
})

const pageSizeModel = computed({
  get: () => pagination.value.pageSize,
  set: (val: number) => {
    pagination.value = { ...pagination.value, pageSize: val, pageIndex: 0 }
  },
})

const table = useVueTable({
  get data() {
    return props.data
  },
  get columns() {
    return props.columns
  },
  state: {
    get sorting() {
      return sorting.value
    },
    get globalFilter() {
      return filterModel.value
    },
    get pagination() {
      return pagination.value
    },
  },
  onSortingChange: (updaterOrValue) => {
    sorting.value =
      typeof updaterOrValue === 'function' ? updaterOrValue(sorting.value) : updaterOrValue
  },
  onGlobalFilterChange: (updaterOrValue) => {
    const newValue =
      typeof updaterOrValue === 'function' ? updaterOrValue(filterModel.value) : updaterOrValue
    filterModel.value = newValue ?? ''
  },
  onPaginationChange: (updaterOrValue) => {
    pagination.value =
      typeof updaterOrValue === 'function' ? updaterOrValue(pagination.value) : updaterOrValue
  },
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
})

const visiblePages = computed(() => {
  const pageCount = table.getPageCount()
  const currentPage = table.getState().pagination.pageIndex
  const pages: (number | string)[] = []

  if (pageCount <= 7) {
    for (let i = 0; i < pageCount; i++) pages.push(i + 1)
    return pages
  }

  pages.push(1)

  let start = Math.max(1, currentPage - 1)
  let end = Math.min(pageCount - 2, currentPage + 1)

  if (currentPage <= 1) {
    end = Math.min(3, pageCount - 2)
  }
  if (currentPage >= pageCount - 3) {
    start = Math.max(1, pageCount - 5)
  }

  if (start > 1) pages.push('...')
  for (let i = start; i <= end; i++) pages.push(i + 1)
  if (end < pageCount - 2) pages.push('...')

  pages.push(pageCount)

  return pages
})
</script>
