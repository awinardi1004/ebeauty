<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use App\Models\ProductReview;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductReviewResource\Pages;
use App\Filament\Resources\ProductReviewResource\RelationManagers;

class ProductReviewResource extends Resource
{
    protected static ?string $model = ProductReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
     protected static ?string $navigationLabel = 'Management Review';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User Name')
                    ->options(User::query()->pluck('username', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive(),

                Forms\Components\Select::make('order_id')
                    ->label('Order Code')
                    ->options(function (callable $get) {
                        $userId = $get('user_id');
                        if (!$userId) return [];

                        return \App\Models\Order::where('user_id', $userId)
                            ->pluck('code', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive(),

                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->options(function (callable $get) {
                        $orderId = $get('order_id');
                        if (!$orderId) return [];

                        $productIds = Transaction::where('order_id', $orderId)->pluck('product_id');

                        return Product::whereIn('id', $productIds)->pluck('title', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('rating')
                    ->label('Rating')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required()
                    ->helperText('Nilai harus antara 1 sampai 5'),

                Forms\Components\Textarea::make('review')
                    ->label('Review')
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
                Tables\Columns\TextColumn::make('user.username')
                    ->label('Username'),
                
                Tables\Columns\TextColumn::make('order.code')
                    ->label('Order Code'),
                
                Tables\Columns\TextColumn::make('product.title')
                    ->label('Product'),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'username')
                    ->label('Username'),
                
                 Tables\Filters\SelectFilter::make('rating')
                    ->label('Rating')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                    ]),
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
            'index' => Pages\ListProductReviews::route('/'),
            'create' => Pages\CreateProductReview::route('/create'),
            'edit' => Pages\EditProductReview::route('/{record}/edit'),
        ];
    }
}
