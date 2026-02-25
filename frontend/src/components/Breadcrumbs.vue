<template>
  <nav class="flex items-center gap-2 min-w-0 flex-wrap">
    <button
      @click="emit('navigate', null)"
      class="text-xs font-medium text-slate-800 hover:text-sky-600 shrink-0 transition-colors">
      Root
    </button>
    <template v-for="(crumb, idx) in path" :key="crumb.id">
      <span class="text-slate-400 shrink-0">/</span>
      <button
        v-if="idx < path.length - 1"
        @click="emit('navigate', crumb.id)"
        class="text-xs font-medium text-slate-800 hover:text-sky-600 shrink-0 transition-colors">
        {{ crumb.name }}
      </button>
      <span v-else class="text-sm font-semibold text-slate-900 truncate">{{ crumb.name }}</span>
    </template>
  </nav>
</template>

<script setup>
defineProps({ path: { type: Array, default: () => [] } })
defineEmits(['navigate'])
</script>
