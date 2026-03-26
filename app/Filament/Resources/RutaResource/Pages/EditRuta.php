<?php

namespace App\Filament\Resources\RutaResource\Pages;

use App\Filament\Resources\RutaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRuta extends EditRecord
{
    protected static string $resource = RutaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
