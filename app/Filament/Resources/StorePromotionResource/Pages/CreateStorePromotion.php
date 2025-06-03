<?php

namespace App\Filament\Resources\StorePromotionResource\Pages;

use App\Filament\Resources\StorePromotionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStorePromotion extends CreateRecord
{
    protected static string $resource = StorePromotionResource::class;

    protected function getRedirectUrl(): string
    {
        return StorePromotionResource::getUrl('index');
    }
}
