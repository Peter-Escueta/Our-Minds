import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import ChecklistView from '@/views/ChecklistView.vue'
import EditChecklistView from '@/views/EditChecklistView.vue'
import ConsentFormView from '@/views/ConsentFormView.vue'
import ChildListView from '@/views/ChildListView.vue'
import ResultView from '@/views/ResultView.vue'
import EvaluationView from '@/views/EvaluationView.vue'
import ReadEvaluationView from '@/views/ReadEvaluationView.vue'

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
      path: '/assessments/:id/create', 
      name: 'assessment-create',     
      component: ChecklistView,
      meta: { requiresAuth: true, allowedRoles: ['assessor'] as UserRole[] },
      props: true 
    },
    {
      path: '/edit-checklist',
      name: 'edit-checklist',
      component: EditChecklistView,
      meta: { requiresAuth: true, allowedRoles: ['consultant'] as UserRole[] }
    },
    {
      path: '/children/create',
      name: 'screening',
      component: ConsentFormView,
      meta: { requiresAuth: true, allowedRoles: ['consultant'] as UserRole[] }
    },
     {
      path: '/children',
      name: 'children',
      component: ChildListView,
      meta: { requiresAuth: true, allowedRoles: ['consultant', 'assessor'] as UserRole[] }
    },
    {
  path: '/assessments/:id/results',
  name: 'assessment-results',
  component: ResultView,
  meta: { requiresAuth: true }
},
  {
  path: '/assessments/:id/evaluate',
  name: 'assessment-evaluate',
  component: EvaluationView,
  meta: { requiresAuth: true }
},
   {
      path: '/evaluations/:id',
      name: 'evaluation-view',
      component: ReadEvaluationView,
      meta: { requiresAuth: true } 
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
        next({ name: 'children' })
      } else if (userRole === 'consultant') {
        next({ name: 'edit-checklist' })
      } else {'/'
        next({ name: 'home' }) 
      }
    } else {
      next()
    }
  } else {
    if (token && to.name === 'home') {
      if (userRole === 'assessor') {
        next({ name: 'children' })
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