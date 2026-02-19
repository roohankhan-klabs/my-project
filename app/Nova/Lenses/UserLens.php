<?php

namespace App\Nova\Lenses;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Nova;

class UserLens extends Lens
{
    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];
    /**
     * Indicates whether the lens should automatically poll for new records.
     *
     * @var bool
     */
    // public static $polling = 300;

    /**
     * Indicates whether to show the polling toggle button inside Nova.
     *
     * @var bool
     */
    // public static $showPollingToggle = true;

    /**
     * Get the query builder / paginator for the lens.
     */
    public static function query(LensRequest $request, Builder $query): Builder|Paginator
    {
        return $request->withOrdering($request->withFilters(
            $query->select(self::columns())
                ->join('folders', 'users.id', 'folders.user_id', null, 'inner')
                ->join('files', 'folders.id', 'files.folder_id', null, 'left')
                ->groupBy('users.id', 'users.name')
                ->withCasts([
                    'total_folders' => 'int',
                    'total_files' => 'int',
                ])
        ), fn ($query) => $query->orderBy('folders', 'desc'));
    }

    /**
     * Get the columns that should be selected.
     *
     * @return array
     */
    protected static function columns()
    {
        return [
            'users.id',
            'users.name',
            DB::raw('count(folders.id) as total_folders, count(files.id) as total_files'),
        ];
    }

    /**
     * Get the fields available to the lens.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(Nova::__('ID'), 'id')->sortable(),
            Text::make('Name', 'name'),
            Number::make('Number of folders', 'total_folders', function ($value) {
                return number_format($value, 2, '.', '');
            }),
            Number::make('Number of files', 'total_files', function ($value) {
                return number_format($value, 2, '.', '');
            }),
        ];
    }

    /**
     * Get the cards available on the lens.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     *
     * @return array<int, \Laravel\Nova\Filters\Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available on the lens.
     *
     * @return array<int, \Laravel\Nova\Actions\Action>
     */
    public function actions(NovaRequest $request): array
    {
        return parent::actions($request);
    }

    /**
     * Get the URI key for the lens.
     */
    public function uriKey(): string
    {
        return 'user-lens';
    }
}
