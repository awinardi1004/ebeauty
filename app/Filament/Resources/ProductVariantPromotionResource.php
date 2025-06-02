<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProductVariant;
use Filament\Resources\Resource;
use App\Models\ProductVariantPromotion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductVariantPromotionResource\Pages;
use App\Filament\Resources\ProductVariantPromotionResource\RelationManagers;

class ProductVariantPromotionResource extends Resource
{
    protected static ?string $model = ProductVariantPromotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\Repeater::make('promotions')
                        ->label('Daftar Promo Varian Produk')
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->label('Produk')
                                ->options(Product::all()->pluck('title', 'id'))
                                ->reactive()
                                ->required(),

                            Forms\Components\Select::make('product_variant_id')
                                ->label('Varian Produk')
                                ->options(function (callable $get) {
                                    $productId = $get('product_id');
                                    return ProductVariant::where('product_id', $productId)
                                        ->pluck('name', 'id');
                                })
                                ->required()
                                ->reactive(),
                            Forms\Components\Placeholder::make('current_price')
                                ->label('Harga Saat Ini')
                                ->content(function (callable $get) {
                                    $variantId = $get('product_variant_id');
                                    $variant = ProductVariant::find($variantId);
                                    return $variant ? 'Rp' . number_format($variant->price) : '-';
                                }),

                            Forms\Components\TextInput::make('name')
                                ->label('Nama Promo')
                                ->required(),

                            Forms\Components\TextInput::make('disc_product_variant')
                                ->label('Harga Diskon')
                                ->numeric()
                                ->required(),

                            Forms\Components\DatePicker::make('start_date')
                                ->label('Tanggal Mulai')
                                ->required(),

                            Forms\Components\DatePicker::make('end_date')
                                ->label('Tanggal Berakhir')
                                ->required(),
                        ])
                        ->columns(2)
                        ->createItemButtonLabel('Tambah Promo')
                        ->columnSpanFull(),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Promotion Name'),

                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->disk('public')
                    ->height(60)
                    ->width(60)
                    ->getStateUsing(function ($record) {
                        return optional(
                            $record->productVariant?->product?->productImages->first()
                        )->image_path;
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->label('Product name')
                    ->getStateUsing(function ($record) {
                        return optional(
                            $record->productVariant?->product
                        )->title;
                    }),

                Tables\Columns\TextColumn::make('productVariant.name')
                    ->label('Variant'),

                Tables\Columns\TextColumn::make('productVariant.price')
                    ->label('Price'),

                Tables\Columns\TextColumn::make('disc_product_variant')
                    ->label('Disc Price'),
                
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->getStateUsing(function ($record) {
                        $start = \Carbon\Carbon::parse($record->start_date)->format('d M Y');
                        $end = \Carbon\Carbon::parse($record->end_date)->format('d M Y');
                        return "$start - $end";
                    }),

                
            ])
            ->filters([
                //
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
            'index' => Pages\ListProductVariantPromotions::route('/'),
            'create' => Pages\CreateProductVariantPromotion::route('/create'),
            'edit' => Pages\EditProductVariantPromotion::route('/{record}/edit'),
        ];
    }
}
