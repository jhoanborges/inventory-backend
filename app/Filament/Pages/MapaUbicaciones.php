<?php

namespace App\Filament\Pages;

use App\Models\Ubicacion;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class MapaUbicaciones extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static string|\UnitEnum|null $navigationGroup = 'Operaciones';

    protected static ?string $navigationLabel = 'Mapa Ubicaciones';

    protected static ?string $title = 'Mapa de Ubicaciones';

    protected static ?string $slug = 'mapa-ubicaciones';

    protected static string $view = 'filament.pages.mapa-ubicaciones';

    public ?int $user_id = null;

    public ?string $desde = null;

    public ?string $hasta = null;

    public function mount(): void
    {
        $this->desde = now()->toDateString();
        $this->hasta = now()->toDateString();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Usuario')
                    ->options(
                        User::whereHas('ubicaciones')
                            ->pluck('name', 'id')
                    )
                    ->placeholder('Todos los usuarios')
                    ->searchable(),
                DatePicker::make('desde')
                    ->label('Desde')
                    ->default(now()),
                DatePicker::make('hasta')
                    ->label('Hasta')
                    ->default(now()),
            ])
            ->columns(3);
    }

    public function filter(): void
    {
        // Triggers reactive update via getViewData()
    }

    public function getMapData(): array
    {
        $query = Ubicacion::with('user')
            ->orderBy('registrado_at');

        if ($this->user_id) {
            $query->where('user_id', $this->user_id);
        }

        if ($this->desde) {
            $query->whereDate('registrado_at', '>=', $this->desde);
        }

        if ($this->hasta) {
            $query->whereDate('registrado_at', '<=', $this->hasta);
        }

        $ubicaciones = $query->get();

        $grouped = $ubicaciones->groupBy('user_id');

        $colors = ['#4285F4', '#EA4335', '#FBBC05', '#34A853', '#FF6D01', '#46BDC6', '#7B1FA2', '#C2185B'];
        $colorIndex = 0;

        $routes = [];

        foreach ($grouped as $userId => $points) {
            $user = $points->first()->user;
            $color = $colors[$colorIndex % count($colors)];
            $colorIndex++;

            $routes[] = [
                'user_id' => $userId,
                'user_name' => $user->name,
                'color' => $color,
                'points' => $points->map(fn ($p) => [
                    'lat' => (float) $p->lat,
                    'lng' => (float) $p->lng,
                    'timestamp' => $p->registrado_at->format('d/m/Y H:i:s'),
                    'velocidad' => $p->velocidad,
                ])->values()->toArray(),
            ];
        }

        return $routes;
    }

    protected function getViewData(): array
    {
        return [
            'mapData' => $this->getMapData(),
            'googleMapsApiKey' => config('services.google_maps.api_key'),
        ];
    }
}
