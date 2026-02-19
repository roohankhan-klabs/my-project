<div id="delete-folder" class="w-full max-w-sm rounded-xl bg-white p-5 shadow-sm border border-slate-100">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-slate-900">Delete folder</h2>
        <button
            type="button"
            onclick="window.dashboardModals.closeModal('delete-folder-modal')"
            class="text-slate-400 hover:text-slate-600 text-xs font-medium"
        >
            Close
        </button>
    </div>
    <form method="POST" action="{{ route('folders.delete' ) }}" class="space-y-4">
        @csrf
        @isset($currentFolder)
        <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
        @endisset
        <div>
            <div>
                Confirm delete
            </div>
            <!-- <label for="folder_name" class="block text-xs font-medium text-slate-700">Folder name</label>
            <input
                id="folder_name"
                name="name"
                type="text"
                required
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"> -->
        </div>
        <button
            type="submit"
            class="w-full rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
            Yes
        </button>
        <button
            type="button"
            onclick="window.dashboardModals.closeModal('delete-folder-modal')"
            class="w-full rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
            No
        </button>
    </form>
</div>