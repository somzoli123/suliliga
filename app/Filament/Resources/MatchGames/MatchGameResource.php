<?php

namespace App\Filament\Resources\MatchGames;

use App\Filament\Resources\MatchGames\Pages\CreateMatchGame;
use App\Filament\Resources\MatchGames\Pages\EditMatchGame;
use App\Filament\Resources\MatchGames\Pages\ListMatchGames;
use App\Filament\Resources\MatchGames\Schemas\MatchGameForm;
use App\Filament\Resources\MatchGames\Tables\MatchGamesTable;
use App\Models\MatchGame;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MatchGameResource extends Resource
{
    protected static ?string $model = MatchGame::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Mérkőzések';
    protected static ?string $modelLabel = 'mérkőzés';
    protected static ?string $pluralModelLabel = 'mérkőzések';
    protected static string|UnitEnum|null $navigationGroup = 'Bajnokság';
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return MatchGameForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MatchGamesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMatchGames::route('/'),
            'create' => CreateMatchGame::route('/create'),
            'edit' => EditMatchGame::route('/{record}/edit'),
        ];
    }
}
