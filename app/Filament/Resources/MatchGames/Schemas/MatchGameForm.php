<?php

namespace App\Filament\Resources\MatchGames\Schemas;

use App\Models\Team;
use App\Models\Tournament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MatchGameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tournament_id')
                    ->label('Bajnokság')
                    ->options(Tournament::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('home_team_id')
                    ->label('Hazai csapat')
                    ->options(Team::query()->where('status', 'approved')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('away_team_id')
                    ->label('Vendégcsapat')
                    ->options(Team::query()->where('status', 'approved')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->different('home_team_id'),

                DateTimePicker::make('scheduled_at')
                    ->label('Mérkőzés időpontja'),

                TextInput::make('home_score')
                    ->label('Hazai gólok')
                    ->numeric()
                    ->minValue(0),

                TextInput::make('away_score')
                    ->label('Vendéggólok')
                    ->numeric()
                    ->minValue(0),

                TextInput::make('round')
                    ->label('Forduló')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->minValue(1),

                Select::make('status')
                    ->label('Állapot')
                    ->options([
                        'scheduled' => 'Kiírva',
                        'live' => 'Élőben',
                        'played' => 'Lejátszva',
                        'postponed' => 'Elhalasztva',
                    ])
                    ->default('scheduled')
                    ->required(),
            ]);
    }
}
