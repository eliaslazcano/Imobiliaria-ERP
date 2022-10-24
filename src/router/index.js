import Vue from 'vue'
import VueRouter from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import NotFound from '../views/NotFound.vue'
import store from '@/store'

Vue.use(VueRouter)

const routes = [
  {
    path: '*',
    component: NotFound
  },
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: {
      hideAppBar: true,
      hideNavigationDrawer: true
    },
  },
  {
    path: '/perfil',
    name: 'perfil',
    component: () => import('../views/MeuPerfil'),
    meta: {
      title: 'Seu perfil'
    },
  },
]

const router = new VueRouter({
  routes
})

router.beforeEach((to, from, next) => {
  if (store.state.token) {
    if (to.name === 'login') next('/');
    else next();
  } else {
    if (to.name === 'login') next();
    else next('/login');
  }
})

export default router
