<?php

namespace App\Filament\Resources\RutaResource\Pages;

use App\Filament\Resources\RutaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRuta extends ViewRecord
{
    protected static string $resource = RutaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
