<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        $folder = Folder::create([
            'user_id' => $user->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Folder created successfully.', 'folder' => $folder], 201);
        }

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

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Folder renamed.', 'folder' => $folder]);
        }

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

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Folder deleted.']);
        }

        return redirect()
            ->back()
            ->with('success', 'Folder and everything inside it have been deleted.');
    }

    public function move(Request $request)
    {
        Log::info('Folder move request received', [
            'request_data' => $request->all(),
            'user_id' => $request->user()->id,
        ]);

        $validated = $request->validate([
            'folder_id' => 'required|exists:folders,id',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        Log::info('Request validated', [
            'validated' => $validated,
            'user_id' => $request->user()->id,
        ]);

        $folder = Folder::where('user_id', $request->user()->id)
            ->findOrFail($validated['folder_id']);

        Log::info('Source folder found', [
            'folder_id' => $folder->id,
            'folder_name' => $folder->name,
            'current_parent_id' => $folder->parent_id,
        ]);

        if (isset($validated['parent_id']) && $validated['parent_id'] !== null) {
            Log::info('Looking for parent folder', [
                'parent_id' => $validated['parent_id'],
                'user_id' => $request->user()->id,
            ]);

            $parentFolder = Folder::where('user_id', $request->user()->id)
                ->findOrFail($validated['parent_id']);

            Log::info('Parent folder found', [
                'parent_folder_id' => $parentFolder->id,
                'parent_folder_name' => $parentFolder->name,
            ]);

            // Prevent moving a folder into itself or its children
            if ($folder->id === $parentFolder->id || $this->isDescendant($folder, $parentFolder->id)) {
                Log::info('Move blocked - folder into itself or child');

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Cannot move a folder into itself or its children.'], 422);
                }

                return redirect()->back()->withErrors(['Cannot move a folder into itself or its children.']);
            }

            $folder->parent_id = $parentFolder->id;
            Log::info('Setting parent_id', ['parent_id' => $parentFolder->id]);
        } else {
            Log::info('Moving to root - setting parent_id to null');
            $folder->parent_id = null;
        }

        Log::info('Saving folder', ['folder_data' => $folder->toArray()]);
        $folder->save();

        Log::info('Folder saved successfully', ['folder_id' => $folder->id]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Folder moved successfully.', 'folder' => $folder]);
        }

        return redirect()
            ->back()
            ->with('success', 'Folder moved successfully.');
    }

    /**
     * Check if targetId is a descendant (child, grandchild, etc.) of source.
     * Used to prevent moving a folder into one of its own children.
     */
    private function isDescendant(Folder $source, int $targetId): bool
    {
        $children = Folder::where('parent_id', $source->id)->get();

        foreach ($children as $child) {
            if ($child->id === $targetId) {
                return true;
            }

            if ($this->isDescendant(Folder::find($child->id), $targetId)) {
                return true;
            }
        }

        return false;
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
