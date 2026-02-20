<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FolderResource\Pages;
use App\Models\Folder;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FolderResource extends Resource
{
    protected static ?string $model = Folder::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationLabel = 'Folders';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Folder Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Folder Name'),

                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Folder')
                            ->options(Folder::whereNull('parent_id')->pluck('name', 'id'))
                            ->nullable()
                            ->searchable(),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->label('Owner'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label('ID'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Folder Name'),

                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Owner'),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Folder')
                    ->placeholder('Root'),

                Tables\Columns\TextColumn::make('files_count')
                    ->counts('files')
                    ->label('Files'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->label('Created')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Filter by User'),

                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent Folder')
                    ->options(Folder::whereNull('parent_id')->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFolders::route('/'),
            'view' => Pages\ViewFolder::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() > 0 ? (string) static::getModel()::count() : null;
    }
}
