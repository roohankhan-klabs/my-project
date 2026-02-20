<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $currentFolderId = request()->query('folder');
        $currentFolder = null;
        $rootFolder = Folder::query()
            ->where('user_id', $user->id)
            // ->where('is_root', true)
            ->first();

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
            $folders = $rootFolder
                ? $foldersQuery->where('parent_id', $rootFolder->id)->get()
                : $foldersQuery->whereNull('parent_id')->get();
        }

        $filesQuery = File::query()
            ->where('user_id', $user->id)
            ->orderBy('name');

        if ($currentFolder) {
            $files = $currentFolder->$filesQuery->where('folder_id', $currentFolder->id)->get();
                // ? $filesQuery->whereNull('folder_id')->get()
        } else {
            $files = $filesQuery->whereNull('folder_id')->get();
        }

        return view('dashboard', compact('folders', 'files', 'currentFolder', 'rootFolder'));
    }
}
