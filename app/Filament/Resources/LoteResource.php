<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoteResource\Pages\CreateLote;
use App\Filament\Resources\LoteResource\Pages\EditLote;
use App\Filament\Resources\LoteResource\Pages\ListLotes;
use App\Models\Lote;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoteResource extends Resource
{
    protected static ?string $model = Lote::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventario';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('producto_id')
                ->relationship('producto', 'nombre')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('numero_lote')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            TextInput::make('cantidad')
                ->numeric()
                ->required()
                ->minValue(0),
            DatePicker::make('fecha_fabricacion'),
            DatePicker::make('fecha_vencimiento'),
            Select::make('estado')
                ->options([
                    'activo' => 'Activo',
                    'vencido' => 'Vencido',
                    'agotado' => 'Agotado',
                ])
                ->default('activo')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_lote')->searchable()->sortable(),
                TextColumn::make('producto.nombre')->searchable()->sortable(),
                TextColumn::make('cantidad')->sortable(),
                TextColumn::make('fecha_fabricacion')->date()->sortable(),
                TextColumn::make('fecha_vencimiento')->date()->sortable(),
                BadgeColumn::make('estado')
                    ->colors([
                        'success' => 'activo',
                        'danger' => 'vencido',
                        'warning' => 'agotado',
                    ]),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLotes::route('/'),
            'create' => CreateLote::route('/create'),
            'edit' => EditLote::route('/{record}/edit'),
        ];
    }
}
