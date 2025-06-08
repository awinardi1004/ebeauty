<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProductVariant;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel= 'Management Transaction';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Kode Transaksi')
                    ->default(fn(): string => 'TRX-' . mt_rand(10000, 99999))
                    ->readOnly()
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->required()
                    ->options(User::all()->pluck('username', 'id')) 
                    ->searchable(),

                Forms\Components\Card::make([
                    Forms\Components\Repeater::make('transaction') 
                        ->label('Daftar Produk')
                        ->relationship('transaction')
                        ->defaultItems(1)
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->label('Produk')
                                ->options(Product::pluck('title', 'id'))
                                ->reactive()
                                ->afterStateUpdated(fn (callable $set) => $set('product_variant_id', null))
                                ->required(),

                            Forms\Components\Select::make('product_variant_id')
                                ->label('Varian Produk')
                                ->options(function (callable $get) {
                                    $productId = $get('product_id');
                                    if (!$productId) return [];

                                    return ProductVariant::where('product_id', $productId)->pluck('name', 'id');
                                })
                                ->required()
                                ->reactive(),

                            Forms\Components\TextInput::make('qty')
                                ->label('Quantity')
                                ->required(),
                        ])->createItemButtonLabel('Tambah Produk')
                ]),

                Forms\Components\FileUpload::make('payment_receipt')
                    ->label('Payment Receipt')
                    ->disk('public')
                    ->directory('payment-receipts') // opsional
                    ->image(), // jika ingin hanya file gambar
                
                Forms\Components\TextInput::make('amount')
                    ->label('Total')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ])
                    ->default('pending')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode Transaksi'),

                Tables\Columns\TextColumn::make('user.username')
                    ->label('Username '),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Total'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
