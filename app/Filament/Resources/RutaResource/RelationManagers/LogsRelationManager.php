<?php

namespace App\Filament\Resources\RutaResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $title = 'Historial de Estados';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Usuario'),
                BadgeColumn::make('estado_anterior')
                    ->label('Estado Anterior')
                    ->colors([
                        'warning' => 'pendiente',
                        'primary' => 'en_progreso',
                        'danger' => 'pausada',
                        'success' => 'completada',
                    ]),
                BadgeColumn::make('estado_nuevo')
                    ->label('Estado Nuevo')
                    ->colors([
                        'warning' => 'pendiente',
                        'primary' => 'en_progreso',
                        'danger' => 'pausada',
                        'success' => 'completada',
                    ]),
                TextColumn::make('motivo')
                    ->label('Motivo')
                    ->placeholder('—')
                    ->wrap(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
