<template>
  <div class="min-h-screen bg-slate-100 flex items-center justify-center px-4">
    <div class="w-full max-w-sm bg-white rounded-xl shadow-sm border border-slate-100 p-8">
      <h1 class="text-xl font-bold text-slate-900 mb-6 text-center">Your Drive</h1>

      <!-- Tab switcher -->
      <div class="flex mb-6 border-b border-slate-200">
        <button
          class="flex-1 pb-2 text-sm font-semibold transition-colors"
          :class="mode === 'login' ? 'text-sky-600 border-b-2 border-sky-600' : 'text-slate-500 hover:text-slate-700'"
          @click="mode = 'login'">
          Sign In
        </button>
        <button
          class="flex-1 pb-2 text-sm font-semibold transition-colors"
          :class="mode === 'register' ? 'text-sky-600 border-b-2 border-sky-600' : 'text-slate-500 hover:text-slate-700'"
          @click="mode = 'register'">
          Register
        </button>
      </div>

      <!-- Error message -->
      <div v-if="auth.error" class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ auth.error }}
      </div>

      <!-- Login form -->
      <form v-if="mode === 'login'" @submit.prevent="handleLogin" class="space-y-4">
        <input
          v-model="loginForm.email"
          type="email"
          placeholder="Email"
          required
          class="w-full border border-slate-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        <input
          v-model="loginForm.password"
          type="password"
          placeholder="Password"
          required
          class="w-full border border-slate-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        <button
          type="submit"
          :disabled="auth.loading"
          class="w-full bg-sky-600 text-white text-sm font-semibold py-2 rounded-md hover:bg-sky-700 disabled:opacity-50 transition-colors">
          {{ auth.loading ? 'Signing in…' : 'Sign In' }}
        </button>
      </form>

      <!-- Register form -->
      <form v-else @submit.prevent="handleRegister" class="space-y-4">
        <input
          v-model="registerForm.name"
          type="text"
          placeholder="Full Name"
          required
          class="w-full border border-slate-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        <input
          v-model="registerForm.email"
          type="email"
          placeholder="Email"
          required
          class="w-full border border-slate-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        <input
          v-model="registerForm.password"
          type="password"
          placeholder="Password (min 8 characters)"
          required
          class="w-full border border-slate-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        <input
          v-model="registerForm.password_confirmation"
          type="password"
          placeholder="Confirm Password"
          required
          class="w-full border border-slate-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        <button
          type="submit"
          :disabled="auth.loading"
          class="w-full bg-sky-600 text-white text-sm font-semibold py-2 rounded-md hover:bg-sky-700 disabled:opacity-50 transition-colors">
          {{ auth.loading ? 'Creating account…' : 'Create Account' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()
const mode = ref('login')

const loginForm = ref({ email: '', password: '' })
const registerForm = ref({ name: '', email: '', password: '', password_confirmation: '' })

async function handleLogin() {
  try {
    const result = await auth.login(loginForm.value)
    if (result.is_admin) {
      window.location.href = '/nova/resources/users'
    } else {
      router.push('/dashboard')
    }
  } catch {
    // error is set in the store
  }
}

async function handleRegister() {
  try {
    const result = await auth.register(registerForm.value)
    if (result.is_admin) {
      window.location.href = '/nova/resources/users'
    } else {
      router.push('/dashboard')
    }
  } catch {
    // error is set in the store
  }
}
</script>
