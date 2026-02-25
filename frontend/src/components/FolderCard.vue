<template>
  <li
    class="relative group cursor-grab active:cursor-grabbing"
    draggable="true"
    @dragstart="onDragStart"
    @dragover.prevent
    @drop.prevent="onDrop">
    <a
      @click.prevent="emit('navigate', folder.id)"
      href="#"
      class="flex flex-col items-center justify-center rounded-lg border border-slate-100 bg-slate-50 px-3 py-4 hover:border-sky-300 hover:bg-sky-50 transition-colors">
      <!-- Folder icon -->
      <svg class="w-10 h-10 text-sky-400 mb-2" fill="currentColor" viewBox="0 0 24 24">
        <path d="M10 4H4c-1.11 0-2 .89-2 2v12a2 2 0 002 2h16a2 2 0 002-2V8c0-1.11-.89-2-2-2h-8l-2-2z"/>
      </svg>

      <span
        v-if="!renaming"
        class="truncate text-xs text-slate-800 w-full text-center"
        @dblclick.stop="startRename">
        {{ folder.name }}
      </span>
      <input
        v-else
        ref="renameInput"
        v-model="newName"
        class="text-xs border border-sky-400 rounded px-1 w-full text-center focus:outline-none"
        @blur="commitRename"
        @keydown.enter.prevent="commitRename"
        @keydown.escape.prevent="cancelRename"
        @click.stop />
    </a>

    <button
      @click.stop="emit('delete', folder.id)"
      class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity rounded p-0.5 bg-red-100 text-red-600 hover:bg-red-200">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </li>
</template>

<script setup>
import { ref, nextTick } from 'vue'

const props = defineProps({ folder: Object })
const emit = defineEmits(['navigate', 'delete', 'rename', 'drop-file', 'drop-folder'])

const renaming = ref(false)
const newName = ref('')
const renameInput = ref(null)

function startRename() {
  newName.value = props.folder.name
  renaming.value = true
  nextTick(() => renameInput.value?.focus())
}

function commitRename() {
  if (newName.value.trim() && newName.value.trim() !== props.folder.name) {
    emit('rename', props.folder.id, newName.value.trim())
  }
  renaming.value = false
}

function cancelRename() {
  renaming.value = false
}

function onDragStart(e) {
  e.dataTransfer.setData('text/plain', JSON.stringify({ type: 'folder', id: props.folder.id }))
  e.dataTransfer.effectAllowed = 'move'
}

function onDrop(e) {
  const raw = e.dataTransfer.getData('text/plain')
  if (!raw) return
  try {
    const data = JSON.parse(raw)
    if (data.type === 'file') emit('drop-file', data.id, props.folder.id)
    else if (data.type === 'folder' && data.id !== props.folder.id) emit('drop-folder', data.id, props.folder.id)
  } catch {}
}
</script>
