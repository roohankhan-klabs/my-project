<?php

namespace App\Http\Controllers;

use App\Models\File as FileModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10 MB
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $uploadedFile = $request->file('file');

        $path = $uploadedFile->store('uploads/'.$user->id, 'public');

        FileModel::create([
            'user_id' => $user->id,
            'folder_id' => $validated['folder_id'] ?? null,
            'name' => $uploadedFile->getClientOriginalName(),
            'mime_type' => $uploadedFile->getClientMimeType(),
            'size' => $uploadedFile->getSize(),
            'path' => $path,
        ]);

        return redirect()
            ->back()
            ->with('success', 'File uploaded successfully.');
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

        return redirect()
            ->back()
            ->with('success', 'File deleted.');
    }
}
