<?php

namespace App\Filament\Resources\ProductPromotionResource\Pages;

use App\Filament\Resources\ProductPromotionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductPromotion extends CreateRecord
{
    protected static string $resource = ProductPromotionResource::class;

     protected function getRedirectUrl(): string
    {
        return ProductPromotionResource::getUrl('index');
    }
}
