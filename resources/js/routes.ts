import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from './composables/auth';

export const routes = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/login',
            component: () => import('./pages/auth/login.vue'),
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
            children: [
                {
                    path: '',
                    component: () => import('./pages/hospital.vue')
                }
            ]
        }
    ]
})