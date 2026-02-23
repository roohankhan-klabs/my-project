<?php

namespace App\Filament\Pages;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class DashboardPage extends Page
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate'),
                        DatePicker::make('endDate'),
                        // ...
                    ])
                    ->columns(3),
            ]);
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?int $navigationSort = -1;

    // protected string $view = 'filament.pages.dashboard';

    public $user;

    public $stats = [];

    public function getColumns(): int|array
    {
        return [
            'md' => 4,
            'xl' => 5,
        ];
    }
    // protected int | string | array $columnSpan = 'full';

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->stats = [
            'total_users' => User::count(),
            'total_files' => File::count(),
            'total_folders' => Folder::count(),
            'my_files' => File::where('user_id', $this->user->id)->count(),
            'my_folders' => Folder::where('user_id', $this->user->id)->count(),
            'storage_used' => $this->user->storage_used,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $this->mount();
                }),
            Action::make('logout')
                ->label('Logout')
                ->icon('heroicon-o-arrow-left-on-rectangle')
                ->url('/logout')
                ->color('gray'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverviewWidget::class,
        ];
    }
}
