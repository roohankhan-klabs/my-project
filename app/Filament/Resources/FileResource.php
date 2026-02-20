<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileResource\Pages;
use App\Models\File;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FileResource extends Resource
{
    protected static ?string $model = File::class;

    // protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationLabel = 'Files';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make('File Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('File Name'),

                        Forms\Components\TextInput::make('path')
                            ->required()
                            ->maxLength(500)
                            ->label('File Path'),

                        Forms\Components\TextInput::make('size')
                            ->required()
                            ->numeric()
                            ->suffix('bytes')
                            ->label('File Size'),

                        Forms\Components\TextInput::make('mime_type')
                            ->maxLength(100)
                            ->label('MIME Type'),

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
                    ->label('File Name')
                    ->limit(30),

                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Owner'),

                Tables\Columns\TextColumn::make('size')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' KB')
                    ->label('Size')
                    ->sortable(),

                Tables\Columns\TextColumn::make('mime_type')
                    ->label('Type')
                    ->limit(20),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->label('Uploaded')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Filter by User'),

                Tables\Filters\Filter::make('large_files')
                    ->label('Large Files (>1MB)')
                    ->query(fn (Builder $query) => $query->where('size', '>', 1024 * 1024)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (File $record) => asset('storage/' . $record->path))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListFiles::route('/'),
            'view' => Pages\ViewFile::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() > 0 ? (string) static::getModel()::count() : null;
    }
}
