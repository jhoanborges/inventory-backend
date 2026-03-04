<?php

namespace App\Filament\Resources\RutaResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $title = 'Historial de Estados';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario'),
                Tables\Columns\BadgeColumn::make('estado_anterior')
                    ->label('Estado Anterior')
                    ->colors([
                        'warning' => 'pendiente',
                        'primary' => 'en_progreso',
                        'danger' => 'pausada',
                        'success' => 'completada',
                    ]),
                Tables\Columns\BadgeColumn::make('estado_nuevo')
                    ->label('Estado Nuevo')
                    ->colors([
                        'warning' => 'pendiente',
                        'primary' => 'en_progreso',
                        'danger' => 'pausada',
                        'success' => 'completada',
                    ]),
                Tables\Columns\TextColumn::make('motivo')
                    ->label('Motivo')
                    ->placeholder('—')
                    ->wrap(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
