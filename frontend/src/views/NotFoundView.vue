<template>
  <div class="min-h-screen bg-slate-100 flex items-center justify-center px-4">
    <div class="text-center">
      <h1 class="text-2xl font-bold text-slate-800 mb-2">Page Not Found</h1>
      <p class="text-slate-500 mb-6">The page you are looking for does not exist.</p>
      <RouterLink to="/dashboard" class="text-sky-600 hover:underline text-sm font-medium">
        Go to Dashboard
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>
