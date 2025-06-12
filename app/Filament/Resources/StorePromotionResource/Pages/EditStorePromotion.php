<?php

namespace App\Filament\Resources\StorePromotionResource\Pages;

use App\Filament\Resources\StorePromotionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStorePromotion extends EditRecord
{
    protected static string $resource = StorePromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

     protected function getRedirectUrl(): string
    {
        return StorePromotionResource::getUrl('index');
    }
}
