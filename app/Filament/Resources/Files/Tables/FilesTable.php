<?php

namespace App\Filament\Resources\Files\Tables;

use App\Models\File;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class FilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                TextColumn::make('folder.name')
                    ->label('Folder')
                    ->sortable(),
                // ->toggleable()
                // ->toggledHiddenByDefault(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('mime_type')
                    ->formatStateUsing(static function (File $record) {
                        $value = $record->mime_type;

                        return strlen($value) > 30 ? substr($value, 0, 30).'...' : $value;
                    })
                    ->searchable(),
                TextColumn::make('size_in_kb')
                    ->label('Size')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('path')
                    ->searchable(),
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
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
