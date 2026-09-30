<template>
  <div style="display:flex; height:calc(100vh - 120px); background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
    <div style="width:300px; border-right:1px solid #e2e8f0; overflow-y:auto;">
      <div style="padding:14px; font-weight:700; border-bottom:1px solid #f1f5f9;">Диалоги</div>
      <div v-for="c in chats" :key="c.id" @click="selectChat(c)" :style="{ padding:'12px', cursor:'pointer', background: activeChat?.id === c.id ? '#eff6ff' : 'transparent', borderBottom:'1px solid #f8fafc' }">
        <div style="font-weight:600; font-size:13px;">{{ c.companion?.name }}</div>
        <div style="font-size:12px; color:#64748b;">{{ c.last_message?.text || 'Нет сообщений' }}</div>
      </div>
    </div>
    <div style="flex:1; display:flex; flex-direction:column; background:#f8fafc;">
      <div v-if="activeChat" style="padding:12px; background:#fff; border-bottom:1px solid #e2e8f0; font-weight:600;">{{ activeChat.companion?.name }}</div>
      <div style="flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:8px;">
        <div v-for="m in messages" :key="m.id" :style="{ alignSelf: m.sender_id === currentUserId ? 'flex-end' : 'flex-start', background: m.sender_id === currentUserId ? '#1e3a8a' : '#fff', color: m.sender_id === currentUserId ? '#fff' : '#000', padding:'8px 12px', borderRadius:'10px', maxWidth:'70%' }">
          {{ m.text }}
        </div>
      </div>
      <div v-if="activeChat" style="padding:12px; background:#fff; display:flex; gap:8px; border-top:1px solid #e2e8f0;">
        <input v-model="text" @keydown.enter="send" placeholder="Напишите сообщение..." style="flex:1; padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px;" />
        <button @click="send" style="background:#3b82f6; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">Отправить</button>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
const props = defineProps({ currentUserId: Number, pusherKey: String, pusherCluster: String });
const chats = ref([]);
const activeChat = ref(null);
const messages = ref([]);
const text = ref('');
onMounted(async () => {
  const res = await fetch('/api/chats');
  if (res.ok) chats.value = await res.json();
});
const selectChat = async (c) => {
  activeChat.value = c;
  const res = await fetch(`/api/chats/${c.id}`);
  if (res.ok) messages.value = await res.json();
};
const send = async () => {
  if (!text.value.trim() || !activeChat.value) return;
  const t = text.value; text.value = '';
  const res = await fetch(`/api/chats/${activeChat.value.id}/messages`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ text: t }) });
  if (res.ok) messages.value.push(await res.json());
};
</script>