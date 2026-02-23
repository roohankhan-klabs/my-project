<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("User Information")
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),

                        Toggle::make('is_admin')
                            ->columnSpanFull()->disabled(),
                        TextEntry::make('created_at')->date(),
                    ])
            ]);
    }
}
