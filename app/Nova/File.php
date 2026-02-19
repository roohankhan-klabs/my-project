<?php

namespace App\Nova;

use App\Models\File as FileModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\File as FileField;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class File extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\File>
     */
    public static $model = FileModel::class;

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
        'name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable()->hideFromIndex(),
            FileField::make('File', 'path')
                ->disk('public')
                ->store(function (NovaRequest $request, $model, string $attribute, string $requestAttribute): array {
                    $file = $request->file($requestAttribute);
                    $userId = $request->viaResource === 'users' && $request->viaResourceId
                        ? (int) $request->viaResourceId
                        : $request->user()?->getKey();
                    $path = $file->store('uploads/'.$userId, 'public');

                    return [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                    ];
                })
                ->storeAs(fn (NovaRequest $request, $model, string $attribute, string $requestAttribute) => $request->file($requestAttribute)->getClientOriginalName())
                ->acceptedTypes('.pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif,.zip')
                ->thumbnail(function ($request, string $attribute, FileModel $model) {
                    if (! $model->path || ! $this->isImage($model->mime_type)) {
                        return null;
                    }

                    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                    $disk = Storage::disk('public');

                    return $disk->url($model->path);
                })
                ->preview(function ($request, ?string $attribute, FileModel $model) {
                    if (! $model->path || ! $this->isImage($model->mime_type)) {
                        return null;
                    }

                    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                    $disk = Storage::disk('public');

                    return $disk->url($model->path);
                })
                ->rules($request->isCreateOrAttachRequest() ? 'required' : 'nullable'),
            Text::make('Name')->exceptOnForms(),
            Text::make('Path')->onlyOnDetail(),
            Number::make('Size')
                ->exceptOnForms()
                ->displayUsing(fn ($value) => $value ? round($value / 1024, 2).' KB' : '-'),
            Text::make('Type', 'mime_type')
                ->displayUsing(fn ($value) => $value && strlen($value) > 30 ? substr($value, 0, 27).'...' : $value)
                ->exceptOnForms(),
            BelongsTo::make('User')
                ->default(fn (NovaRequest $request) => $request->viaResource === 'users' && $request->viaResourceId
                    ? $request->viaResourceId
                    : $request->user()?->getKey())
                ->readonly(),
            BelongsTo::make('Folder')
                ->nullable()
                ->default(fn (NovaRequest $request) => $request->viaResource === 'folders' && $request->viaResourceId
                    ? $request->viaResourceId
                    : null)
                ->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                    $userId = $request->viaResource === 'users' && $request->viaResourceId
                        ? (int) $request->viaResourceId
                        : $request->user()?->getKey();

                    return $query->where('user_id', $userId);
                })
                ->rules([
                    'nullable',
                    'exists:folders,id',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($value === null) {
                            return;
                        }

                        $userId = $request->viaResource === 'users' && $request->viaResourceId
                            ? (int) $request->viaResourceId
                            : $request->user()?->getKey();

                        $folderExists = \App\Models\Folder::where('id', $value)
                            ->where('user_id', $userId)
                            ->exists();

                        if (! $folderExists) {
                            $fail('You can only assign files to folders you own.');
                        }
                    },
                ]),
        ];
    }

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

    /**
     * Check if the given mime type is an image.
     */
    protected function isImage(?string $mimeType): bool
    {
        if (! $mimeType) {
            return false;
        }

        return str_starts_with($mimeType, 'image/');
    }
}
