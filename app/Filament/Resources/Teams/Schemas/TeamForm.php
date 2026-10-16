<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Csapat neve')
                    ->required()
                    ->maxLength(100),

                TextInput::make('class_name')
                    ->label('Osztály')
                    ->placeholder('Pl. 11.A')
                    ->required()
                    ->maxLength(50),

                TextInput::make('captain_name')
                    ->label('Csapatkapitány neve')
                    ->required()
                    ->maxLength(100),

                Select::make('status')
                    ->label('Nevezés állapota')
                    ->options([
                        'pending' => 'Jóváhagyásra vár',
                        'approved' => 'Jóváhagyva',
                        'rejected' => 'Elutasítva',
                    ])
                    ->default('pending')
                    ->required(),
                    
Repeater::make('players')
    ->label('Csapat játékosai')
    ->relationship()
    ->schema([
        TextInput::make('name')
            ->label('Játékos neve')
            ->required()
            ->maxLength(100),

        TextInput::make('jersey_number')
            ->label('Mezszám')
            ->numeric()
            ->minValue(1)
            ->maxValue(99)
            ->nullable(),
    ])
    ->addActionLabel('Játékos hozzáadása')
    ->defaultItems(0)
    ->collapsible(),

            ]);
    }
}
