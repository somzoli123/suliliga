<?php

namespace App\Filament\Resources\Tournaments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TournamentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('A bajnokság adatai')
                    ->description('Ez jelenik meg a nyilvános kezdőlapon a bajnokság nevénél.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')->label('Bajnokság neve')->required()->maxLength(120),
                        TextInput::make('season')->label('Szezon')->placeholder('Pl. 2026/27')->maxLength(50),
                        DatePicker::make('start_date')->label('Kezdés napja')->native(false),
                        DatePicker::make('end_date')->label('Zárás napja')->native(false)->afterOrEqual('start_date'),
                        Select::make('status')
                            ->label('Állapot')
                            ->options([
                                'upcoming' => 'Hamarosan indul',
                                'active' => 'Folyamatban',
                                'finished' => 'Lezárult',
                            ])
                            ->default('upcoming')
                            ->required(),
                    ]),
            ]);
    }
}
