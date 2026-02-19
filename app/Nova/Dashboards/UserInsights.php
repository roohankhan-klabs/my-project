<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\NewUsers;
use Laravel\Nova\Dashboard;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;

class UserInsights extends Dashboard
{
    /**
     * Get the displayable name of the dashboard.
     *
     * @return string
     */
    public function name()
    {
        return 'User Insights';
    }
    /**
     * Get the menu that should represent the dashboard.
     *
     * @return \Laravel\Nova\Menu\MenuItem
     */
    public function menu(Request $request)
    {
        return parent::menu($request)->withBadge(function () {
            return 'NEW!';
        });
    }
    /**
     * Get the cards for the dashboard.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(): array
    {
        return [
            NewUsers::make(),
            // UsersOverTime::make(),
            //
        ];
    }

    /**
     * Get the URI key for the dashboard.
     */
    public function uriKey(): string
    {
        return 'user-insights';
    }
}
