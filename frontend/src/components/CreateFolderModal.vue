<script setup>
import { ref } from 'vue'

const emit = defineEmits(['create', 'close'])
const name = ref('')

function submit() {
  if (name.value.trim()) {
    emit('create', name.value.trim())
    name.value = ''
  }
}
</script>

<template>
  <div class="fixed inset-0 z-50 bg-slate-900/40 flex items-center justify-center px-4">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
      <h3 class="text-sm font-semibold text-slate-900 mb-4">Create New Folder</h3>
      <form @submit.prevent="submit">
        <input
          v-model="name"
          type="text"
          placeholder="Folder name"
          maxlength="255"
          required
          autofocus
          class="w-full border border-slate-200 rounded-md px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-sky-500" />
        <div class="flex justify-end gap-2">
          <button
            type="button"
            @click="emit('close')"
            class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800 transition-colors">
            Cancel
          </button>
          <button
            type="submit"
            class="px-3 py-1.5 text-xs font-semibold bg-sky-600 text-white rounded-md hover:bg-sky-700 transition-colors">
            Create
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
