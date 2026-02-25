<script setup>
import { ref } from 'vue'

const emit = defineEmits(['files-selected'])
const isDragging = ref(false)
const fileInput = ref(null)

function onDrop(e) {
  isDragging.value = false
  const files = e.dataTransfer?.files
  if (files?.length) emit('files-selected', files)
}

function onFileInputChange() {
  if (fileInput.value?.files?.length) emit('files-selected', fileInput.value.files)
  fileInput.value.value = ''
}
</script>

<template>
  <div
    class="rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-8 flex flex-col items-center justify-center text-center transition-colors cursor-pointer"
    :class="isDragging ? 'border-sky-400 bg-sky-50' : 'hover:bg-slate-100 hover:border-slate-400'"
    @dragenter.prevent="isDragging = true"
    @dragleave.prevent="isDragging = false"
    @dragover.prevent
    @drop.prevent="onDrop"
    @click="fileInput.click()">
    <svg class="w-10 h-10 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
    </svg>
    <p class="text-sm font-medium text-slate-700">Click to select or drag and drop files here</p>
    <p class="text-xs text-slate-400 mt-1">Maximum 10 MB per file</p>
    <input ref="fileInput" type="file" multiple class="hidden" @change="onFileInputChange" />
  </div>
</template>
