<?php

namespace Lbcdev\LivewireMapComponent;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class MapComponentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Registrar el componente Livewire
        Livewire::component('lbcdev-map', MapComponent::class);

        // Publicar las vistas
        $this->publishes([
            __DIR__ . '/../resources/views' => $this->app->resourcePath('views/vendor/lbcdev-map'),
        ], 'lbcdev-map-views');

        // Cargar las vistas
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'lbcdev-map');
    }

    public function register(): void
    {
        //
    }
}
