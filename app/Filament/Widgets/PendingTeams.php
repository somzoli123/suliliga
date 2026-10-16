<?php

namespace App\Filament\Widgets;

use App\Models\Team;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PendingTeams extends TableWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Jóváhagyásra váró nevezések')
            ->description('Egy kattintással elfogadhatod vagy elutasíthatod a beérkezett csapatokat.')
            ->query(fn () => Team::query()->where('status', 'pending')->withCount('players')->latest('id'))
            ->paginated(false)
            ->emptyStateHeading('Nincs függő nevezés')
            ->emptyStateDescription('Minden beérkezett csapat el van bírálva.')
            ->emptyStateIcon(Heroicon::OutlinedCheckCircle)
            ->columns([
                TextColumn::make('name')->label('Csapat')->weight('bold')->description(fn (Team $r) => $r->class_name),
                TextColumn::make('captain_name')->label('Kapitány'),
                TextColumn::make('players_count')->label('Játékos')->badge()->color('gray'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Jóváhagyás')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->action(function (Team $record) {
                        $record->update(['status' => 'approved']);
                        Notification::make()->title("{$record->name} jóváhagyva")->success()->send();
                    }),
                Action::make('reject')
                    ->label('Elutasítás')
                    ->icon(Heroicon::OutlinedXMark)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Elutasítod a nevezést?')
                    ->action(function (Team $record) {
                        $record->update(['status' => 'rejected']);
                        Notification::make()->title("{$record->name} elutasítva")->danger()->send();
                    }),
            ]);
    }
}
