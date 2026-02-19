<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        Folder::create([
            'user_id' => $user->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Folder created successfully.');
    }

    public function update(Request $request, Folder $folder)
    {
        if ($folder->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder->update(['name' => $validated['name']]);

        return redirect()
            ->back()
            ->with('success', 'Folder renamed.');
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'folder_id' => 'required|exists:folders,id',
        ]);

        $folder = Folder::where('user_id', $request->user()->id)
            ->findOrFail($validated['folder_id']);

        $this->deleteFolderAndContents($folder);

        return redirect()
            ->back()
            ->with('success', 'Folder and everything inside it have been deleted.');
    }

    /**
     * Permanently delete a folder, all its files (from storage and DB), and all descendant folders.
     */
    private function deleteFolderAndContents(Folder $folder): void
    {
        foreach ($folder->files as $file) {
            Storage::disk('public')->delete($file->path);
            $file->delete();
        }

        foreach ($folder->children as $child) {
            $this->deleteFolderAndContents($child);
        }

        $folder->delete();
    }
}
