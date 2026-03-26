<x-filament-panels::page>
    <form wire:submit.prevent="filter">
        {{ $this->form }}

        <div class="mt-3">
            <x-filament::button type="submit" icon="heroicon-o-funnel">
                Filtrar
            </x-filament::button>
        </div>
    </form>

    <div
        id="map"
        style="width: 100%; height: 70vh; border-radius: 0.5rem;"
        class="mt-4 border border-gray-200 dark:border-gray-700"
    ></div>

    @if (! $googleMapsApiKey)
        <div class="mt-2 text-sm text-danger-600 dark:text-danger-400">
            Falta configurar GOOGLE_MAPS_API_KEY en el archivo .env
        </div>
    @endif

    <script>
        const mapDataJson = @js($mapData);

        function initMapaUbicaciones() {
            const mapEl = document.getElementById('map');
            if (!mapEl) return;

            const map = new google.maps.Map(mapEl, {
                zoom: 6,
                center: { lat: 14.6349, lng: -90.5069 },
                mapTypeId: 'roadmap',
            });

            const infoWindow = new google.maps.InfoWindow();
            const mapData = mapDataJson;

            if (!mapData.length) return;

            const bounds = new google.maps.LatLngBounds();

            mapData.forEach(function (route) {
                if (!route.points.length) return;

                const path = route.points.map(function (p) {
                    const pos = { lat: p.lat, lng: p.lng };
                    bounds.extend(pos);
                    return pos;
                });

                new google.maps.Polyline({
                    path: path,
                    geodesic: true,
                    strokeColor: route.color,
                    strokeOpacity: 0.8,
                    strokeWeight: 3,
                    map: map,
                });

                // Start marker
                const startMarker = new google.maps.Marker({
                    position: path[0],
                    map: map,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 7,
                        fillColor: route.color,
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 2,
                    },
                    title: route.user_name + ' - Inicio',
                });

                startMarker.addListener('click', function () {
                    infoWindow.setContent(
                        '<div style="color:#333"><strong>' + route.user_name + '</strong><br>' +
                        '<small>Inicio: ' + route.points[0].timestamp + '</small></div>'
                    );
                    infoWindow.open(map, startMarker);
                });

                // End marker
                if (path.length > 1) {
                    const lastPoint = route.points[route.points.length - 1];

                    const endMarker = new google.maps.Marker({
                        position: path[path.length - 1],
                        map: map,
                        icon: {
                            path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
                            scale: 5,
                            fillColor: route.color,
                            fillOpacity: 1,
                            strokeColor: '#ffffff',
                            strokeWeight: 2,
                        },
                        title: route.user_name + ' - Fin',
                    });

                    endMarker.addListener('click', function () {
                        infoWindow.setContent(
                            '<div style="color:#333"><strong>' + route.user_name + '</strong><br>' +
                            '<small>Fin: ' + lastPoint.timestamp + '</small><br>' +
                            (lastPoint.velocidad ? '<small>Vel: ' + lastPoint.velocidad + ' km/h</small>' : '') +
                            '</div>'
                        );
                        infoWindow.open(map, endMarker);
                    });
                }
            });

            map.fitBounds(bounds);
        }

        // Load Google Maps API dynamically (works with SPA navigation)
        (function () {
            const apiKey = @js($googleMapsApiKey);
            if (!apiKey) return;

            if (typeof google !== 'undefined' && google.maps) {
                initMapaUbicaciones();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key=' + apiKey + '&callback=initMapaUbicaciones';
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        })();
    </script>
</x-filament-panels::page>
