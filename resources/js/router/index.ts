import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Ecommerce',
    component: () => import('../views/Ecommerce.vue'),
    meta: {
      title: 'eCommerce Dashboard',
      requiresAuth: false,
    },
  },
  {
    path: '/calendar',
    name: 'Calendar',
    component: () => import('../views/Others/Calendar.vue'),
    meta: {
      title: 'Calendar',
      requiresAuth: false,
    },
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('../views/Others/UserProfile.vue'),
    meta: {
      title: 'Profile',
      requiresAuth: false,
    },
  },
  {
    path: '/form-elements',
    name: 'Form Elements',
    component: () => import('../views/Forms/FormElements.vue'),
    meta: {
      title: 'Form Elements',
      requiresAuth: false,
    },
  },
  {
    path: '/basic-tables',
    name: 'Basic Tables',
    component: () => import('../views/Tables/BasicTables.vue'),
    meta: {
      title: 'Basic Tables',
      requiresAuth: false,
    },
  },
  {
    path: '/data-tables',
    name: 'Data Tables',
    component: () => import('../views/Tables/DataTables.vue'),
    meta: {
      title: 'Data Tables',
      requiresAuth: false,
    },
  },
  {
    path: '/line-chart',
    name: 'Line Chart',
    component: () => import('../views/Chart/LineChart/LineChart.vue'),
    meta: {
      title: 'Line Chart',
      requiresAuth: false,
    },
  },
  {
    path: '/bar-chart',
    name: 'Bar Chart',
    component: () => import('../views/Chart/BarChart/BarChart.vue'),
    meta: {
      title: 'Bar Chart',
      requiresAuth: false,
    },
  },
  {
    path: '/alerts',
    name: 'Alerts',
    component: () => import('../views/UiElements/Alerts.vue'),
    meta: {
      title: 'Alerts',
      requiresAuth: false,
    },
  },
  {
    path: '/avatars',
    name: 'Avatars',
    component: () => import('../views/UiElements/Avatars.vue'),
    meta: {
      title: 'Avatars',
      requiresAuth: false,
    },
  },
  {
    path: '/badge',
    name: 'Badge',
    component: () => import('../views/UiElements/Badges.vue'),
    meta: {
      title: 'Badge',
      requiresAuth: false,
    },
  },
  {
    path: '/buttons',
    name: 'Buttons',
    component: () => import('../views/UiElements/Buttons.vue'),
    meta: {
      title: 'Buttons',
      requiresAuth: false,
    },
  },
  {
    path: '/modal',
    name: 'Modal',
    component: () => import('../views/UiElements/Modal.vue'),
    meta: {
      title: 'Modal',
      requiresAuth: false,
    },
  },
  {
    path: '/dialogs',
    name: 'Dialogs',
    component: () => import('../views/UiElements/Dialogs.vue'),
    meta: {
      title: 'Dialogs',
      requiresAuth: false,
    },
  },
  {
    path: '/toasts',
    name: 'Toasts',
    component: () => import('../views/UiElements/Toasts.vue'),
    meta: {
      title: 'Toasts',
      requiresAuth: false,
    },
  },
  {
    path: '/images',
    name: 'Images',
    component: () => import('../views/UiElements/Images.vue'),
    meta: {
      title: 'Images',
      requiresAuth: false,
    },
  },
  {
    path: '/videos',
    name: 'Videos',
    component: () => import('../views/UiElements/Videos.vue'),
    meta: {
      title: 'Videos',
      requiresAuth: false,
    },
  },
  {
    path: '/blank',
    name: 'Blank',
    component: () => import('../views/Pages/BlankPage.vue'),
    meta: {
      title: 'Blank',
      requiresAuth: false,
    },
  },
  {
    path: '/error-404',
    name: '404 Error',
    component: () => import('../views/Errors/FourZeroFour.vue'),
    meta: {
      title: '404 Error',
      requiresAuth: false,
    },
  },
  {
    path: '/signin',
    name: 'Signin',
    component: () => import('../views/Auth/Signin.vue'),
    meta: {
      title: 'Signin',
      requiresAuth: false,
      layout: 'auth',
    },
  },
  {
    path: '/signup',
    name: 'Signup',
    component: () => import('../views/Auth/Signup.vue'),
    meta: {
      title: 'Signup',
      requiresAuth: false,
      layout: 'auth',
    },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('../views/Errors/FourZeroFour.vue'),
    meta: {
      title: 'Page Not Found',
      requiresAuth: false,
    },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { left: 0, top: 0 }
  },
  routes,
})

router.beforeEach((to) => {
  const title = to.meta.title as string | undefined
  document.title = title ? `${title} | TailAdmin` : 'TailAdmin'

  // Future: Add auth guard logic here
  // if (to.meta.requiresAuth && !isAuthenticated) {
  //   return { name: 'Signin' }
  // }
})

export default router
