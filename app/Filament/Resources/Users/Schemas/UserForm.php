<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation) => $operation === Operation::Create)
                    ->label(fn (string $operation) => $operation === Operation::Edit ? 'New Password (leave empty to keep current)' : 'Password'),
                // DateTimePicker::make('email_verified_at'),
                // Textarea::make('two_factor_secret')
                //     ->columnSpanFull(),
                // Textarea::make('two_factor_recovery_codes')
                //     ->columnSpanFull(),
                // DateTimePicker::make('two_factor_confirmed_at'),
                TextInput::make('storage_used')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->readOnly(),
                Toggle::make('is_admin')
                    ->required(),
            ]);
    }
}
