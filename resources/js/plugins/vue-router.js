import Vue from 'vue'
import VueRouter from 'vue-router'
import store from './vuex'

import Index from '../views/Index'
import Login from '../views/Login'
import Register from '../views/Register'
import SelectCompany from '../views/SelectCompany'
import Settings from '../views/Settings'

Vue.use(VueRouter)

const routes = [
    {
        path: '/',
        name: 'Index',
        component: Index,
    },
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: {
            requiresVisitor: true
        }
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
        meta: {
            requiresVisitor: true
        }
    },
    {
        path: '/select-company',
        name: 'SelectCompany',
        component: SelectCompany,
        meta: {
            requiresAuth: true
        }
    },
    {
        path: '/settings',
        name: 'Settings',
        component: Settings,
        meta: {
            requiresAuth: true
        }
    }
]

const router = new VueRouter({
    mode: 'history',
    routes
})

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!store.getters.authenticated) {
            next({ name: 'Login' })
        } else {
            if (to.name === 'SelectCompany' && store.state.user.f !== 'NONE') {
                next({ name: 'Index' })
            } else {
                next()
            }
        }
    } else if (to.matched.some(record => record.meta.requiresVisitor)) {
        if (store.getters.authenticated) {
            next({ name: 'Index' })
        } else {
            next()
        }
    } else {
        if (store.getters.authenticated && store.state.user.f === 'NONE') {
            next({ name: 'SelectCompany' })
        } else {
            next()
        }
    }
})

export default router