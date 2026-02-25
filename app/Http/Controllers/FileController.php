<?php

namespace App\Http\Controllers;

use App\Models\File as FileModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'file' => 'required|array',
            'file.*' => 'file|max:10240', // 10 MB per file
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $uploadedFiles = $request->file('file');
        $totalSize = 0;
        $createdFiles = [];

        foreach ($uploadedFiles as $uploadedFile) {
            $path = $uploadedFile->store('uploads/'.$user->id, 'public');

            $file = FileModel::create([
                'user_id' => $user->id,
                'folder_id' => $validated['folder_id'] ?? null,
                'name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getClientMimeType(),
                'size' => $uploadedFile->getSize(),
                'path' => $path,
            ]);

            $createdFiles[] = $file;
            $totalSize += $uploadedFile->getSize();
        }

        // Update user's storage_used column by adding the total size of the uploaded files
        // if ($totalSize > 0) {
        //     $user->increment('storage_used', $totalSize);
        // }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => count($uploadedFiles) > 1 ? 'Files uploaded successfully.' : 'File uploaded successfully.',
                'files' => $createdFiles,
            ], 201);
        }

        return redirect()
            ->back()
            ->with('success', count($uploadedFiles) > 1 ? 'Files uploaded successfully.' : 'File uploaded successfully.');
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
        ]);

        $file = FileModel::where('user_id', $request->user()->id)
            ->findOrFail($validated['file_id']);

        Storage::disk('public')->delete($file->path);
        $file->delete();

        // $user = $request->user();
        // $user->decrement('storage_used', $file->size);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'File deleted.']);
        }

        return redirect()
            ->back()
            ->with('success', 'File deleted.');
    }

    public function move(Request $request)
    {
        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $file = FileModel::where('user_id', $request->user()->id)
            ->findOrFail($validated['file_id']);

        if (isset($validated['folder_id']) && $validated['folder_id'] !== null) {
            $folder = \App\Models\Folder::where('user_id', $request->user()->id)
                ->findOrFail($validated['folder_id']);
            $file->folder_id = $folder->id;
        } else {
            $file->folder_id = null;
        }

        $file->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'File moved successfully.', 'file' => $file]);
        }

        return redirect()
            ->back()
            ->with('success', 'File moved successfully.');
    }

    public function download(FileModel $file)
    {
        $user = Auth::user();

        // Check if user owns the file or is admin
        if ($user->id !== $file->user_id && ! $user->is_admin) {
            abort(403, 'Unauthorized');
        }

        $filePath = storage_path('app/public/'.$file->path);

        if (! file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, $file->name);
    }
}
