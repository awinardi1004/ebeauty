<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StorePromotionResource\Pages;
use App\Filament\Resources\StorePromotionResource\RelationManagers;
use App\Models\StorePromotion;
use Filament\Forms;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StorePromotionResource extends Resource
{
    protected static ?string $model = StorePromotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationLabel= 'Management Store Promotion';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Category Name')
                    ->required(),

                Forms\Components\DateTimePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->required(),
                
                Forms\Components\FileUpload::make('path')
                    ->label('Photo Promotion')
                    ->image()
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Promotion Name'),
                
                Tables\Columns\ImageColumn::make('path')
                    ->label('Photo')
                    ->disk('public'),
                
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
            'index' => Pages\ListStorePromotions::route('/'),
            'create' => Pages\CreateStorePromotion::route('/create'),
            'edit' => Pages\EditStorePromotion::route('/{record}/edit'),
        ];
    }
}
