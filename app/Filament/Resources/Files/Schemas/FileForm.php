<?php

namespace App\Filament\Resources\Files\Schemas;

use App\Models\Folder;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
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
                        if (! $userId) {
                            return [];
                        }

                        return Folder::where('user_id', $userId)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search, callable $get) {
                        $userId = $get('user_id');
                        if (! $userId) {
                            return [];
                        }

                        return Folder::where('user_id', $userId)
                            ->where('name', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id');
                    })
                    ->reactive(),
                FileUpload::make('path')
                    // ->image()
                    // ->imageEditor()
                    // ->imageEditorAspectRatioOptions([
                    //     null,
                    //     '16:9',
                    //     '4:3',
                    //     '1:1',
                    // ])
                    ->label('File')
                    ->disk('public')
                    ->required()
                    ->directory(function (callable $get): string {
                        $userId = $get('user_id');
                        return $userId ? "uploads/{$userId}" : 'uploads';
                    })
                    ->visibility('public')
                    ->maxSize(10240) // 10MB
                    // ->acceptedFileTypes(['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif'])
                    ->helperText('Maximum file size: 10MB. Accepted formats: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG, GIF')
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // Auto-populate other fields when file is uploaded
                        if ($state && is_object($state) && method_exists($state, 'getClientOriginalName')) {
                            $set('name', $state->getClientOriginalName());
                            $set('mime_type', $state->getMimeType());
                            $set('size', $state->getSize());
                        }
                    }),
                TextInput::make('name')
                    ->label('File Name')
                    ->hidden()
                    ->required()
                    ->default(function (callable $get) {
                        $path = $get('path');
                        if ($path) {
                            return basename($path);
                        }

                        return null;
                    }),
                TextInput::make('mime_type')
                    ->label('MIME Type')
                    ->hidden()
                    ->required()
                    ->default(function (callable $get) {
                        $path = $get('path');
                        if ($path) {
                            $fullPath = storage_path('app/public/'.$path);
                            if (file_exists($fullPath)) {
                                return mime_content_type($fullPath);
                            }
                        }

                        return null;
                    }),
                TextInput::make('size')
                    ->label('File Size')
                    ->hidden()
                    ->required()
                    ->default(function (callable $get) {
                        $path = $get('path');
                        if ($path) {
                            $fullPath = storage_path('app/public/'.$path);
                            if (file_exists($fullPath)) {
                                return filesize($fullPath);
                            }
                        }

                        return null;
                    }),
            ]);
    }
}
