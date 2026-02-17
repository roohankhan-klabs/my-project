<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;

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
}
