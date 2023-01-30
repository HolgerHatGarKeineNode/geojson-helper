<div class="w-full p-0 lg:p-6" wire:loading.class="opacity-50 pointer-events-none cursor-not-allowed">
    <div class="flex max-w-none flex-col space-y-4 text-black">
        <div class="rounded-lg bg-white shadow">
            <div class="px-4 py-5 lg:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Search for an area</h3>
                <div class="mt-2 text-sm text-gray-500">

                    <form wire:submit.prevent="submit">
                        <div class="flex flex-col space-y-2 lg:flex-row lg:space-y-0 lg:space-x-2">

                            @if (!$model?->simplified_geojson)
                                <x-input wire:model.defer="search" />
                                <x-button type="submit">Search</x-button>
                                <div>
                                    @if (!$model?->simplified_geojson && $search)
                                        <x-badge lg positive class="md:h-[38px] lg:whitespace-nowrap">
                                            Now select the appropriate place below so that a GeoJSON can be built.
                                        </x-badge>
                                    @endif
                                </div>
                            @else
                                <a href="/">
                                    <x-badge gray class="whitespace-nowrap">
                                        Reset form
                                    </x-badge>
                                </a>
                            @endif
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-2 lg:grid-cols-3">

            <div class="rounded-lg bg-white shadow">
                <div class="px-4 py-5 lg:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Search city: {{ $search }}</h3>
                    <div class="mt-2 text-sm text-gray-500">

                        <div class="flex max-h-[200px] flex-col space-y-2 overflow-y-auto">
                            @foreach ($osmSearchResultsCity as $item)
                                <code wire:key="osmItemCity_{{ $loop->index }}" class="w-full">
                                    <div class="cursor-pointer underline" wire:click="selectItem({{ $loop->index }})">
                                        {{ $item['display_name'] }} [{{ $item['type'] }}
                                        with {{ count($item['geojson']['coordinates'], COUNT_RECURSIVE) }} points]
                                    </div>
                                </code>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white shadow">
                <div class="px-4 py-5 lg:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Search state: {{ $search }}</h3>
                    <div class="mt-2 text-sm text-gray-500">

                        <div class="flex max-h-[200px] flex-col space-y-2 overflow-y-auto">
                            @foreach ($osmSearchResultsState as $item)
                                <code wire:key="osmItemState_{{ $loop->index }}" class="w-full">
                                    <div class="cursor-pointer underline"
                                        wire:click="selectItem({{ $loop->index }}, true)">
                                        {{ $item['display_name'] }}
                                        [with {{ count($item['geojson']['coordinates'], COUNT_RECURSIVE) }} points]
                                    </div>
                                </code>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white shadow">
                <div class="px-4 py-5 lg:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Search country: {{ $search }}</h3>
                    <div class="mt-2 text-sm text-gray-500">

                        <div class="flex max-h-[200px] flex-col space-y-2 overflow-y-auto">
                            @foreach ($osmSearchResultsCountry as $item)
                                <code wire:key="osmItemCountry_{{ $loop->index }}" class="w-full">
                                    <div class="cursor-pointer underline"
                                        wire:click="selectItem({{ $loop->index }}, false, true)">
                                        {{ $item['display_name'] }}
                                        [with {{ count($item['geojson']['coordinates'], COUNT_RECURSIVE) }} points]
                                    </div>
                                </code>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>

        </div>
        @if ($selectedItem)
            <div class="rounded-lg bg-white shadow">
                <div class="px-4 py-5 lg:p-6">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-medium leading-6 text-green-800">
                            GeoJSON created
                        </h3>
                        <x-toggle lg label="Fetch water boundaries from https://osm-boundaries.com"
                            wire:model="water" />
                    </div>
                    <div class="mt-2 text-sm text-gray-500">
                        <div class="flex flex-col space-y-2">
                            @php
                                $currentPoints = is_array($model->simplified_geojson['coordinates'][0]) ? count($model->simplified_geojson['coordinates'][0] ?? []) : 0;
                            @endphp
                            <h1>
                                Current data
                                [points:{{ $currentPoints }}]
                            </h1>
                            <h1 class="py-2">
                                (smaller percentage means fewer points - aim for no more than 150)
                            </h1>
                            <div class="flex hidden space-x-2 overflow-auto lg:block">
                                @php
                                    $btnClassLeft = 'relative inline-flex items-center rounded-l-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-amber-400 focus:z-10 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500';
                                    $btnClassRight = 'relative -ml-px inline-flex items-center rounded-r-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-amber-400 focus:z-10 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500';
                                    $btnClassCenter = 'relative -ml-px inline-flex items-center border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-amber-400 focus:z-10 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500';
                                    $currentClass = 'bg-amber-500';
                                @endphp
                                <div class="isolate inline-flex rounded-md shadow-sm">
                                    @foreach ($percentages as $percentage)
                                        @php
                                            $btnClass = $loop->first ? $btnClassLeft : ($loop->last ? $btnClassRight : $btnClassCenter);
                                        @endphp
                                        <button wire:key="percentage_{{ $loop->index }}" type="button"
                                            wire:click="setPercentage({{ $percentage }})"
                                            class="{{ $btnClass }} {{ $currentPercentage === $percentage ? $currentClass : '' }}">
                                            {{ $percentage }}%
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="block lg:hidden">
                                <x-native-select label="Select percentage" placeholder="Select percentage"
                                    :options="$percentages" wire:model="currentPercentage" />
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endif

        <div>
            @if ($model?->simplified_geojson)
                <div class="rounded-lg bg-white shadow">
                    <div class="grid grid-cols-1 gap-4 px-4 py-5 lg:grid-cols-2 lg:p-6">
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-[#FFA500]">OSM GeoJSON</h3>
                            <div class="mt-2 text-sm text-gray-500">
                                @php
                                    $jsonEncodedSelectedItem = json_encode($selectedItem['geojson'], JSON_THROW_ON_ERROR);
                                @endphp

                                <div class="flex w-full flex-col space-y-2">
                                    <pre class="overflow-x-auto py-3 text-[#FFA500]">{{ $jsonEncodedSelectedItem }}</pre>
                                    <div>
                                        <x-button x-data="{
                                            textToCopy: '{{ $jsonEncodedSelectedItem }}',
                                        }"
                                            @click.prevent="window.navigator.clipboard.writeText(textToCopy);window.$wireui.notify({title:'{{ __('Copied!') }}',icon:'success'});"
                                            lg amber>
                                            Copy to clipboard
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-blue-500">Simplified GeoJSON</h3>
                            <div class="mt-2 text-sm text-gray-500">
                                @php
                                    $jsonEncodedSimplifiedGeoJson = json_encode($model->simplified_geojson, JSON_THROW_ON_ERROR);
                                @endphp
                                <div class="flex w-full flex-col space-y-2">
                                    <pre class="overflow-x-auto py-3 text-blue-500">{{ $jsonEncodedSimplifiedGeoJson }}</pre>
                                    <div>
                                        <x-button x-data="{
                                            textToCopy: '{{ $jsonEncodedSimplifiedGeoJson }}',
                                        }"
                                            @click.prevent="window.navigator.clipboard.writeText(textToCopy);window.$wireui.notify({title:'{{ __('Copied!') }}',icon:'success'});"
                                            lg blue>
                                            Copy to clipboard
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if ($selectedItemWater)
                            <div class="cols-span-2">
                                <h3 class="text-lg font-medium leading-6 text-[#FF0084]">Water GeoJSON</h3>
                                <div class="mt-2 text-sm text-gray-500">
                                    @php
                                        $jsonEncodedGeoJsonWater = json_encode($selectedItemWater, JSON_THROW_ON_ERROR);
                                    @endphp
                                    <div class="flex w-full flex-col space-y-2">
                                        <pre class="overflow-x-auto py-3 text-[#FF0084]">{{ $jsonEncodedGeoJsonWater }}</pre>
                                        <div>
                                            <x-button x-data="{
                                                textToCopy: '{{ $jsonEncodedGeoJsonWater }}',
                                            }"
                                                @click.prevent="window.navigator.clipboard.writeText(textToCopy);window.$wireui.notify({title:'{{ __('Copied!') }}',icon:'success'});"
                                                lg pink>
                                                Copy to clipboard
                                            </x-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col space-y-4 px-4 py-5 lg:p-6">
                        <div class="w-full">
                            <div>
                                <h1 class="font-bold">
                                    GeoJSON preview
                                </h1>
                                <div wire:ignore class="my-4" x-data="{
                                    geojson: @entangle('selectedItem.geojson'),
                                    simplifiedGeojson: @entangle('model.simplified_geojson'),
                                    geojsonWater: @entangle('selectedItemWater'),
                                    init() {
                                        const map = L.map($refs.map)
                                            .setView([0, 0], 13);
                                
                                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png?{foo}', { foo: 'bar', attribution: '&copy; <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors' }).addTo(map);
                                
                                        const geojsonFeature = {
                                            'type': 'Feature',
                                            'geometry': this.geojson
                                        };
                                        const simplifiedGeojsonFeature = {
                                            'type': 'Feature',
                                            'geometry': this.simplifiedGeojson
                                        };
                                        L.geoJson(geojsonFeature, { style: { color: '#FFA500', fillColor: '#FFA500', fillOpacity: 0.3 } }).addTo(map);
                                        let simplifiedGeoJSON = L.geoJson(simplifiedGeojsonFeature, { style: { fillOpacity: 0.5 } }).addTo(map);
                                        map.fitBounds(simplifiedGeoJSON.getBounds(), { padding: [50, 50] });
                                
                                        $wire.on('geoJsonUpdated', () => {
                                            map.eachLayer((layer) => {
                                                layer.remove();
                                            });
                                            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png?{foo}', { foo: 'bar', attribution: '&copy; <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors' }).addTo(map);
                                            const geojsonFeature = {
                                                'type': 'Feature',
                                                'geometry': this.geojson
                                            };
                                            const simplifiedGeojsonFeature = {
                                                'type': 'Feature',
                                                'geometry': this.simplifiedGeojson
                                            };
                                            const geojsonWaterFeature = {
                                                'type': 'Feature',
                                                'geometry': this.geojsonWater
                                            };
                                            L.geoJson(geojsonFeature, { style: { color: '#FFA500', fillColor: '#FFA500', fillOpacity: 0.3 } }).addTo(map);
                                            L.geoJson(geojsonWaterFeature, { style: { color: '#FF0084', fillColor: '#FF0084', fillOpacity: 0.2 } }).addTo(map);
                                            let simplifiedGeoJSON = L.geoJson(simplifiedGeojsonFeature, { style: { fillOpacity: 0.5 } }).addTo(map);
                                            map.fitBounds(simplifiedGeoJSON.getBounds(), { padding: [50, 50] });
                                        });
                                    }
                                }">
                                    <div x-ref="map" style="height: 50vh;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @if ($model?->osm_relation)
            <div class="flex flex-col rounded-lg bg-white px-4 py-5 shadow lg:p-6">
                <code>
                    osm_id: {{ $model->osm_relation['osm_id'] }}
                </code>
                <code>
                    display_name: {{ $model->osm_relation['display_name'] }}
                </code>
            </div>
        @endif
        @if ($search)
            <div class='rounded-lg bg-white px-4 py-5 shadow lg:p-6'>
                <h1>Wikipedia search <span class='text-sm text-gray-500'>(for population data)</span></h1>
                <div class="flex space-x-2">
                    <a target="_blank" class="text-amber-500 underline"
                        href="https://en.wikipedia.org/wiki/{{ urlencode($search) }}">Wikipedia EN:
                        {{ $search }}</a>
                    <a target="_blank" class="text-amber-500 underline"
                        href="https://de.wikipedia.org/wiki/{{ urlencode($search) }}">Wikipedia DE:
                        {{ $search }}</a>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .leaflet-attribution-flag {
        display: inline;
    }
</style>
