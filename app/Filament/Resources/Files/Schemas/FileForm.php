<?php

namespace App\Filament\Resources\Files\Schemas;

use App\Models\User;
use App\Models\Folder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Closure;

class FileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
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
                    ->reactive(),
                Select::make('folder_id')
                    ->label('Folder')
                    ->options(function (callable $get) {
                        $userId = $get('user_id');
                        if (!$userId) {
                            return [];
                        }
                        return Folder::where('user_id', $userId)
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
                            ->where('name', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id');
                    })
                    ->reactive(),
                FileUpload::make('file')
                    ->label('File')
                    ->required()
                    ->directory('files')
                    ->visibility('public')
                    ->maxSize(10240) // 10MB
                    ->acceptedFileTypes(['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif'])
                    ->helperText('Maximum file size: 10MB. Accepted formats: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG, GIF')
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return null;
                        }
                        
                        $file = $state[0];
                        return [
                            'name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                        ];
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (!$state) {
                            return null;
                        }
                        
                        $file = $state[0];
                        return [
                            'name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                            'path' => $file->store('files', 'public'),
                        ];
                    }),
            ]);
    }
}
