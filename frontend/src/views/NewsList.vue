<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const items = ref([])
const loading = ref(false)
const error = ref(null)

onMounted(async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/news')
    items.value = data
  } catch (e) {
    error.value = 'Ошибка загрузки'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <h1>Новости</h1>
    <div v-if="loading">Загрузка...</div>
    <div v-else-if="error">{{ error }}</div>
    <ul v-else>
      <li v-for="n in items" :key="n.id">
        <router-link :to="`/news/${n.slug}`">{{ n.title }}</router-link>
        <small v-if="n.published_at"> — {{ new Date(n.published_at).toLocaleString() }}</small>
      </li>
    </ul>
  </div>
</template>
