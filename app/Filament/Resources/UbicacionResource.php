<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UbicacionResource\Pages\ListUbicaciones;
use App\Models\Ubicacion;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class UbicacionResource extends Resource
{
    protected static ?string $model = Ubicacion::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|\UnitEnum|null $navigationGroup = 'Operaciones';

    protected static ?string $navigationLabel = 'Ubicaciones';

    protected static ?string $pluralModelLabel = 'Ubicaciones';

    protected static ?string $modelLabel = 'Ubicación';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lat')
                    ->label('Latitud'),
                TextColumn::make('lng')
                    ->label('Longitud'),
                TextColumn::make('altitud')
                    ->label('Altitud')
                    ->suffix(' m')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('velocidad')
                    ->label('Velocidad')
                    ->suffix(' m/s')
                    ->toggleable(),
                TextColumn::make('rumbo')
                    ->label('Rumbo')
                    ->suffix('°')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('precision')
                    ->label('Precisión')
                    ->suffix(' m')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('dispositivo.modelo')
                    ->label('Dispositivo')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('dispositivo.os')
                    ->label('SO')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('dispositivo.bateria')
                    ->label('Batería')
                    ->suffix('%')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('dispositivo.version_app')
                    ->label('Versión App')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('registrado_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('registrado_at', 'desc')
            ->defaultGroup(
                Group::make('user.name')
                    ->label('Usuario')
                    ->collapsible()
            )
            ->groups([
                Group::make('user.name')
                    ->label('Usuario')
                    ->collapsible(),
                Group::make('registrado_at')
                    ->label('Fecha')
                    ->date()
                    ->collapsible(),
            ])
            ->filters([])
            ->recordActions([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUbicaciones::route('/'),
        ];
    }
}
