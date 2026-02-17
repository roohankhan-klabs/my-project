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
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-700 focus:ring-offset-2"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-8 space-y-8">
            <div class="flex items-center justify-between gap-3">
                <div class="text-xs text-slate-600">
                    <a href="{{ route('dashboard') }}" class="font-medium text-slate-800 hover:text-sky-600">Root</a>
                    @isset($currentFolder)
                        <span class="mx-1 text-slate-400">/</span>
                        <span>{{ $currentFolder->name }}</span>
                    @endisset
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="window.dashboardModals.openModal('create-folder-modal')"
                        class="inline-flex items-center rounded-md bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                    >
                        Create folder
                    </button>
                    <button
                        type="button"
                        onclick="window.dashboardModals.openModal('upload-file-modal')"
                        class="inline-flex items-center rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                    >
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
                    <!-- <h2 class="text-sm font-semibold text-slate-900 mb-3">Folders</h2> -->
                    @if ($folders->isEmpty())
                        <p class="text-xs text-slate-500">No folders yet.</p>
                    @else
                        <ul class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm text-slate-800">
                            @foreach ($folders as $folder)
                                <li>
                                    <a
                                        href="{{ route('dashboard', ['folder' => $folder->id]) }}"
                                        class="flex flex-col items-center justify-center rounded-lg border border-slate-100 bg-slate-50 px-3 py-3 hover:border-sky-300 hover:bg-sky-50"
                                    >
                                        <img
                                            src="{{ asset('icons/folder.png') }}"
                                            alt="Folder"
                                            class="mb-2 h-10 w-10 object-contain"
                                        >
                                        <span class="truncate text-xs text-slate-800" title="{{ $folder->name }}">
                                            {{ $folder->name }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div>
                    <!-- <h2 class="text-sm font-semibold text-slate-900 mb-3">
                        {{ isset($currentFolder) ? 'Files in this folder' : 'Files in root' }}
                    </h2> -->
                    @if ($files->isEmpty())
                        <p class="text-xs text-slate-500">No files uploaded yet.</p>
                    @else
                        <ul class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm text-slate-800">
                            @foreach ($files as $file)
                                <li class="flex flex-col rounded-lg border border-slate-100 bg-slate-50 p-2">
                                    <div class="mb-2 flex-1 flex items-center justify-center overflow-hidden rounded-md bg-white">
                                        @if (str_starts_with($file->mime_type, 'image/'))
                                            <img
                                                src="{{ asset('storage/' . $file->path) }}"
                                                alt="{{ $file->name }}"
                                                class="max-h-24 w-full object-contain"
                                            >
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
            class="fixed inset-0 z-50 hidden bg-slate-900/40 flex items-center justify-center px-4"
        >
            @include('popups.create-folder')
        </div>

        <div
            id="upload-file-modal"
            class="fixed inset-0 z-50 hidden bg-slate-900/40 flex items-center justify-center px-4"
        >
            @include('popups.upload-file')
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
        </script>
    </body>
</html>
