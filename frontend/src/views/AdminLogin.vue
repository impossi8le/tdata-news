<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const login = ref('')
const password = ref('')
const loading = ref(false)
const error = ref(null)
const router = useRouter()
const auth = useAuthStore()

const submit = async () => {
  error.value = null
  loading.value = true
  try {
    await auth.login(login.value, password.value)
    router.push('/admin/news')
  } catch (e) {
    const msg = e?.response?.data?.message || 'Неверные данные'
    error.value = msg
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <form @submit.prevent="submit" style="max-width:360px">
    <h1>Вход в админку</h1>
    <label>
      Логин
      <input v-model="login" type="text" required />
    </label>
    <label>
      Пароль
      <input v-model="password" type="password" required />
    </label>
    <button :disabled="loading" type="submit">Войти</button>
    <p v-if="error" style="color:red">{{ error }}</p>
  </form>
</template>

<style scoped>
label { display:block; margin:12px 0; }
input { width:100%; padding:8px; }
</style>
