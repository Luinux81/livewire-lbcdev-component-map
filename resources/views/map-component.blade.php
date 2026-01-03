{{-- resources/views/livewire/components/map-component.blade.php --}}
<div 
    class="space-y-4"
    x-data="mapComponentAlpine({
        lat: {{ $this->getDisplayLatitude() }},
        lng: {{ $this->getDisplayLongitude() }},
        interactive: {{ $interactive ? 'true' : 'false' }},
        zoom: {{ $zoom }}
    })"
    x-init="initMap()"
    @fly-to-coordinates.window="flyToCoordinates($event.detail)"
>
    @if($showLabel)
        {{-- Encabezado con ubicación --}}
        <div class="flex items-center gap-2">
            <x-heroicon-o-map-pin class="w-5 h-5 text-primary-500" />
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                @if($this->hasCoordinates())
                    Ubicación: {{ $latitude }}, {{ $longitude }}
                @else
                    Selecciona una ubicación
                @endif
            </span>
        </div>
    @endif

    @if($showPasteButton && $interactive)
        {{-- Botón para pegar coordenadas --}}
        <div class="flex items-center gap-2">
            <button
                type="button"
                wire:click="toggleCoordinateInput"
                class="inline-flex items-center px-3 py-2 text-sm font-medium bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Pegar coordenadas
            </button>
        </div>

        @if($showCoordinateInput)
            {{-- Input de coordenadas --}}
            <div class="flex items-start gap-2">
                <div class="flex-1">
                    <input
                        type="text"
                        wire:model="coordinateInput"
                        placeholder="Ej: 40.416775, -3.703790"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    />
                    @error('coordinateInput')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button
                    type="button"
                    wire:click="applyCoordinates"
                    class="px-4 py-2 text-sm font-medium bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition"
                >
                    Aplicar
                </button>
            </div>
        @endif
    @endif

    {{-- Contenedor del mapa --}}
    <div class="border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden relative" wire:ignore>
        <div
            x-ref="mapContainer"
            class="w-full"
            style="height: {{ $height }}px;"
        ></div>
        
        @if(!$interactive)
            {{-- Overlay cuando no es interactivo --}}
            <div class="absolute inset-0 bg-gray-900/10 dark:bg-gray-900/30 pointer-events-none"></div>
        @endif
    </div>
</div>

@script
<script>
Alpine.data('mapComponentAlpine', (config) => ({
    map: null,
    marker: null,
    
    initMap() {
        // Verificar que Leaflet esté disponible
        if (typeof L === 'undefined') {
            console.error('Leaflet no está cargado');
            return;
        }

        // Configurar opciones del mapa según interactividad
        const mapOptions = {
            zoomControl: config.interactive,
            scrollWheelZoom: config.interactive,
            doubleClickZoom: config.interactive,
            boxZoom: config.interactive,
            keyboard: config.interactive,
            dragging: config.interactive,
            touchZoom: config.interactive
        };

        // Inicializar mapa
        this.map = L.map(this.$refs.mapContainer, mapOptions)
            .setView([config.lat, config.lng], config.zoom);

        // Agregar capa de tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(this.map);

        // Crear marcador
        this.marker = L.marker([config.lat, config.lng], {
            draggable: config.interactive
        }).addTo(this.map);

        // Eventos solo si es interactivo
        if (config.interactive) {
            // Click en el mapa
            this.map.on('click', (e) => {
                this.updatePosition(e.latlng.lat, e.latlng.lng);
            });

            // Drag del marcador
            this.marker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                this.updatePosition(pos.lat, pos.lng);
            });
        }
    },

    updatePosition(lat, lng) {
        // Actualizar posición del marcador
        this.marker.setLatLng([lat, lng]);
        
        // Notificar al componente Livewire
        $wire.updateCoordinates(lat, lng);
    },

    flyToCoordinates(detail) {
        if (this.map && detail.latitude && detail.longitude) {
            this.map.flyTo([detail.latitude, detail.longitude], this.map.getZoom());
            this.marker.setLatLng([detail.latitude, detail.longitude]);
        }
    }
}));
</script>
@endscript