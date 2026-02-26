<template>
    <div class="min-h-screen bg-slate-100">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-5xl flex items-center justify-between px-4 py-3">
                <h1 class="text-lg font-semibold text-slate-900">{{ auth.user.name }}'s Drive</h1>
                <button @click="handleLogout"
                    class="inline-flex items-center rounded-md bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-900 transition-colors">
                    Logout
                </button>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-8 space-y-6">

            <!-- Breadcrumbs + Create Folder -->
            <div class="flex items-center justify-between gap-3">
                <Breadcrumbs :path="filesStore.breadcrumbPath" @navigate="navigateTo" />
                <button @click="showCreateFolderModal = true"
                    class="inline-flex items-center gap-1.5 rounded-md bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-700 transition-colors shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Folder
                </button>
            </div>

            <!-- Upload zone -->
            <DragDropUpload @files-selected="onFilesSelected" />

            <!-- Toast notification -->
            <Transition name="fade">
                <div v-if="toast"
                    :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700'"
                    class="rounded-md border px-4 py-3 text-sm font-medium">
                    {{ toast.message }}
                </div>
            </Transition>

            <!-- Loading state -->
            <div v-if="filesStore.loading" class="text-center py-12 text-slate-500 text-sm">
                Loading…
            </div>

            <!-- Content -->
            <template v-else>
                <section class="rounded-xl bg-white p-5 shadow-sm border border-slate-100 space-y-6">

                    <!-- Folders section -->
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900 mb-3">Folders</h2>

                        <!-- Back link when inside a folder -->
                        <div v-if="filesStore.currentFolder" class="mb-4">
                            <button @click="navigateTo(filesStore.currentFolder.parent_id)" @dragover.prevent
                                @drop.prevent="onDropOnParent"
                                class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 hover:border-sky-300 hover:bg-sky-50 transition-colors text-slate-600 hover:text-sky-700 w-full text-left">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                </svg>
                                <span class="text-sm font-medium">
                                    Up to {{ filesStore.currentFolder.parent_id ? 'parent folder' : 'root' }}
                                </span>
                            </button>
                        </div>

                        <p v-if="filesStore.folders.length === 0" class="text-xs text-slate-500 italic">
                            No folders here yet.
                        </p>
                        <ul v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            <FolderCard v-for="folder in filesStore.folders" :key="folder.id" :folder="folder"
                                @navigate="navigateTo" @delete="confirmDeleteFolder" @rename="handleRenameFolder"
                                @drop-file="handleMoveFile" @drop-folder="handleMoveFolder" />
                        </ul>
                    </div>

                    <!-- Files section -->
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900 mb-3">Files</h2>
                        <p v-if="filesStore.files.length === 0" class="text-xs text-slate-500 italic">
                            No files here yet. Upload files using the area above.
                        </p>
                        <ul v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            <FileCard v-for="file in filesStore.files" :key="file.id" :file="file"
                                @delete="confirmDeleteFile" @download="filesStore.downloadFile($event)" />
                        </ul>
                    </div>

                </section>
            </template>
        </main>

        <!-- Modals -->
        <CreateFolderModal v-if="showCreateFolderModal" @create="handleCreateFolder"
            @close="showCreateFolderModal = false" />

        <DeleteConfirmModal v-if="pendingDelete" :item-name="pendingDelete.name" :item-type="pendingDelete.type"
            @confirm="executePendingDelete" @cancel="pendingDelete = null" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useFilesStore } from '@/stores/files'
import Breadcrumbs from '@/components/Breadcrumbs.vue'
import DragDropUpload from '@/components/DragDropUpload.vue'
import FolderCard from '@/components/FolderCard.vue'
import FileCard from '@/components/FileCard.vue'
import CreateFolderModal from '@/components/CreateFolderModal.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

const auth = useAuthStore()
const filesStore = useFilesStore()
const router = useRouter()
const route = useRoute()

const showCreateFolderModal = ref(false)
const pendingDelete = ref(null)
const toast = ref(null)

function showToast(type, message) {
    toast.value = { type, message }
    setTimeout(() => { toast.value = null }, 3000)
}

onMounted(() => {
    const folderId = route.query.folder ? Number(route.query.folder) : null
    filesStore.fetchDashboard(folderId)
})

async function navigateTo(folderId) {
    if (folderId) {
        await router.push({ name: 'dashboard', query: { folder: folderId } })
    } else {
        await router.push({ name: 'dashboard' })
    }
    await filesStore.fetchDashboard(folderId)
}

async function handleLogout() {
    try {
        await auth.logout()
    } catch {
        // ignore — local state is already cleared by the store
    }
    router.push('/login')
}

async function handleCreateFolder(name) {
    try {
        await filesStore.createFolder(name, filesStore.currentFolder?.id ?? null)
        showCreateFolderModal.value = false
        showToast('success', 'Folder created.')
    } catch {
        showToast('error', 'Could not create folder.')
    }
}

async function handleRenameFolder(folderId, name) {
    try {
        await filesStore.renameFolder(folderId, name)
        showToast('success', 'Folder renamed.')
    } catch {
        showToast('error', 'Could not rename folder.')
    }
}

function confirmDeleteFolder(folderId) {
    const folder = filesStore.folders.find(f => f.id === folderId)
    pendingDelete.value = { type: 'folder', id: folderId, name: folder?.name ?? 'folder' }
}

function confirmDeleteFile(fileId) {
    const file = filesStore.files.find(f => f.id === fileId)
    pendingDelete.value = { type: 'file', id: fileId, name: file?.name ?? 'file' }
}

async function executePendingDelete() {
    const { type, id } = pendingDelete.value
    pendingDelete.value = null
    try {
        if (type === 'folder') await filesStore.deleteFolder(id)
        else await filesStore.deleteFile(id)
        showToast('success', `${type === 'folder' ? 'Folder' : 'File'} deleted.`)
    } catch {
        showToast('error', 'Could not delete item.')
    }
}

async function onFilesSelected(fileList) {
    try {
        await filesStore.uploadFiles(fileList, filesStore.currentFolder?.id ?? null)
        showToast('success', `${fileList.length > 1 ? 'Files' : 'File'} uploaded successfully.`)
    } catch {
        showToast('error', 'Upload failed. Check file sizes (max 10 MB each).')
    }
}

async function handleMoveFile(fileId, targetFolderId) {
    try {
        const resolvedFolderId = targetFolderId != null ? Number(targetFolderId) : null
        await filesStore.moveFile(Number(fileId), resolvedFolderId)
        showToast('success', 'File moved.')
    } catch {
        showToast('error', 'Could not move file.')
    }
}

async function handleMoveFolder(folderId, parentFolderId) {
    try {
        const resolvedParentId = parentFolderId != null ? Number(parentFolderId) : null
        await filesStore.moveFolder(Number(folderId), resolvedParentId)
        showToast('success', 'Folder moved.')
    } catch (err) {
        const msg = err.response?.data?.message ?? 'Could not move folder.'
        showToast('error', msg)
    }
}

async function onDropOnParent(e) {
    const raw = e.dataTransfer.getData('text/plain')
    if (!raw) return
    try {
        const data = JSON.parse(raw)
        const parentId = filesStore.currentFolder?.parent_id ?? null
        if (data.type === 'file') await handleMoveFile(data.id, parentId)
        else if (data.type === 'folder') await handleMoveFolder(data.id, parentId)
    } catch { }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
