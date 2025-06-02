<?php

namespace App\Filament\Resources\ProductVariantPromotionResource\Pages;

use App\Filament\Resources\ProductVariantPromotionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductVariantPromotion extends EditRecord
{
    protected static string $resource = ProductVariantPromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
