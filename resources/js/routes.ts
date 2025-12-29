import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from './composables/auth';

export const routes = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/login',
            component: () => import('./pages/auth/Login.vue'),
            beforeEnter: async (to, from, next) => {
                const auth = useAuth()
                await auth.me()

                if (auth.user.value != null) {
                    return next('/')
                }
                return next()
            }
        },
        {
            path: '/',
            beforeEnter: async (to, from, next) => {
                const auth = useAuth()
                await auth.me()

                if (auth.user.value != null) {
                    return next()
                }
                return next('/login')
            },
            component: () => import('./layouts/AdminLayout.vue'),
            children: [
                {
                    path: '',
                    component: () => import('./pages/Dashboard.vue')
                },
                {
                    path: 'rumah-sakit',
                    component: () => import('./pages/RumahSakit.vue')
                },
                {
                    path: 'pasien',
                    component: () => import('./pages/Pasien.vue')
                },
            ]
        },
        {
            path: '/:pathMatch(.*)*',
            component: () => import('./pages/NotFound.vue')
        }
    ]
})