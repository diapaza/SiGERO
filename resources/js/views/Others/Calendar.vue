<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div
      class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
    >
      <div class="custom-calendar">
        <FullCalendar ref="calendarRef" class="min-h-screen" :options="calendarOptions" />
      </div>

      <!-- Modal -->
      <BaseModal v-model:isOpen="isOpen" :title="selectedEvent ? 'Edit Event' : 'Add Event'" size="lg" contentClass="rounded-3xl" @close="resetModalFields">
        <template #body>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Plan your next big moment: schedule or edit an event to stay on track
          </p>

          <div class="mt-8">
            <div>
              <BaseLabel label="Event Title" />
              <input
                v-model="eventTitle"
                type="text"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              />
            </div>

            <div class="mt-6">
              <label class="block mb-4 text-sm font-medium text-gray-700 dark:text-gray-400">
                Event Color
              </label>
              <div class="flex flex-wrap items-center gap-4 sm:gap-5">
                <div v-for="(value, key) in calendarsEvents" :key="key" class="n-chk">
                  <div :class="`form-check form-check-${value} form-check-inline`">
                    <label
                      class="flex items-center text-sm text-gray-700 form-check-label dark:text-gray-400"
                      :for="`modal${key}`"
                    >
                      <span class="relative">
                        <input
                          type="radio"
                          :name="'event-level'"
                          :value="key"
                          :id="`modal${key}`"
                          v-model="eventLevel"
                          class="sr-only form-check-input"
                        />
                        <span
                          class="flex items-center justify-center w-5 h-5 mr-2 border border-gray-300 rounded-full box dark:border-gray-700"
                        >
                          <span class="w-2 h-2 bg-white rounded-full dark:bg-transparent"></span>
                        </span>
                      </span>
                      {{ key }}
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6">
              <BaseLabel label="Enter Start Date" />
              <input
                v-model="eventStartDate"
                type="date"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              />
            </div>

            <div class="mt-6">
              <BaseLabel label="Enter End Date" />
              <input
                v-model="eventEndDate"
                type="date"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              />
            </div>
          </div>
        </template>
        <template #actions>
          <BaseButton variant="outline" @click="closeModal">Close</BaseButton>
          <BaseButton variant="primary" @click="handleAddOrUpdateEvent">{{ selectedEvent ? 'Update Changes' : 'Add Event' }}</BaseButton>
          <BaseButton v-if="selectedEvent" variant="danger" @click="handleDeleteEvent">Delete Event</BaseButton>
        </template>
      </BaseModal>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/shared/PageBreadcrumb.vue'
import BaseModal from '@/components/base/BaseModal.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseLabel from '@/components/base/BaseLabel.vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'

const currentPageTitle = ref('Calendar')

const calendarRef = ref(null)
const isOpen = ref(false)
const selectedEvent = ref(null)
const eventTitle = ref('')
const eventStartDate = ref('')
const eventEndDate = ref('')
const eventLevel = ref('')
const events = ref([])

const calendarsEvents = reactive({
  Danger: 'danger',
  Success: 'success',
  Primary: 'primary',
  Warning: 'warning',
})

onMounted(() => {
  events.value = [
    {
      id: '1',
      title: 'Event Conf.',
      start: new Date().toISOString().split('T')[0],
      extendedProps: { calendar: 'Danger' },
    },
    {
      id: '2',
      title: 'Meeting',
      start: new Date(Date.now() + 86400000).toISOString().split('T')[0],
      extendedProps: { calendar: 'Success' },
    },
    {
      id: '3',
      title: 'Workshop',
      start: new Date(Date.now() + 172800000).toISOString().split('T')[0],
      end: new Date(Date.now() + 259200000).toISOString().split('T')[0],
      extendedProps: { calendar: 'Primary' },
    },
  ]
})

const openModal = () => {
  isOpen.value = true
}

const closeModal = () => {
  isOpen.value = false
  resetModalFields()
}

const resetModalFields = () => {
  eventTitle.value = ''
  eventStartDate.value = ''
  eventEndDate.value = ''
  eventLevel.value = ''
  selectedEvent.value = null
}

const handleDateSelect = (selectInfo) => {
  resetModalFields()
  eventStartDate.value = selectInfo.startStr
  eventEndDate.value = selectInfo.endStr || selectInfo.startStr
  openModal()
}

const handleEventClick = (clickInfo) => {
  const event = clickInfo.event
  selectedEvent.value = event
  eventTitle.value = event.title
  eventStartDate.value = event.start?.toISOString().split('T')[0] || ''
  eventEndDate.value = event.end?.toISOString().split('T')[0] || ''
  eventLevel.value = event.extendedProps.calendar
  openModal()
}

const handleAddOrUpdateEvent = () => {
  if (selectedEvent.value) {
    // Update existing event
    events.value = events.value.map((event) =>
      event.id === selectedEvent.value.id
        ? {
            ...event,
            title: eventTitle.value,
            start: eventStartDate.value,
            end: eventEndDate.value,
            extendedProps: { calendar: eventLevel.value },
          }
        : event,
    )
  } else {
    // Add new event
    const newEvent = {
      id: Date.now().toString(),
      title: eventTitle.value,
      start: eventStartDate.value,
      end: eventEndDate.value,
      allDay: true,
      extendedProps: { calendar: eventLevel.value },
    }
    events.value.push(newEvent)
  }
  closeModal()
}
const handleDeleteEvent = () => {
  if (selectedEvent.value) {
    events.value = events.value.filter((event) => event.id !== selectedEvent.value.id)
    closeModal()
  }
}

const renderEventContent = (eventInfo) => {
  const colorClass = `fc-bg-${eventInfo.event.extendedProps.calendar.toLowerCase()}`
  return {
    html: `
      <div class="event-fc-color flex fc-event-main ${colorClass} p-1 rounded-sm">
        <div class="fc-daygrid-event-dot"></div>
        <div class="fc-event-time">${eventInfo.timeText}</div>
        <div class="fc-event-title">${eventInfo.event.title}</div>
      </div>
    `,
  }
}

const calendarOptions = reactive({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  headerToolbar: {
    left: 'prev,next addEventButton',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay',
  },
  events: events,
  selectable: true,
  select: handleDateSelect,
  eventClick: handleEventClick,
  eventContent: renderEventContent,
  customButtons: {
    addEventButton: {
      text: 'Add Event +',
      click: openModal,
    },
  },
})
</script>
