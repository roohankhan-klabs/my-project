<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('storage_used')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' MB'),
                IconEntry::make('is_admin')
                    ->boolean(),
            ]);
    }
}
