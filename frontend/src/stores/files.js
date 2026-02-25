import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/axios'

export const useFilesStore = defineStore('files', () => {
  const folders = ref([])
  const files = ref([])
  const currentFolder = ref(null)
  const breadcrumbPath = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchDashboard(folderId = null) {
    loading.value = true
    error.value = null
    try {
      const params = folderId ? { folder: folderId } : {}
      const { data } = await api.get('/dashboard', { params })
      folders.value = data.folders
      files.value = data.files
      currentFolder.value = data.current_folder
      breadcrumbPath.value = data.breadcrumb_path
    } catch {
      error.value = 'Failed to load dashboard.'
    } finally {
      loading.value = false
    }
  }

  async function createFolder(name, parentId = null) {
    const { data } = await api.post('/folders', { name, parent_id: parentId })
    folders.value.push(data.folder)
    return data.folder
  }

  async function renameFolder(folderId, name) {
    const { data } = await api.patch(`/folders/${folderId}`, { name })
    const idx = folders.value.findIndex(f => f.id === folderId)
    if (idx !== -1) folders.value[idx].name = name
    if (currentFolder.value?.id === folderId) currentFolder.value.name = name
    return data.folder
  }

  async function deleteFolder(folderId) {
    await api.delete('/folders', { data: { folder_id: folderId } })
    folders.value = folders.value.filter(f => f.id !== folderId)
  }

  async function moveFolder(folderId, parentId) {
    await api.patch('/folders/move', { folder_id: folderId, parent_id: parentId })
    folders.value = folders.value.filter(f => f.id !== folderId)
  }

  async function uploadFiles(fileList, folderId = null) {
    const form = new FormData()
    Array.from(fileList).forEach(f => form.append('file[]', f))
    if (folderId) form.append('folder_id', folderId)
    const { data } = await api.post('/files', form, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    files.value.push(...data.files)
    return data.files
  }

  async function deleteFile(fileId) {
    await api.delete('/files', { data: { file_id: fileId } })
    files.value = files.value.filter(f => f.id !== fileId)
  }

  async function moveFile(fileId, folderId) {
    await api.patch('/files/move', { file_id: fileId, folder_id: folderId })
    files.value = files.value.filter(f => f.id !== fileId)
  }

  async function downloadFile(file) {
    const response = await api.get(`/files/${file.id}/download`, {
      responseType: 'blob',
    })
    const url = URL.createObjectURL(response.data)
    const a = document.createElement('a')
    a.href = url
    a.download = file.name
    a.click()
    URL.revokeObjectURL(url)
  }

  return {
    folders, files, currentFolder, breadcrumbPath, loading, error,
    fetchDashboard, createFolder, renameFolder, deleteFolder, moveFolder,
    uploadFiles, deleteFile, moveFile, downloadFile,
  }
})
