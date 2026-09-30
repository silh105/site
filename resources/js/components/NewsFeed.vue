<template>
  <div style="max-width: 680px; margin: 0 auto;">
    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:16px;">
      <select v-model="newPost.category_id" style="margin-bottom:8px; padding:6px; border-radius:6px; border:1px solid #cbd5e1;">
        <option disabled value="">Выберите категорию</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <textarea v-model="newPost.text" placeholder="Что у вас нового?" style="width:100%; min-height:60px; padding:8px; border:1px solid #e2e8f0; border-radius:6px;"></textarea>
      <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
        <input type="text" v-model="newPost.video_url" placeholder="Ссылка на YouTube" style="padding:6px; border:1px solid #cbd5e1; border-radius:6px; width:65%;" />
        <button @click="createPost" style="background:#3b82f6; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">Опубликовать</button>
      </div>
    </div>
    <div v-for="post in posts" :key="post.id" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:12px;">
      <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
        <strong>{{ post.user?.profile?.first_name || post.user?.login }}</strong>
        <span style="background:#eff6ff; color:#2563eb; font-size:11px; padding:2px 8px; border-radius:4px;">{{ post.category?.name }}</span>
      </div>
      <p style="margin-bottom:8px;">{{ post.text }}</p>
      <div v-if="getYoutubeId(post.video_url)" style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden; border-radius:8px;">
        <iframe style="position:absolute; top:0; left:0; width:100%; height:100%;" :src="'https://www.youtube.com/embed/' + getYoutubeId(post.video_url)" frameborder="0" allowfullscreen></iframe>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, reactive, onMounted } from 'vue';
const categories = ref([]);
const posts = ref([]);
const newPost = reactive({ category_id: '', text: '', video_url: '' });
onMounted(async () => {
  const cRes = await fetch('/api/categories');
  if (cRes.ok) categories.value = await cRes.json();
  const pRes = await fetch('/api/posts');
  if (pRes.ok) { const d = await pRes.json(); posts.value = d.data || []; }
});
const getYoutubeId = (url) => {
  if (!url) return null;
  const m = url.match(/(?:youtu\.be\/|youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=))([^"&?\/\s]{11})/);
  return m ? m[1] : null;
};
const createPost = async () => {
  if (!newPost.text || !newPost.category_id) return;
  const res = await fetch('/api/posts', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(newPost) });
  if (res.ok) { posts.value.unshift(await res.json()); newPost.text = ''; newPost.video_url = ''; }
};
</script>