<?php

namespace App\Filament\Resources;

use App\Enums\EstadoRuta;
use App\Filament\Resources\RutaResource\Pages;
use App\Filament\Resources\RutaResource\RelationManagers\LogsRelationManager;
use App\Models\Ruta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RutaResource extends Resource
{
    protected static ?string $model = Ruta::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Logística';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('origen')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('destino')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('operador_id')
                ->relationship('operador', 'name')
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('vehiculo')
                ->maxLength(255),
            Forms\Components\Select::make('estado')
                ->options([
                    'pendiente' => 'Pendiente',
                    'en_progreso' => 'En Progreso',
                    'completada' => 'Completada',
                ])
                ->default('pendiente')
                ->required(),
            Forms\Components\DateTimePicker::make('fecha_inicio'),
            Forms\Components\DateTimePicker::make('fecha_fin'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('origen')->sortable(),
                Tables\Columns\TextColumn::make('destino')->sortable(),
                Tables\Columns\TextColumn::make('operador.name')->sortable(),
                Tables\Columns\TextColumn::make('vehiculo'),
                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'primary' => 'en_progreso',
                        'danger' => 'pausada',
                        'success' => 'completada',
                    ]),
                Tables\Columns\TextColumn::make('fecha_inicio')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Información General')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('nombre'),
                    Infolists\Components\TextEntry::make('operador.name')->label('Operador'),
                    Infolists\Components\TextEntry::make('vehiculo')->label('Vehículo'),
                    Infolists\Components\TextEntry::make('estado')
                        ->badge()
                        ->color(fn (EstadoRuta $state) => match ($state) {
                            EstadoRuta::Pendiente => 'warning',
                            EstadoRuta::EnProgreso => 'primary',
                            EstadoRuta::Pausada => 'danger',
                            EstadoRuta::Completada => 'success',
                        }),
                    Infolists\Components\TextEntry::make('fecha_inicio')
                        ->label('Inicio')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('—'),
                    Infolists\Components\TextEntry::make('fecha_fin')
                        ->label('Fin')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('—'),
                ]),
            Infolists\Components\Section::make('Origen')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('origen')->label('Nombre'),
                    Infolists\Components\TextEntry::make('origen_direccion')->label('Dirección'),
                    Infolists\Components\TextEntry::make('origen_lat')->label('Latitud'),
                    Infolists\Components\TextEntry::make('origen_lng')->label('Longitud'),
                ]),
            Infolists\Components\Section::make('Destino')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('destino')->label('Nombre'),
                    Infolists\Components\TextEntry::make('destino_direccion')->label('Dirección'),
                    Infolists\Components\TextEntry::make('destino_lat')->label('Latitud'),
                    Infolists\Components\TextEntry::make('destino_lng')->label('Longitud'),
                ]),
            Infolists\Components\Section::make('Pausa')
                ->schema([
                    Infolists\Components\TextEntry::make('motivo_pausa')
                        ->label('Motivo de pausa')
                        ->placeholder('Sin pausa activa'),
                ])
                ->visible(fn (Ruta $record) => $record->estado->value === 'pausada'),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            LogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRutas::route('/'),
            'create' => Pages\CreateRuta::route('/create'),
            'view' => Pages\ViewRuta::route('/{record}'),
            'edit' => Pages\EditRuta::route('/{record}/edit'),
        ];
    }
}
