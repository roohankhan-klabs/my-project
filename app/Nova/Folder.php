<?php

namespace App\Nova;

use App\Models\Folder as FolderModel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Folder extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Folder>
     */
    public static $model = FolderModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    // public static $with = ['folders', 'files'];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->rules('required')->sortable(),
            BelongsTo::make('User')
                ->default(fn (NovaRequest $request) => $request->viaResource === 'users' && $request->viaResourceId
                    ? $request->viaResourceId
                    : $request->user()?->getKey())
                ->readonly()
                ->sortable(),
            BelongsTo::make('Folder', 'parent')->nullable(),
            HasMany::make('Children', 'children', self::class),
            HasMany::make('Files'),
        ];
    }

    /**
     * Get the fields displayed by the resource on detail page.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    // public function fieldsForDetail(NovaRequest $request): array
    // {
    //     return [
    //         Text::make('Name', function () {
    //             return sprintf('%s %s', $this->first_name, $this->last_name);
    //         }),

    //         Text::make('Job Title'),
    //     ];
    // }

    /**
     * Get the cards available for the resource.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Laravel\Nova\Filters\Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array<int, \Laravel\Nova\Lenses\Lens>
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, \Laravel\Nova\Actions\Action>
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
