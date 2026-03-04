<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovimientoInventarioResource\Pages\CreateMovimientoInventario;
use App\Filament\Resources\MovimientoInventarioResource\Pages\ListMovimientoInventarios;
use App\Models\MovimientoInventario;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MovimientoInventarioResource extends Resource
{
    protected static ?string $model = MovimientoInventario::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Movimientos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('producto_id')
                ->relationship('producto', 'nombre')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('lote_id')
                ->relationship('lote', 'numero_lote')
                ->searchable()
                ->preload(),
            Select::make('ruta_id')
                ->relationship('ruta', 'nombre')
                ->searchable()
                ->preload(),
            Select::make('user_id')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('tipo')
                ->options([
                    'entrada' => 'Entrada',
                    'salida' => 'Salida',
                ])
                ->required(),
            TextInput::make('cantidad')
                ->numeric()
                ->required()
                ->minValue(1),
            TextInput::make('motivo')
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('producto.nombre')->searchable()->sortable(),
                TextColumn::make('lote.numero_lote'),
                TextColumn::make('user.name')->label('Usuario'),
                BadgeColumn::make('tipo')
                    ->colors([
                        'success' => 'entrada',
                        'danger' => 'salida',
                    ]),
                TextColumn::make('cantidad')->sortable(),
                TextColumn::make('motivo'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMovimientoInventarios::route('/'),
            'create' => CreateMovimientoInventario::route('/create'),
        ];
    }
}
