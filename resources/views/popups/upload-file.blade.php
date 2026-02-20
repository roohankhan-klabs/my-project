<div id="upload-file" class="w-full max-w-sm rounded-xl bg-white p-5 shadow-sm border border-slate-100">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-slate-900">Upload files</h2>
        <button
            type="button"
            onclick="window.dashboardModals.closeModal('upload-file-modal')"
            class="text-slate-400 hover:text-slate-600 text-xs font-medium"
        >
            Close
        </button>
    </div>
    <form
        method="POST"
        action="{{ route('files.store') }}"
        enctype="multipart/form-data"
        class="space-y-4">
        @csrf
        <div>
            <label for="file" class="block text-xs font-medium text-slate-700">Files</label>
            <input
                id="file"
                name="file[]"
                type="file"
                multiple
                required
                class="mt-1 block w-full text-sm text-slate-900 file:mr-4 file:rounded-md file:border-0 file:bg-slate-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-slate-900"
            >
        </div>
        @isset($currentFolder)
        <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
        @elseif ($folders->count())
        <div>
            <label for="folder_id" class="block text-xs font-medium text-slate-700">Folder (optional)</label>
            <select
                id="folder_id"
                name="folder_id"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                <option value="">No folder (root)</option>
                @foreach ($folders as $folder)
                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                @endforeach
            </select>
        </div>
        @endisset
        <button
            type="submit"
            class="w-full rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
            Upload files
        </button>
    </form>
</div>