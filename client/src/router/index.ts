import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import ChecklistView from '@/views/ChecklistView.vue'
import EditChecklistView from '@/views/EditChecklistView.vue'
import ConsentFormView from '@/views/ConsentFormView.vue'

type UserRole = 'assessor' | 'consultant'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { requiresAuth: false }
    },
    {
      path: '/checklist',
      name: 'checklist',
      component: ChecklistView,
      meta: { requiresAuth: true, allowedRoles: ['assessor'] as UserRole[] }
    },
    {
      path: '/edit-checklist',
      name: 'edit-checklist',
      component: EditChecklistView,
      meta: { requiresAuth: true, allowedRoles: ['consultant'] as UserRole[] }
    },
    {
      path: '/screening',
      name: 'screening',
      component: ConsentFormView,
      meta: { requiresAuth: true, allowedRoles: ['consultant'] as UserRole[] }
    }
  ]
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')
  const userRole = localStorage.getItem('user_role') as UserRole | null

  if (to.meta.requiresAuth) {
    if (!token) {
      next({ name: 'home' })
    } else if (to.meta.allowedRoles && userRole && !(to.meta.allowedRoles as UserRole[]).includes(userRole)) {
      if (userRole === 'assessor') {
        next({ name: 'checklist' })
      } else if (userRole === 'consultant') {
        next({ name: 'edit-checklist' })
      } else {
        next({ name: 'home' }) 
      }
    } else {
      next()
    }
  } else {
    if (token && to.name === 'home') {
      if (userRole === 'assessor') {
        next({ name: 'checklist' })
      } else if (userRole === 'consultant') {
        next({ name: 'edit-checklist' })
      } else {
        next() 
      }
    } else {
      next() 
    }
  }
})

export default router