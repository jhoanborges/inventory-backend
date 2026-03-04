<?php

namespace App\Filament\Resources;

use App\Enums\EstadoRuta;
use App\Filament\Resources\RutaResource\Pages\CreateRuta;
use App\Filament\Resources\RutaResource\Pages\EditRuta;
use App\Filament\Resources\RutaResource\Pages\ListRutas;
use App\Filament\Resources\RutaResource\Pages\ViewRuta;
use App\Filament\Resources\RutaResource\RelationManagers\LogsRelationManager;
use App\Models\Ruta;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RutaResource extends Resource
{
    protected static ?string $model = Ruta::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static string|\UnitEnum|null $navigationGroup = 'Logística';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->required()
                ->maxLength(255),
            TextInput::make('origen')
                ->required()
                ->maxLength(255),
            TextInput::make('destino')
                ->required()
                ->maxLength(255),
            Select::make('operador_id')
                ->relationship('operador', 'name')
                ->searchable()
                ->preload(),
            TextInput::make('vehiculo')
                ->maxLength(255),
            Select::make('estado')
                ->options([
                    'pendiente' => 'Pendiente',
                    'en_progreso' => 'En Progreso',
                    'completada' => 'Completada',
                ])
                ->default('pendiente')
                ->required(),
            DateTimePicker::make('fecha_inicio'),
            DateTimePicker::make('fecha_fin'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('origen')->sortable(),
                TextColumn::make('destino')->sortable(),
                TextColumn::make('operador.name')->sortable(),
                TextColumn::make('vehiculo'),
                BadgeColumn::make('estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'primary' => 'en_progreso',
                        'danger' => 'pausada',
                        'success' => 'completada',
                    ]),
                TextColumn::make('fecha_inicio')->dateTime()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información General')
                ->columns(3)
                ->schema([
                    TextEntry::make('nombre'),
                    TextEntry::make('operador.name')->label('Operador'),
                    TextEntry::make('vehiculo')->label('Vehículo'),
                    TextEntry::make('estado')
                        ->badge()
                        ->color(fn (EstadoRuta $state) => match ($state) {
                            EstadoRuta::Pendiente => 'warning',
                            EstadoRuta::EnProgreso => 'primary',
                            EstadoRuta::Pausada => 'danger',
                            EstadoRuta::Completada => 'success',
                        }),
                    TextEntry::make('fecha_inicio')
                        ->label('Inicio')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('—'),
                    TextEntry::make('fecha_fin')
                        ->label('Fin')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('—'),
                ]),
            Section::make('Origen')
                ->columns(2)
                ->schema([
                    TextEntry::make('origen')->label('Nombre'),
                    TextEntry::make('origen_direccion')->label('Dirección'),
                    TextEntry::make('origen_lat')->label('Latitud'),
                    TextEntry::make('origen_lng')->label('Longitud'),
                ]),
            Section::make('Destino')
                ->columns(2)
                ->schema([
                    TextEntry::make('destino')->label('Nombre'),
                    TextEntry::make('destino_direccion')->label('Dirección'),
                    TextEntry::make('destino_lat')->label('Latitud'),
                    TextEntry::make('destino_lng')->label('Longitud'),
                ]),
            Section::make('Pausa')
                ->schema([
                    TextEntry::make('motivo_pausa')
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
            'index' => ListRutas::route('/'),
            'create' => CreateRuta::route('/create'),
            'view' => ViewRuta::route('/{record}'),
            'edit' => EditRuta::route('/{record}/edit'),
        ];
    }
}
