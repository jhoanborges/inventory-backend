<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperacionResource\Pages\ListOperaciones;
use App\Models\Operacion;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OperacionResource extends Resource
{
    protected static ?string $model = Operacion::class;

    protected static ?string $pluralModelLabel = 'Operaciones';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventario';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('numero_operacion')->label('N° Operación'),
            TextEntry::make('tipo')
                ->badge()
                ->color(fn (string $state) => $state === 'entrada' ? 'success' : 'danger'),
            TextEntry::make('estado')
                ->badge()
                ->color(fn (string $state) => match ($state) {
                    'completada' => 'success',
                    'pendiente' => 'warning',
                    'cancelada' => 'danger',
                    default => 'gray',
                }),
            TextEntry::make('user.name')->label('Usuario'),
            TextEntry::make('ruta.nombre')->label('Ruta')->default('Sin ruta'),
            TextEntry::make('observaciones')->columnSpanFull(),
            TextEntry::make('created_at')->label('Fecha')->dateTime(),
            RepeatableEntry::make('items')
                ->label('Productos')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('producto.nombre')->label('Producto'),
                    TextEntry::make('producto.barcode')->label('Código'),
                    TextEntry::make('cantidad'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_operacion')->label('N° Operación')->searchable()->sortable(),
                TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state) => $state === 'entrada' ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'completada' => 'success',
                        'pendiente' => 'warning',
                        'cancelada' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('user.name')->label('Usuario')->sortable(),
                TextColumn::make('ruta.nombre')->label('Ruta')->default('—'),
                TextColumn::make('items_count')->counts('items')->label('Items'),
                TextColumn::make('created_at')->label('Fecha')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                    ]),
                SelectFilter::make('estado')
                    ->options([
                        'completada' => 'Completada',
                        'pendiente' => 'Pendiente',
                        'cancelada' => 'Cancelada',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOperaciones::route('/'),
        ];
    }
}
