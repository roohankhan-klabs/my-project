<?php

namespace App\Filament\Widgets;

use App\Models\File;
use App\Models\User;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        return [
            // Stat::make(
            //     label: 'Total posts',
            //     value: File::query()
            //         ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            //         ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            //         ->count(),
            // ),
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Admin Users', User::where('is_admin', true)->count())
                ->description('Users with admin access')
                ->icon('heroicon-o-shield-check')
                ->color('success'),

            Stat::make('Total Files', File::count())
                ->description('All uploaded files')
                ->icon('heroicon-o-document')
                ->color('info'),

            Stat::make('Total Storage', number_format(File::sum('size') / 1024 / 1024, 2).' MB')
                ->description('Used storage space')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),
        ];
    }
}
