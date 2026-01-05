<?php

namespace Lbcdev\LivewireMapComponent;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class MapComponent extends Component
{
    // Propiedades públicas
    public ?float $latitude = null;
    public ?float $longitude = null;
    public bool $interactive = true;
    public bool $showLabel = true;
    public bool $showPasteButton = false;
    public int $height = 400;
    public int $zoom = 15;

    // Propiedades internas
    public bool $showCoordinateInput = false;
    public string $coordinateInput = '';

    // Coordenadas por defecto
    private const DEFAULT_LAT = 36.9990019;
    private const DEFAULT_LNG = -6.5478919;

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Setter para latitude que convierte strings a float
     */
    public function setLatitudeProperty($value): void
    {
        if ($value === null || $value === '') {
            $this->latitude = null;
            return;
        }

        if (is_string($value)) {
            $value = (float) $value;
        }

        $this->latitude = $value;
    }

    /**
     * Getter para latitude
     */
    public function getLatitudeProperty(): ?float
    {
        return $this->latitude;
    }

    /**
     * Setter para longitude que convierte strings a float
     */
    public function setLongitudeProperty($value): void
    {
        if ($value === null || $value === '') {
            $this->longitude = null;
            return;
        }

        if (is_string($value)) {
            $value = (float) $value;
        }

        $this->longitude = $value;
    }

    /**
     * Getter para longitude
     */
    public function getLongitudeProperty(): ?float
    {
        return $this->longitude;
    }

    public function mount(
        $latitude = null,  // Cambiar tipo para aceptar string o float
        $longitude = null, // Cambiar tipo para aceptar string o float
        bool $interactive = true,
        bool $showLabel = true,
        bool $showPasteButton = false,
        int $height = 400,
        int $zoom = 15
    ): void {
        // Usar los setters para convertir automáticamente
        $this->setLatitudeProperty($latitude);
        $this->setLongitudeProperty($longitude);
        $this->interactive = $interactive;
        $this->showLabel = $showLabel;
        $this->showPasteButton = $showPasteButton;
        $this->height = $height;
        $this->zoom = $zoom;
    }

    // public function mount(
    //     ?float $latitude = null,
    //     ?float $longitude = null,
    //     bool $interactive = true,
    //     bool $showLabel = true,
    //     bool $showPasteButton = false,
    //     int $height = 400,
    //     int $zoom = 15
    // ): void {
    //     $this->latitude = $latitude;
    //     $this->longitude = $longitude;
    //     $this->interactive = $interactive;
    //     $this->showLabel = $showLabel;
    //     $this->showPasteButton = $showPasteButton;
    //     $this->height = $height;
    //     $this->zoom = $zoom;
    // }

    public function updateCoordinates(float $lat, float $lng): void
    {
        if (!$this->interactive) {
            return;
        }

        $this->latitude = round($lat, 6);
        $this->longitude = round($lng, 6);

        // Emitir evento para que el componente padre pueda escuchar
        $this->dispatch('map-coordinates-updated', [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ]);
    }

    public function toggleCoordinateInput(): void
    {
        $this->showCoordinateInput = !$this->showCoordinateInput;
    }

    public function applyCoordinates(): void
    {
        if (!$this->interactive) {
            return;
        }

        $parts = array_map('trim', explode(',', $this->coordinateInput));

        if (count($parts) !== 2) {
            $this->addError('coordinateInput', 'Formato inválido. Usa: latitud, longitud');
            return;
        }

        $lat = filter_var($parts[0], FILTER_VALIDATE_FLOAT);
        $lng = filter_var($parts[1], FILTER_VALIDATE_FLOAT);

        if ($lat === false || $lng === false) {
            $this->addError('coordinateInput', 'Las coordenadas deben ser números válidos');
            return;
        }

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            $this->addError('coordinateInput', 'Coordenadas fuera de rango válido');
            return;
        }

        $this->updateCoordinates($lat, $lng);
        $this->showCoordinateInput = false;
        $this->coordinateInput = '';
        $this->resetErrorBag();

        // Emitir evento para que Alpine actualice el mapa
        $this->dispatch('fly-to-coordinates', [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ]);
    }

    public function getDisplayLatitudeProperty(): float
    {
        return $this->latitude ?? self::DEFAULT_LAT;
    }

    public function getDisplayLongitudeProperty(): float
    {
        return $this->longitude ?? self::DEFAULT_LNG;
    }

    public function getHasCoordinatesProperty(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return View::make('lbcdev-map::map-component');
    }
}
