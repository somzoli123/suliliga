<?php

namespace App\Filament\Resources\Games\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Játék neve')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('short_name')
                    ->label('Rövidítés')
                    ->placeholder('Pl. CS2')
                    ->maxLength(20),

                Toggle::make('is_active')
                    ->label('Aktív játék')
                    ->default(true)
                    ->required(),
            ]);
    }
}
