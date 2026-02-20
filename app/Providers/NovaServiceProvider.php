<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\Features;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Enable RightToLeft support
        // Nova::enableRTL();
        // Nova::enableRTL(fn(Request $request) => $request->user()->wantsRTL());

        // Remove the theme switcher from the Nova sidebar
        Nova::withoutThemeSwitcher();

        // Enable breadcrumbs
        Nova::withBreadcrumbs();
        // Nova::withBreadcrumbs(function (NovaRequest $request) {
        //     return $request->user()->wantsBreadcrumbs();
        // });

        // Add a footer to the Nova sidebar
        Nova::footer(function (Request $request) {
            return Blade::render('
                @if(config(\'app.env\') == \'production\')
                    This is production!
                @elseif(config(\'app.env\') == \'development\')
                    This is development!
                @else
                    This is {{ config(\'app.env\') }}!
                @endif
            ');
        });

        //
    }

    /**
     * Register the configurations for Laravel Fortify.
     */
    protected function fortify(): void
    {
        Nova::fortify()
            ->features([
                Features::updatePasswords(),
                // Features::emailVerification(),
                // Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => true]),
            ])
            ->register();
    }

    /**
     * Register the Nova routes.
     */
    protected function routes(): void
    {
        Nova::routes()
            ->withAuthenticationRoutes(default: true)
            ->withPasswordResetRoutes()
            ->withoutEmailVerificationRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return $user->is_admin;
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array<int, \Laravel\Nova\Dashboard>
     */
    protected function dashboards(): array
    {
        return [
            new \App\Nova\Dashboards\Main(),
            (new \App\Nova\Dashboards\UserInsights())->showRefreshButton(),
            // UserInsights::make()->canSee(function ($request) {
            //     return $request->user()->can('viewUserInsights', User::class);
            // }),
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array<int, \Laravel\Nova\Tool>
     */
    public function tools(): array
    {
        return [];
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();

        Nova::initialPath('/resources/users');
        // Nova::initialPath(function ($request) {
        //     return $request->user()->initialPath();
        // });

        // Nova::report(function ($exception) {
        //     if (app()->bound('sentry')) {
        //         app('sentry')->captureException($exception);
        //     }
        // });
    }
}
