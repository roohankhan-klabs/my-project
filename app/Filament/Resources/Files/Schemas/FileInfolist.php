<?php

namespace App\Filament\Resources\Files\Schemas;

use App\Models\File;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('folder_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('mime_type'),
                TextEntry::make('size')
                    ->numeric(),
                TextEntry::make('path'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (File $record): bool => $record->trashed()),
            ]);
    }
}
