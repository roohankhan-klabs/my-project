<template>
  <div class="fixed inset-0 z-50 bg-slate-900/40 flex items-center justify-center px-4">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
      <h3 class="text-sm font-semibold text-slate-900 mb-2">
        Delete {{ itemType === 'folder' ? 'Folder' : 'File' }}?
      </h3>
      <p class="text-xs text-slate-500 mb-6">
        "<span class="font-medium text-slate-700">{{ itemName }}</span>" will be permanently deleted
        <template v-if="itemType === 'folder'">along with everything inside it</template>.
        This cannot be undone.
      </p>
      <div class="flex justify-end gap-2">
        <button
          @click="emit('cancel')"
          class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-800 transition-colors">
          Cancel
        </button>
        <button
          @click="emit('confirm')"
          class="px-3 py-1.5 text-xs font-semibold bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
          Delete
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  itemName: { type: String, default: '' },
  itemType: { type: String, default: 'item' },
})
defineEmits(['confirm', 'cancel'])
</script>
