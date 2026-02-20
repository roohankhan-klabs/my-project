<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\File;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
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
                ->color('warning'),

            Stat::make('Total Storage', number_format(File::sum('size') / 1024 / 1024, 2) . ' MB')
                ->description('Used storage space')
                ->icon('heroicon-o-chart-bar')
                ->color('danger'),
        ];
    }
}
