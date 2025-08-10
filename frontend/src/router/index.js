import { createRouter, createWebHistory } from 'vue-router'
import NewsList from '../views/NewsList.vue'
import NewsView from '../views/NewsView.vue'
import AdminLogin from '../views/AdminLogin.vue'
import AdminNews from '../views/AdminNews.vue'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/news' },
    { path: '/news', component: NewsList },
    { path: '/news/:slug', component: NewsView, props: true },

    // Логин только для гостей
    { path: '/admin/login', component: AdminLogin, meta: { guestOnly: true } },

    // Админка только для авторизованных
    { path: '/admin/news', component: AdminNews, meta: { requiresAuth: true } },
  ]
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next('/admin/login')
  }
  if (to.meta.guestOnly && auth.isAuthenticated) {
    return next('/admin/news')
  }
  next()
})

export default router
