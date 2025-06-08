<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Management Product';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('productVariants', 'category', 'productImages');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('title')
                        ->label('Title')
                        ->required(),

                Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                Forms\Components\Repeater::make('images')
                    ->label('Foto Produk')
                    ->relationship('productImages')
                    ->schema([
                        Forms\Components\Grid::make(1)->schema([ // pakai grid di dalam repeater
                            Forms\Components\FileUpload::make('image_path')
                                ->label('Gambar')
                                ->image()
                                ->imagePreviewHeight('150')
                                ->panelAspectRatio('1:1')
                                ->required()
                                ->directory('product-images')
                                ->maxSize(2048)
                                ->preserveFilenames(),
                        ])->columnSpan(1),
                    ])
                    ->grid(8) // atur grid repeater
                    ->maxItems(8)
                    ->addActionLabel('Tambah Gambar')
                    ->columnSpanFull(),
                
                Forms\Components\Repeater::make('productVariants')
                    ->label('Varian Produk')
                    ->relationship('productVariants')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Varian')
                            ->required(),

                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                    ])
                    ->createItemButtonLabel('Tambah Varian')
                    ->columns(4)
                    ->minItems(1)
                    ->maxItems(20)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(6) 
                    ->columnSpanFull() 
                    ->extraAttributes(['style' => 'resize: vertical;']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('productImages.0.image_path')
                    ->label('Photo')
                    ->disk('public')
                    ->height(60)
                    ->width(60),
                Tables\Columns\TextColumn::make('title')
                    ->label('Product Name'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category'),

                Tables\Columns\TextColumn::make('price_range')
                    ->label('Price')
                    ->getStateUsing(fn ($record) => $record)
                    ->formatStateUsing(function ($record) {
                        $min = $record->productVariants->min('price');
                        $max = $record->productVariants->max('price');

                        if ($min === null || $max === null) {
                            return 'No price data';
                        }

                        return 'Rp' . number_format($min) . ' - Rp' . number_format($max);
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
