<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100">
    <header class="bg-white shadow-sm">
        <div class="mx-auto max-w-5xl flex items-center justify-between px-4 py-3">
            <h1 class="text-lg font-semibold text-slate-900">Your Drive</h1>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('userLogout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-700 focus:ring-offset-2">
                        Logout
                    </button>
                </form>
                <!-- <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center rounded-md bg-purple-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
                    Nova
                </a> -->
            </div>

        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-8 space-y-8">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('dashboard') }}" class="text-xs font-medium text-slate-800 hover:text-sky-600 shrink-0">Root</a>
                
                @foreach($breadcrumbPath as $folder)
                    @if (!$loop->last)
                        <span class="text-slate-400 shrink-0">/</span>
                        <a href="{{ route('dashboard', ['folder' => $folder->id]) }}" 
                           class="text-xs font-medium text-slate-800 hover:text-sky-600 shrink-0">
                            {{ $folder->name }}
                        </a>
                    @endif
                @endforeach
                
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
            </div>
        </div>

        <div 
            class="rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-8 flex flex-col items-center justify-center text-center hover:bg-slate-100 hover:border-slate-400 transition-colors cursor-pointer"
            onclick="document.getElementById('drag-drop-input').click()">
            <svg class="w-10 h-10 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <p class="text-sm font-medium text-slate-700">Click to select or drag and drop files here</p>
            <p class="text-xs text-slate-400 mt-1">Maximum file size 10MB per file</p>
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
                
                @isset($currentFolder)
                <div class="mb-4">
                    <a href="{{ $currentFolder->parent_id ? route('dashboard', ['folder' => $currentFolder->parent_id]) : route('dashboard') }}"
                       data-folder-dropzone="{{ $currentFolder->parent_id ?? 'root' }}"
                       class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 hover:border-sky-300 hover:bg-sky-50 transition-colors dropdown-target text-slate-600 hover:text-sky-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        <span class="text-sm font-medium">Move up to {{ $currentFolder->parent_id ? $currentFolder->parent->name . ' Folder' : 'root' }}</span>
                    </a>
                </div>
                @endisset
                @if ($folders->isEmpty())
                <p class="text-xs text-slate-500">No folders yet.</p>
                @else
                <ul class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm text-slate-800">
                    @foreach ($folders as $folder)
                    <li class="relative group cursor-grab active:cursor-grabbing"
                        data-folder-id-drag="{{ $folder->id }}"
                        draggable="true">
                        <a
                            href="{{ route('dashboard', ['folder' => $folder->id]) }}"
                            data-folder-dropzone="{{ $folder->id }}"
                            class="flex flex-col items-center justify-center rounded-lg border border-slate-100 bg-slate-50 px-3 py-3 hover:border-sky-300 hover:bg-sky-50 transition-colors dropdown-target">
                            <img
                                src="{{ asset('icons/folder.png') }}"
                                alt="Folder"
                                draggable="false"
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
                    <li class="relative group flex flex-col rounded-lg border border-slate-100 bg-slate-50 p-2 cursor-grab active:cursor-grabbing"
                        draggable="true" 
                        data-file-id="{{ $file->id }}">
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
                            {{ $file->size_in_kb }} KB
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
        id="delete-folder-modal"
        class="fixed inset-0 z-50 hidden bg-slate-900/40 flex items-center justify-center px-4">
        @include('popups.delete-folder')
    </div>

    <!-- Hidden Forms -->

    <form id="move-file-form" method="POST" action="{{ route('files.move') }}" class="hidden">
        @csrf
        @method('PATCH')
        <input type="hidden" name="file_id" id="move-file-id">
        <input type="hidden" name="folder_id" id="move-folder-id">
    </form>

    <form id="move-folder-form" method="POST" action="{{ route('folders.move') }}" class="hidden">
        @csrf
        @method('PATCH')
        <input type="hidden" name="folder_id" id="move-folder-form-id">
        <input type="hidden" name="parent_id" id="move-folder-parent-id">
    </form>

    <form id="drag-drop-form" method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="file" id="drag-drop-input" name="file[]" multiple>
        @isset($currentFolder)
            <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
        @endisset
    </form>

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

        // logic to update folder's name
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

        // drag and drop functionality for files and folders
        (function() {
            const form = document.getElementById('drag-drop-form');
            const fileInput = document.getElementById('drag-drop-input');
            const moveForm = document.getElementById('move-file-form');
            const moveFileInput = document.getElementById('move-file-id');
            const moveFolderInput = document.getElementById('move-folder-id');
            
            const moveFolderForm = document.getElementById('move-folder-form');
            const moveFolderFormIdInput = document.getElementById('move-folder-form-id');
            const moveFolderParentIdInput = document.getElementById('move-folder-parent-id');

            const generalDropzone = document.querySelector('.border-dashed');
            const defaultFolderId = form.querySelector('input[name="folder_id"]')?.value || null;

            if (!form || !fileInput || !moveForm || !moveFolderForm) return;

            // Handle internal file drags
            document.querySelectorAll('[data-file-id]').forEach(fileItem => {
                fileItem.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('text/plain', JSON.stringify({
                        type: 'internal_file',
                        id: fileItem.dataset.fileId
                    }));
                    e.dataTransfer.effectAllowed = 'move';
                });
            });

            // Handle internal folder drags
            document.querySelectorAll('[data-folder-id-drag]').forEach(folderItem => {
                folderItem.addEventListener('dragstart', (e) => {
                    const folderId = folderItem.dataset.folderIdDrag;
                    console.log(`[FOLDER DRAG START] Started dragging folder with ID: ${folderId}`);
                    
                    e.dataTransfer.setData('text/plain', JSON.stringify({
                        type: 'internal_folder',
                        id: folderId
                    }));
                    e.dataTransfer.effectAllowed = 'move';
                });
            });

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    form.submit();
                }
            });

            // Helper to check if drag contains real external files OR our internal file reference
            function hasExternalFiles(e) {
                if (!e.dataTransfer) return false;
                
                // Check if it has internal custom type
                const hasInternalFile = Array.from(e.dataTransfer.types).includes('text/plain');
                if (hasInternalFile) {
                    return true;
                }

                if (e.dataTransfer.items) {
                    for (let i = 0; i < e.dataTransfer.items.length; i++) {
                        if (e.dataTransfer.items[i].kind === 'file') {
                            return true;
                        }
                    }
                }
                
                return e.dataTransfer.types.includes('Files');
            }

            // Setup a dropzone element (general or specific folder)
            function setupDropzone(element, dropzoneFolderId) {
                if (!element) return;

                let dragCounter = 0;

                element.addEventListener('dragenter', (e) => {
                    e.preventDefault();
                    if (!hasExternalFiles(e)) return;
                    
                    dragCounter++;
                    element.classList.add('border-sky-400', 'bg-sky-50');
                    if (element.classList.contains('border-slate-300')) {
                        element.classList.remove('border-slate-300');
                    }
                });

                element.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    if (!hasExternalFiles(e)) return;
                    
                    dragCounter--;
                    if (dragCounter === 0) {
                        element.classList.remove('border-sky-400', 'bg-sky-50');
                        if (!element.classList.contains('dropdown-target')) {
                            element.classList.add('border-slate-300');
                        }
                    }
                });

                element.addEventListener('dragover', (e) => {
                    if (!hasExternalFiles(e)) return;
                    e.preventDefault(); // Required to allow dropping
                });

                element.addEventListener('drop', (e) => {
                    if (!hasExternalFiles(e)) return;
                    
                    e.preventDefault();
                    dragCounter = 0;
                    element.classList.remove('border-sky-400', 'bg-sky-50');
                    if (!element.classList.contains('dropdown-target')) {
                        element.classList.add('border-slate-300');
                    }

                    // Check for internal file/folder move FIRST
                    let isInternalMove = false;
                    const dataString = e.dataTransfer.getData('text/plain');
                    if (dataString) {
                        try {
                            const data = JSON.parse(dataString);
                            if (data.type === 'internal_file' && data.id) {
                                isInternalMove = true;
                                moveFileInput.value = data.id;
                                
                                if (dropzoneFolderId === 'root') {
                                    moveFolderInput.value = ''; // empty string means root
                                } else if (dropzoneFolderId) {
                                    moveFolderInput.value = dropzoneFolderId;
                                } else if (defaultFolderId) {
                                    moveFolderInput.value = defaultFolderId;
                                } else {
                                    moveFolderInput.value = ''; // empty string means root
                                }

                                const currentFolderStr = defaultFolderId ? String(defaultFolderId) : 'root';
                                const targetFolderStr = moveFolderInput && moveFolderInput.value ? String(moveFolderInput.value) : 'root';
                              
                                // Prevent moving file into its current directory
                                if (currentFolderStr !== targetFolderStr || dropzoneFolderId) {
                                     moveForm.submit();
                                }
                            } else if (data.type === 'internal_folder' && data.id) {
                                isInternalMove = true;
                                moveFolderFormIdInput.value = data.id;

                                if (dropzoneFolderId === 'root') {
                                    moveFolderParentIdInput.value = ''; // empty string means root
                                } else if (dropzoneFolderId) {
                                    moveFolderParentIdInput.value = dropzoneFolderId;
                                } else if (defaultFolderId) {
                                    moveFolderParentIdInput.value = defaultFolderId;
                                } else {
                                    moveFolderParentIdInput.value = ''; // empty string means root
                                }

                                const targetFolderStr = moveFolderParentIdInput && moveFolderParentIdInput.value ? String(moveFolderParentIdInput.value) : 'root';
                                
                                // Prevent moving a folder into itself
                                if (String(data.id) !== targetFolderStr) {
                                    moveFolderForm.submit();
                                } else {
                                    console.log(`[FOLDER MOVE BLOCKED] Cannot move folder into itself. Folder ID: ${data.id}, Target: ${targetFolderStr}`);
                                }
                                return; // Exit early after processing folder move
                            }
                        } catch (err) {
                            // Not valid JSON, ignore
                        }
                    }

                    // If it wasn't an internal move, handle as external file upload
                    if (!isInternalMove && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                        let folderInput = form.querySelector('input[name="folder_id"]');
                        
                        if (dropzoneFolderId === 'root') {
                            if (folderInput) folderInput.remove(); // root
                        } else if (dropzoneFolderId) {
                            if (!folderInput) {
                                folderInput = document.createElement('input');
                                folderInput.type = 'hidden';
                                folderInput.name = 'folder_id';
                                form.appendChild(folderInput);
                            }
                            folderInput.value = dropzoneFolderId;
                        } else if (defaultFolderId) {
                            if (folderInput) folderInput.value = defaultFolderId;
                        } else {
                            if (folderInput) folderInput.remove();
                        }

                        fileInput.files = e.dataTransfer.files;
                        form.submit();
                    }
                });
            }

            // Setup general page dropzone (the dashed box)
            setupDropzone(generalDropzone, null);

            // Setup specific folder dropzones
            document.querySelectorAll('[data-folder-dropzone]').forEach(folderItem => {
                setupDropzone(folderItem, folderItem.dataset.folderDropzone);
            });

        })();
    </script>
</body>

</html>