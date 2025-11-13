import { createRouter, createWebHistory } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue';
import { useAuthStore } from '@/stores/auth'

import Dashboard from '@/pages/Dashboard.vue'
import Constancias from '@/pages/Constancias.vue'
import Login from '@/pages/auth/Login.vue'
import Users from '@/pages/Users.vue'

const routes = [
  { 
    path: '/login', name: 'Login', component: Login 
  },
  {
    path: '/',
    component: AppLayout,
    meta: { requiresAuth: true }, // protege todo el layout
    children: [
      { path: '', name: 'Dashboard', component: Dashboard },
      { path: '/pages/constancias', name: 'Constancias', component: Constancias, meta: { permission: 'constancias.view' } },
      { path: '/pages/usuarios', name: 'Users', component: Users, meta: { permission: 'users.view' } }, // o el permiso que uses
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Protege rutas
router.beforeEach(async (to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !(await auth.validateToken())) {
    return { name: 'Login' }
  }

  // Valida permisos
  if (to.meta.permission && !auth.hasPermission(to.meta.permission)) {
    return { name: 'Dashboard' } // Redirige si no tiene permiso
  }
})

export default router