<?php

namespace App\Filament\Resources\MatchGames\Pages;

use App\Filament\Resources\MatchGames\MatchGameResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMatchGames extends ListRecords
{
    protected static string $resource = MatchGameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
