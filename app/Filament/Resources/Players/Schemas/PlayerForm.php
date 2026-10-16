<?php

namespace App\Filament\Resources\Players\Schemas;

use App\Models\Team;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PlayerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('team_id')
                    ->label('Csapat')
                    ->options(Team::query()
                        ->where('status', 'approved')
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                TextInput::make('name')
                    ->label('Játékos neve')
                    ->required()
                    ->maxLength(255),

                TextInput::make('jersey_number')
                    ->label('Mezszám')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(99),
            ]);
    }
}
