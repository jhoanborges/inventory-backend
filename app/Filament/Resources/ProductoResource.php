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
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
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
                ->maxLength(255)
                ->helperText('Se genera automáticamente si se deja vacío.'),
            ViewField::make('barcode_image')
                ->label('Código de barras')
                ->view('filament.forms.barcode-preview')
                ->live(),
            FileUpload::make('imagen')
                ->image()
                ->disk('r2')
                ->directory('productos'),
            Section::make('Imágenes del producto')
                ->schema([
                    FileUpload::make('imagenes')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('r2')
                        ->directory('productos/galeria')
                        ->maxFiles(10)
                        ->columnSpanFull(),
                ]),
            Toggle::make('activo')
                ->default(true),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('sku')->label('SKU'),
            TextEntry::make('nombre'),
            TextEntry::make('descripcion')->columnSpanFull(),
            TextEntry::make('categoria'),
            TextEntry::make('unidad_medida')->label('Unidad'),
            TextEntry::make('precio')->money('USD'),
            TextEntry::make('stock_actual'),
            TextEntry::make('stock_minimo'),
            TextEntry::make('barcode')->label('Código'),
            ViewEntry::make('barcode_image')
                ->label('Código de barras')
                ->view('filament.infolists.barcode-preview'),
            ImageEntry::make('imagen')
                ->disk('r2')
                ->height(120),
            ImageEntry::make('imagenes')
                ->label('Imágenes del producto')
                ->disk('r2')
                ->height(120)
                ->columnSpanFull(),
            IconEntry::make('activo')->boolean(),
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
                TextColumn::make('barcode')->searchable()->toggleable(),
                ImageColumn::make('barcode_image')
                    ->label('Código de barras')
                    ->disk('r2')
                    ->height(40)
                    ->toggleable(),
                TextColumn::make('stock_minimo'),
                IconColumn::make('activo')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('activo'),
            ])
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
