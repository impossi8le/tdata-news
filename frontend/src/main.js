import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'

async function bootstrap() {
  const app = createApp(App)
  const pinia = createPinia()
  app.use(pinia)
  app.use(router)

  // Проверяем сессию при старте
  const auth = useAuthStore()
  await auth.check()

  app.mount('#app')
}

bootstrap()
