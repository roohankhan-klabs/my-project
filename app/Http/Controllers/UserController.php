<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Folder;
use App\Models\File;

class UserController extends Controller
{
    public function drive()
    {
        $user = Auth::user();

        $currentFolderId = request()->query('folder');
        $currentFolder = null;

        if ($currentFolderId) {
            $currentFolder = Folder::query()
                ->where('user_id', $user->id)
                ->where('id', $currentFolderId)
                ->firstOrFail();
        }

        $foldersQuery = Folder::query()
            ->where('user_id', $user->id)
            ->orderBy('name');

        if ($currentFolder) {
            $folders = $foldersQuery
                ->where('parent_id', $currentFolder->id)
                ->get();
        } else {
            $folders = $foldersQuery
                ->whereNull('parent_id')
                ->get();
        }

        $filesQuery = File::query()
            ->where('user_id', $user->id)
            ->orderBy('name');

        if ($currentFolder) {
            $files = $filesQuery
                ->where('folder_id', $currentFolder->id)
                ->get();
        } else {
            $files = $filesQuery
                ->whereNull('folder_id')
                ->get();
        }

        return view('dashboard', compact('folders', 'files', 'currentFolder'));
    }
}
