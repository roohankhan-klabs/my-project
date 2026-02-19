<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100">
    <header class="bg-white shadow-sm">
        <div class="mx-auto max-w-5xl flex items-center justify-between px-4 py-3">
            <h1 class="text-lg font-semibold text-slate-900">Your Drive</h1>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-700 focus:ring-offset-2">
                        Logout
                    </button>
                </form>
                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center rounded-md bg-purple-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
                    Nova
                </a>
            </div>

        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-8 space-y-8">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('dashboard') }}" class="text-xs font-medium text-slate-800 hover:text-sky-600 shrink-0">Root</a>
                @isset($currentFolder)
                <span class="text-slate-400 shrink-0">/</span>
                <div class="min-w-0 flex items-center">
                    <span
                        id="folder-name-heading"
                        role="button"
                        tabindex="0"
                        class="text-lg font-semibold text-slate-900 truncate cursor-pointer hover:text-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-1 rounded px-1 -mx-1"
                        title="Click to rename"
                        data-folder-id="{{ $currentFolder->id }}">{{ $currentFolder->name }}</span>
                    <form
                        id="folder-name-form"
                        method="POST"
                        action="{{ route('folders.update', $currentFolder) }}"
                        class="hidden flex items-center gap-1 shrink min-w-0">
                        @csrf
                        @method('PATCH')
                        <input
                            type="text"
                            name="name"
                            value="{{ $currentFolder->name }}"
                            class="text-lg font-semibold text-slate-900 border border-slate-300 rounded px-2 py-0.5 w-full min-w-0 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                            maxlength="255"
                            required>
                    </form>
                </div>
                @endisset
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick="window.dashboardModals.openModal('create-folder-modal')"
                    class="inline-flex items-center rounded-md bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                    Create folder
                </button>
                <button
                    type="button"
                    onclick="window.dashboardModals.openModal('upload-file-modal')"
                    class="inline-flex items-center rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    Upload file
                </button>
            </div>
        </div>

        @if (session('success'))
        <div class="rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <section class="rounded-xl bg-white p-5 shadow-sm border border-slate-100 space-y-6">
            <div>
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Folders</h2>
                @if ($folders->isEmpty())
                <p class="text-xs text-slate-500">No folders yet.</p>
                @else
                <ul class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm text-slate-800">
                    @foreach ($folders as $folder)
                    <li class="relative group">
                        <a
                            href="{{ route('dashboard', ['folder' => $folder->id]) }}"
                            class="flex flex-col items-center justify-center rounded-lg border border-slate-100 bg-slate-50 px-3 py-3 hover:border-sky-300 hover:bg-sky-50">
                            <img
                                src="{{ asset('icons/folder.png') }}"
                                alt="Folder"
                                class="mb-2 h-10 w-10 object-contain">
                            <span class="truncate text-xs text-slate-800" title="{{ $folder->name }}">
                                {{ $folder->name }}
                            </span>
                        </a>
                        <form
                            method="POST"
                            action="{{ route('folders.delete') }}"
                            class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity"
                            onsubmit="return confirm('Delete folder and everything inside it?');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="folder_id" value="{{ $folder->id }}">
                            <button
                                type="submit"
                                class="rounded p-1 bg-red-100 text-red-600 hover:bg-red-200 text-xs font-medium"
                                title="Delete folder">
                                Delete
                            </button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>

            <div>
                <h2 class="text-sm font-semibold text-slate-900 mb-3">
                    Files
                </h2>
                @if ($files->isEmpty())
                <p class="text-xs text-slate-500">No files uploaded yet.</p>
                @else
                <ul class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm text-slate-800">
                    @foreach ($files as $file)
                    <li class="relative group flex flex-col rounded-lg border border-slate-100 bg-slate-50 p-2">
                        <div class="mb-2 flex-1 flex items-center justify-center overflow-hidden rounded-md bg-white">
                            @if (str_starts_with($file->mime_type, 'image/'))
                            <img
                                src="{{ asset('storage/' . $file->path) }}"
                                alt="{{ $file->name }}"
                                class="max-h-24 w-full object-contain">
                            @else
                            <span class="text-[10px] text-slate-400">No preview</span>
                            @endif
                        </div>
                        <div class="truncate text-xs text-slate-700" title="{{ $file->name }}">
                            {{ $file->name }}
                        </div>
                        <div class="mt-1 text-[10px] uppercase tracking-wide text-slate-400">
                            {{ number_format($file->getSizeInMB(), 2) }} MB
                        </div>
                        <form
                            method="POST"
                            action="{{ route('files.destroy') }}"
                            class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity"
                            onsubmit="return confirm('Delete file?');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="file_id" value="{{ $file->id }}">
                            <button
                                type="submit"
                                class="rounded p-1 bg-red-100 text-red-600 hover:bg-red-200 text-xs font-medium"
                                title="Delete file">
                                Delete
                            </button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </section>
    </main>

    <!-- Modals -->
    <div
        id="create-folder-modal"
        class="fixed inset-0 z-50 hidden bg-slate-900/40 flex items-center justify-center px-4">
        @include('popups.create-folder')
    </div>

    <div
        id="upload-file-modal"
        class="fixed inset-0 z-50 hidden bg-slate-900/40 flex items-center justify-center px-4">
        @include('popups.upload-file')
    </div>

    <div
        id="delete-folder-modal"
        class="fixed inset-0 z-50 hidden bg-slate-900/40 flex items-center justify-center px-4">
        @include('popups.delete-folder')
    </div>

    <script>
        window.dashboardModals = {
            openModal(id) {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.remove('hidden');
                }
            },
            closeModal(id) {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.add('hidden');
                }
            },
        };

        (function() {
            const heading = document.getElementById('folder-name-heading');
            const form = document.getElementById('folder-name-form');
            const input = form && form.querySelector('input[name="name"]');
            if (!heading || !form || !input) return;

            function showEdit() {
                heading.classList.add('hidden');
                form.classList.remove('hidden');
                input.focus();
                input.select();
            }

            function showHeading() {
                form.classList.add('hidden');
                heading.classList.remove('hidden');
                heading.textContent = input.value.trim() || input.value;
            }

            function save() {
                const name = input.value.trim();
                if (name) {
                    form.submit();
                } else {
                    input.value = heading.textContent;
                    showHeading();
                }
            }

            heading.addEventListener('click', showEdit);
            heading.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    showEdit();
                }
            });

            input.addEventListener('blur', save);
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    save();
                }
                if (e.key === 'Escape') {
                    input.value = heading.textContent;
                    showHeading();
                }
            });
        })();
    </script>
</body>

</html>