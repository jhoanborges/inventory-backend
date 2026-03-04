<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages\CreateProducto;
use App\Filament\Resources\ProductoResource\Pages\EditProducto;
use App\Filament\Resources\ProductoResource\Pages\ListProductos;
use App\Models\Producto;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventario';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sku')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            TextInput::make('nombre')
                ->required()
                ->maxLength(255),
            Textarea::make('descripcion')
                ->columnSpanFull(),
            TextInput::make('categoria')
                ->maxLength(255),
            Select::make('unidad_medida')
                ->options([
                    'kg' => 'Kilogramo',
                    'unidad' => 'Unidad',
                    'caja' => 'Caja',
                    'litro' => 'Litro',
                    'metro' => 'Metro',
                ])
                ->required(),
            TextInput::make('precio')
                ->numeric()
                ->prefix('$'),
            TextInput::make('stock_actual')
                ->numeric()
                ->default(0),
            TextInput::make('stock_minimo')
                ->numeric()
                ->default(0),
            TextInput::make('barcode')
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            FileUpload::make('imagen')
                ->image()
                ->directory('productos'),
            Toggle::make('activo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->searchable()->sortable(),
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('categoria')->sortable(),
                TextColumn::make('unidad_medida'),
                TextColumn::make('precio')->money('USD')->sortable(),
                TextColumn::make('stock_actual')->sortable(),
                TextColumn::make('stock_minimo'),
                IconColumn::make('activo')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('activo'),
            ])
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductos::route('/'),
            'create' => CreateProducto::route('/create'),
            'edit' => EditProducto::route('/{record}/edit'),
        ];
    }
}
