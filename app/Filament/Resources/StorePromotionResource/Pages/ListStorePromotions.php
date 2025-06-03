<?php

namespace App\Filament\Resources\StorePromotionResource\Pages;

use App\Filament\Resources\StorePromotionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStorePromotions extends ListRecords
{
    protected static string $resource = StorePromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
