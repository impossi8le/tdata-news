<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRoute } from 'vue-router'

const route = useRoute()
const item = ref(null)
const loading = ref(false)
const error = ref(null)

onMounted(async () => {
  loading.value = true
  try {
    const { data } = await axios.get(`/api/news/${route.params.slug}`)
    item.value = data
  } catch (e) {
    error.value = 'Новость не найдена'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <div v-if="loading">Загрузка...</div>
    <div v-else-if="error">{{ error }}</div>
    <article v-else>
      <h1>{{ item.title }}</h1>
      <p v-html="item.content"></p>
      <p v-if="item.published_at"><small>Опубликовано: {{ new Date(item.published_at).toLocaleString() }}</small></p>
    </article>
  </div>
</template>
