<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProductVariant;
use App\Models\ProductPromotion;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductPromotionResource\Pages;
use App\Filament\Resources\ProductPromotionResource\RelationManagers;

class ProductPromotionResource extends Resource
{
    protected static ?string $model = ProductPromotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\TextInput::make('name')
                        ->label('Promotion Name')
                        ->required(),

                    Forms\Components\DateTimePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->required(),

                    Forms\Components\DateTimePicker::make('end_date')
                    ->label('Tanggal Berakhir')
                    ->required()
                    ->rules(['after:start_date'])
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $start = $get('start_date');
                        if ($start) {
                            $minEndDate = Carbon::parse($start)->addDay();
                            if (Carbon::parse($state)->lt($minEndDate)) {
                                $set('end_date', $minEndDate);
                            }
                        }
                    })
                    ->helperText('Tanggal berakhir harus setelah tanggal mulai'),
                ])->columns(3),

                Forms\Components\Card::make([
                    Forms\Components\Repeater::make('productPromotionDetail') 
                        ->label('Daftar Promo Produk')
                        ->relationship()
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

                            Forms\Components\TextInput::make('disc_product_variant')
                                ->label('Harga Diskon')
                                ->numeric()
                                ->helperText('Harga harus lebih rendah')
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
            'index' => Pages\ListProductPromotions::route('/'),
            'create' => Pages\CreateProductPromotion::route('/create'),
            'edit' => Pages\EditProductPromotion::route('/{record}/edit'),
        ];
    }
}
