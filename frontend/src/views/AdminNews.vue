<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const list = ref([])
const loading = ref(false)
const error = ref(null)
const router = useRouter()

const form = ref({
  id: null,
  title: '',
  slug: '',
  content: '',
  is_published: false,
  published_at: ''
})

const resetForm = () => {
  form.value = { id: null, title: '', slug: '', content: '', is_published: false, published_at: '' }
}

const load = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/anews')
    list.value = data
  } catch (e) {
    error.value = 'Ошибка загрузки'
  } finally {
    loading.value = false
  }
}

const save = async () => {
  try {
    if (form.value.id) {
      await axios.put(`/api/news/${form.value.id}`, form.value)
    } else {
      const { data } = await axios.post('/api/news', form.value)
      list.value.unshift(data)
    }
    await load()
    resetForm()
  } catch (e) {
    alert('Ошибка сохранения')
  }
}

const edit = (row) => {
  form.value = { ...row }
}

const removeItem = async (row) => {
  if (!confirm('Удалить новость?')) return
  await axios.delete(`/api/news/${row.id}`)
  await load()
}

const logout = async () => {
  await auth.logout()
  router.push('/admin/login')
}

onMounted(() => {
  if (!auth.isAuthenticated) return
  load()
})
</script>

<template>
  <div>
    <div style="display:flex; justify-content:space-between; align-items:center">
      <h1>Админка: Новости</h1>
      <button @click="$router.push('/news')">К списку новостей</button>
      <button @click="logout">Выйти</button>
    </div>

    <section style="margin:16px 0; padding:12px; border:1px solid #ddd">
      <h3>{{ form.id ? 'Редактировать новость' : 'Создать новость' }}</h3>
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px">
        <label>Заголовок <input v-model="form.title" /></label>
        <label>Слаг <input v-model="form.slug" /></label>
        <label>Опубликовано <input type="checkbox" v-model="form.is_published" /></label>
        <label>Дата публикации <input v-model="form.published_at" type="datetime-local" /></label>
      </div>
      <div style="margin-top:12px">
        <label>Контент</label>
        <textarea v-model="form.content" rows="6" style="width:100%"></textarea>
      </div>
      <div style="margin-top:12px; display:flex; gap:8px">
        <button @click="save">Сохранить</button>
        <button v-if="form.id" @click="resetForm" type="button">Отмена</button>
      </div>
    </section>

    <div v-if="loading">Загрузка...</div>
    <div v-else-if="error">{{ error }}</div>

    <table v-else border="1" cellpadding="6" cellspacing="0">
      <thead>
        <tr>
          <th>ID</th><th>Заголовок</th><th>Слаг</th><th>Опубл.</th><th>Публ. дата</th><th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="n in list" :key="n.id">
          <td>{{ n.id }}</td>
          <td>{{ n.title }}</td>
          <td>{{ n.slug }}</td>
          <td>{{ n.is_published ? 'Да' : 'Нет' }}</td>
          <td>{{ n.published_at ? new Date(n.published_at).toLocaleString() : '' }}</td>
          <td>
            <button @click="edit(n)">Редактировать</button>
            <button @click="removeItem(n)">Удалить</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
