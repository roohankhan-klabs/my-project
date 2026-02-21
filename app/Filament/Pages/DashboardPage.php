<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Models\User;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;

class DashboardPage extends Page
{
    // protected static ?string $navigationIcon = 'heroicon-o-home';
    // protected static ?string $navigationLabel = 'Dashboard';
    // protected static ?int $navigationSort = -1;

    // protected static string $view = 'filament.pages.dashboard';

    // public $user;
    // public $stats = [];

    // public function mount(): void
    // {
    //     $this->user = Auth::user();
    //     $this->stats = [
    //         'total_users' => User::count(),
    //         'total_files' => File::count(),
    //         'total_folders' => Folder::count(),
    //         'my_files' => File::where('user_id', $this->user->id)->count(),
    //         'my_folders' => Folder::where('user_id', $this->user->id)->count(),
    //         'storage_used' => number_format($this->user->storage_used / 1024, 2) . ' MB',
    //     ];
    // }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make('refresh')
    //             ->label('Refresh')
    //             ->icon('heroicon-o-arrow-path')
    //             ->action(function () {
    //                 $this->mount();
    //             }),
    //         Action::make('logout')
    //             ->label('Logout')
    //             ->icon('heroicon-o-arrow-left-on-rectangle')
    //             ->url('/logout')
    //             ->color('gray'),
    //     ];
    // }
}
