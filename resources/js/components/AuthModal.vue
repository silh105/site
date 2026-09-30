<template>
  <div v-if="isOpen" class="modal-backdrop" @click.self="$emit('close')">
    <div class="modal-card">
      <button class="close-btn" @click="$emit('close')">&times;</button>
      <div class="tab-header">
        <button :class="['tab-btn', { active: tab === 'login' }]" @click="tab = 'login'">Вход</button>
        <button :class="['tab-btn', { active: tab === 'register' }]" @click="tab = 'register'">Регистрация</button>
      </div>
      <div v-if="serverError" class="alert-error">{{ serverError }}</div>
      <form v-if="tab === 'login'" @submit.prevent="login">
        <div class="form-group"><label>Логин</label><input v-model="loginForm.login" required /></div>
        <div class="form-group"><label>Пароль</label><input type="password" v-model="loginForm.password" required /></div>
        <button type="submit" class="submit-btn">Войти</button>
      </form>
      <form v-else @submit.prevent="register">
        <div class="form-group"><label>Имя</label><input v-model="regForm.first_name" required /></div>
        <div class="form-group"><label>Фамилия</label><input v-model="regForm.last_name" required /></div>
        <div class="form-group"><label>Логин (латиница)</label><input v-model="regForm.login" required /></div>
        <div class="form-group"><label>Email</label><input type="email" v-model="regForm.email" required /></div>
        <div class="form-group"><label>Пароль</label><input type="password" v-model="regForm.password" required /></div>
        <div class="form-group"><label>Повтор пароля</label><input type="password" v-model="regForm.password_confirmation" required /></div>
        <label><input type="checkbox" v-model="regForm.terms" required /> Согласие на обработку данных</label>
        <button type="submit" class="submit-btn" style="margin-top:10px;">Зарегистрироваться</button>
      </form>
    </div>
  </div>
</template>
<script setup>
import { ref, reactive } from 'vue';
defineProps({ isOpen: Boolean });
const emit = defineEmits(['close']);
const tab = ref('login');
const serverError = ref('');
const loginForm = reactive({ login: '', password: '' });
const regForm = reactive({ first_name: '', last_name: '', login: '', email: '', password: '', password_confirmation: '', terms: false });
const login = async () => {
  const res = await fetch('/api/login', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(loginForm) });
  const data = await res.json();
  if (data.success) window.location.href = data.redirect || '/feed'; else serverError.value = data.message;
};
const register = async () => {
  const res = await fetch('/api/register', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(regForm) });
  const data = await res.json();
  if (data.success) window.location.href = '/feed'; else serverError.value = data.message || 'Ошибка данных';
};
</script>
<style scoped>
.modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal-card { background: #fff; border-radius: 12px; width: 90%; max-width: 420px; padding: 20px; position: relative; }
.close-btn { position: absolute; right: 14px; top: 14px; border: none; background: none; font-size: 20px; cursor: pointer; }
.tab-header { display: flex; margin-bottom: 16px; border-bottom: 1px solid #e2e8f0; }
.tab-btn { flex: 1; padding: 8px; border: none; background: none; font-weight: 600; cursor: pointer; }
.tab-btn.active { border-bottom: 2px solid #3b82f6; color: #1e3a8a; }
.form-group { display: flex; flex-direction: column; margin-bottom: 10px; }
.form-group label { font-size: 12px; font-weight: 600; margin-bottom: 4px; }
.form-group input { padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
.submit-btn { width: 100%; padding: 10px; background: #1e3a8a; color: #fff; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
.alert-error { background: #fee2e2; color: #b91c1c; padding: 8px; border-radius: 6px; margin-bottom: 10px; font-size: 12px; }
</style>