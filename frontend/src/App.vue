<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'
import { storeToRefs } from 'pinia'

const router = useRouter()
const auth = useAuthStore()
const { isAuthenticated } = storeToRefs(auth)

const logout = async () => {
  await auth.logout()
  router.push('/admin/login')
}
</script>

<template>
  <header style="display:flex; gap:12px; align-items:center; padding:8px 0">
    <router-link to="/news">Новости</router-link>

    <!-- Видна только авторизованным -->
    <router-link v-if="isAuthenticated" to="/admin/news">Админка</router-link>

    <!-- Видна только НЕ авторизованным -->
    <router-link v-else to="/admin/login">Вход</router-link>

    <!-- Кнопка выхода видна только авторизованным -->
    <button v-if="isAuthenticated" @click="logout">Выйти</button>
  </header>

  <router-view />
</template>
