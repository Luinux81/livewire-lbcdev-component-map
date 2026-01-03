# Livewire Map Component

Un componente Livewire flexible e interactivo para mapas usando Leaflet.js.

## Características

- 🗺️ Mapas interactivos con Leaflet.js
- 📍 Marcadores arrastrables
- 🎯 Click en el mapa para colocar marcadores
- 📋 Entrada manual de coordenadas
- 🎨 Soporte para modo claro/oscuro
- 🔒 Modo de solo lectura opcional
- ⚡ Eventos Livewire para integración con otros componentes

## Requisitos

- PHP 8.1 o superior
- Laravel 10.x o superior
- Livewire 3.x

## Instalación

### 1. Instalar el paquete via Composer

```bash
composer require lbcdev/livewire-map-component
```

### 2. Incluir Leaflet.js en tu layout

Agrega estos scripts en el `<head>` de tu layout principal (antes de `@livewireStyles`):

```html
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
```

### 3. (Opcional) Publicar las vistas

Si deseas personalizar las vistas del componente:

```bash
php artisan vendor:publish --tag=lbcdev-map-views
```

Las vistas se publicarán en `resources/views/vendor/lbcdev-map/`.

## Uso Básico

### Uso simple

```blade
<livewire:lbcdev-map />
```

### Con coordenadas iniciales

```blade
<livewire:lbcdev-map 
    :latitude="40.416775" 
    :longitude="-3.703790" 
/>
```

### Modo de solo lectura

```blade
<livewire:lbcdev-map 
    :latitude="40.416775" 
    :longitude="-3.703790"
    :interactive="false"
/>
```

### Con todas las opciones

```blade
<livewire:lbcdev-map 
    :latitude="40.416775" 
    :longitude="-3.703790"
    :interactive="true"
    :showLabel="true"
    :showPasteButton="true"
    :height="500"
    :zoom="15"
/>
```

## Propiedades

| Propiedad | Tipo | Default | Descripción |
| --------- | ---- | ------- | ----------- |-------------|
| `latitude` | `?float` | `null` | Latitud inicial del marcador |
| `longitude` | `?float` | `?null` | Longitud inicial del marcador |
| `interactive` | `bool` | `true` | Permite interacción con el mapa |
| `showLabel` | `bool` | `true` | Muestra etiqueta con coordenadas |
| `showPasteButton` | `bool` | `false` | Muestra botón para pegar coordenadas |
| `height` | `int` | `400` | Altura del mapa en píxeles |
| `zoom` | `int` | `15` | Nivel de zoom inicial |

## Integración con Formularios

### Usando en un formulario Livewire

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class CreateLocation extends Component
{
    public $name;
    public $latitude;
    public $longitude;

    protected $listeners = ['map-coordinates-updated' => 'updateMapCoordinates'];

    public function updateMapCoordinates($data)
    {
        $this->latitude = $data['latitude'];
        $this->longitude = $data['longitude'];
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Guardar en la base de datos...
    }

    public function render()
    {
        return view('livewire.create-location');
    }
}
```

### Vista del formulario

```blade
<div>
    <form wire:submit="save">
        <div class="mb-4">
            <label class="block mb-2">Nombre</label>
            <input type="text" wire:model="name" class="w-full px-3 py-2 border rounded">
            @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-2">Ubicación en el mapa</label>
            <livewire:lbcdev-map 
                :latitude="$latitude" 
                :longitude="$longitude"
                :showPasteButton="true"
            />
            @error('latitude') <span class="text-red-500">{{ $message }}</span> @enderror
            @error('longitude') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">
            Guardar
        </button>
    </form>
</div>
```

## Eventos

### Eventos que emite el componente

#### `map-coordinates-updated`

Se emite cuando las coordenadas cambian (click en mapa, arrastrar marcador, o entrada manual):

```php
$this->dispatch('map-coordinates-updated', [
    'latitude' => 40.416775,
    'longitude' => -3.703790
]);
```

### Eventos que escucha el componente

#### `fly-to-coordinates`

Anima el mapa hacia unas coordenadas específicas:

```php
$this->dispatch('fly-to-coordinates', [
    'latitude' => 40.416775,
    'longitude' => -3.703790
]);
```

## Personalización

### Estilos personalizados

El componente utiliza clases de Tailwind CSS. Puedes personalizar los estilos publicando las vistas y modificándolas según tus necesidades.

### Coordenadas por defecto

Por defecto, si no se proporcionan coordenadas, el mapa se centrará en:

- Latitud: 36.9990019
- Longitud: -6.5478919

Puedes modificar estas coordenadas editando las constantes en el componente después de publicar las vistas.

## Ejemplos Avanzados

### Selector de ubicación para direcciones

```blade
<div>
    <div class="mb-4">
        <input 
            type="text" 
            placeholder="Buscar dirección..." 
            wire:model.live="searchAddress"
            class="w-full px-3 py-2 border rounded"
        >
    </div>

    <livewire:lbcdev-map 
        :latitude="$latitude" 
        :longitude="$longitude"
        :showPasteButton="true"
        :height="500"
    />
</div>
```

### Múltiples mapas en una página

```blade
<div class="grid grid-cols-2 gap-4">
    <div>
        <h3 class="mb-2">Ubicación de origen</h3>
        <livewire:lbcdev-map 
            :latitude="$originLat" 
            :longitude="$originLng"
            wire:key="origin-map"
        />
    </div>

    <div>
        <h3 class="mb-2">Ubicación de destino</h3>
        <livewire:lbcdev-map 
            :latitude="$destLat" 
            :longitude="$destLng"
            wire:key="destination-map"
        />
    </div>
</div>
```

## Soporte

Si encuentras algún problema o tienes sugerencias:

- 🐛 [Reportar un bug](https://github.com/Luinux81/livewire-lbcdev-component-map/issues)
- 💡 [Solicitar una característica](https://github.com/Luinux81/livewire-lbcdev-component-map/issues)

## Licencia

Este paquete es software de código abierto licenciado bajo la [Licencia MIT](LICENSE).

## Créditos

- Desarrollado por [Tu Nombre](https://github.com/Luinux81)
- Utiliza [Leaflet.js](https://leafletjs.com/) para los mapas
- Construido con [Livewire](https://livewire.laravel.com/)
