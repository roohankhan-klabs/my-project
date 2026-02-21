<?php

namespace App\Filament\Resources\Folders;

use App\Filament\Resources\Folders\Pages\ManageFolders;
use App\Models\Folder;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
use Closure;

class FolderResource extends Resource
{
    protected static ?string $model = Folder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::FolderOpen;

    protected static ?string $recordTitleAttribute = 'Folder';

    protected static ?string $navigationParentItem = 'Users';

    protected static ?int $navigationSort = 1;
    
    // protected static string | UnitEnum | null $navigationGroup = 'Shop';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('User')
                    ->required()
                    ->options(function () {
                        return User::where('is_admin', false)->pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return User::where('is_admin', false)
                            ->where('name', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id');
                    })
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('parent_id', null)),
                Select::make('parent_id')
                    ->label('Folder')
                    ->options(function (callable $get) {
                        $userId = $get('user_id');
                        if (!$userId) {
                            return [];
                        }
                        return Folder::where('user_id', $userId)
                            ->whereNull('parent_id')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search, callable $get) {
                        $userId = $get('user_id');
                        if (!$userId) {
                            return [];
                        }
                        return Folder::where('user_id', $userId)
                            ->whereNull('parent_id')
                            ->where('name', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id');
                    })
                    ->reactive(),
                TextInput::make('name')
                    ->required(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('parent_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Folder $record): bool => $record->trashed()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Folder')
            ->modifyQueryUsing(fn ($query) => $query->with(['user', 'parent', 'files']))
            ->columns([                
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('user_name')
                    ->label('User')
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->user ? $record->user->name : '-'),
                TextColumn::make('parent_name')
                    ->label('Folder')
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->parent ? $record->parent->name : '-'),
                TextColumn::make('storage_used')
                    ->label('Storage Used')
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        if (!$record->relationLoaded('files')) {
                            return 'Loading...';
                        }
                        $totalSize = $record->files->sum('size');
                        return number_format($totalSize / 1024 / 1024, 2) . ' MB';
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFolders::route('/'),
        ];
    }
}
