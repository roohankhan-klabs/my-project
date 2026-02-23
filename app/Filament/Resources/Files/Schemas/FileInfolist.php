<?php

namespace App\Filament\Resources\Files\Schemas;

use App\Models\File;
use Dom\Text;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('File Information')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User'),
                        TextEntry::make('folder.name')
                            ->label('Folder')
                            ->placeholder('-'),
                        TextEntry::make('name'),
                        TextEntry::make('mime_type'),
                        TextEntry::make('size')
                            ->numeric()
                            ->formatStateUsing(fn(int $state): string => number_format($state / 1024, 2) . ' KB'),
                        TextEntry::make('path'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn(File $record): bool => $record->trashed()),
                    ]),
                Section::make('File Preview')
                    ->schema([
                        ImageEntry::make('path')
                            ->label('Preview')
                            ->visible(fn(File $record): bool => str_starts_with($record->mime_type, 'image/'))
                            ->url(fn(File $record): string => asset('storage/' . $record->path)),
                        TextEntry::make('download_link')
                            ->label('Download')
                            ->url(fn(File $record): string => route('files.download', $record)),
                    ]),
            ]);
    }
}
