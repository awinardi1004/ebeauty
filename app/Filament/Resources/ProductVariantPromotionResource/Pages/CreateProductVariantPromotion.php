<?php

namespace App\Filament\Resources\ProductVariantPromotionResource\Pages;

use Filament\Actions;
use App\Models\ProductVariantPromotion;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProductVariantPromotionResource;

class CreateProductVariantPromotion extends CreateRecord
{
    protected static string $resource = ProductVariantPromotionResource::class;

    protected function afterCreate(): void
    {
        $promotions = $this->data['promotions'] ?? [];

        foreach ($promotions as $promo) {
            ProductVariantPromotion::create([
                'product_variant_id' => $promo['product_variant_id'],
                'name' => $promo['name'],
                'disc_product_variant' => $promo['disc_product_variant'],
                'start_date' => $promo['start_date'],
                'end_date' => $promo['end_date'],
            ]);
        }

        // Hapus record utama yang dibuat otomatis
        $this->record->delete();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
