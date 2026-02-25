<template>
  <li
    class="relative group flex flex-col rounded-lg border border-slate-100 bg-slate-50 p-2 cursor-grab active:cursor-grabbing"
    draggable="true"
    @dragstart="onDragStart">
    <!-- Preview -->
    <div class="mb-2 flex-1 flex items-center justify-center overflow-hidden rounded-md bg-white min-h-24">
      <img
        v-if="file.mime_type?.startsWith('image/')"
        :src="`/storage/${file.path}`"
        :alt="file.name"
        class="max-h-24 w-full object-contain" />
      <span v-else class="text-[10px] text-slate-400 px-2 text-center">{{ file.mime_type ?? 'File' }}</span>
    </div>

    <div class="truncate text-xs text-slate-700 font-medium" :title="file.name">{{ file.name }}</div>
    <div class="mt-0.5 text-[10px] uppercase tracking-wide text-slate-400">{{ file.size_in_kb }}</div>

    <!-- Actions -->
    <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
      <button
        @click.stop="emit('download', file)"
        class="rounded p-0.5 bg-sky-100 text-sky-600 hover:bg-sky-200"
        title="Download">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
      </button>
      <button
        @click.stop="emit('delete', file.id)"
        class="rounded p-0.5 bg-red-100 text-red-600 hover:bg-red-200"
        title="Delete">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </li>
</template>

<script setup>
const props = defineProps({ file: Object })
const emit = defineEmits(['delete', 'download'])

function onDragStart(e) {
  e.dataTransfer.setData('text/plain', JSON.stringify({ type: 'file', id: props.file.id }))
  e.dataTransfer.effectAllowed = 'move'
}
</script>
