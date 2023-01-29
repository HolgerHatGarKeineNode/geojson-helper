<div class="p-6 w-full" wire:loading.class="opacity-50 pointer-events-none cursor-not-allowed">
    <div class="max-w-none text-black flex flex-col space-y-4">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Search for a city or state</h3>
                <div class="mt-2 text-sm text-gray-500">

                    <form wire:submit.prevent="submit">
                        <div class="flex space-x-2 items-end">

                            @if(!$model?->simplified_geojson)
                                <x-input wire:model.defer="search"/>
                                <x-button type="submit">Search</x-button>
                                <div>
                                    @if(!$model?->simplified_geojson && $search)
                                        <x-badge lg positive class="whitespace-nowrap">
                                            Now select the appropriate place below so that a geojson can be built.
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

        <div class="grid grid-cols-2 gap-2">

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Search city: {{ $search }}</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">

                        <div class="flex flex-col space-y-2 max-h-[200px] overflow-y-scroll">
                            @foreach($osmSearchResultsCity as $item)
                                <code class="w-full">
                                    <div wire:key="osmItemCity_{{ $loop->index }}" class="cursor-pointer underline"
                                         wire:click="selectItem({{ $loop->index }})">
                                        {{ $item['display_name'] }}
                                    </div>
                                </code>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Search state: {{ $search }}</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">

                        <div class="flex flex-col space-y-2 max-h-[200px] overflow-y-scroll">
                            @foreach($osmSearchResultsState as $item)
                                <code class="w-full">
                                    <div wire:key="osmItemState_{{ $loop->index }}" class="cursor-pointer underline"
                                         wire:click="selectItem({{ $loop->index }}, true)">
                                        {{ $item['display_name'] }}
                                    </div>
                                </code>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>

            {{--<div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Search country: {{ $search }}</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">

                        <div class="flex flex-col space-y-2 max-h-[200px] overflow-y-scroll">
                            @foreach($osmSearchResultsCountry as $item)
                                <code class="w-full">
                                    <div wire:key="osmItemCountry_{{ $loop->index }}" class="cursor-pointer underline"
                                         wire:click="selectItem({{ $loop->index }}, false, true)">
                                        {{ $item['display_name'] }}
                                    </div>
                                </code>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>--}}

        </div>
        @if($selectedItem)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-green-800">
                        geojson created
                    </h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <div class="flex flex-col space-y-2">
                            <h1>
                                Current data
                                [points: {{ is_array($model->simplified_geojson['coordinates'][0]) ? count($model->simplified_geojson['coordinates'][0] ?? []) : 0 }}
                                ]
                            </h1>
                            <h1 class="py-2">
                                smaller percentage means fewer points
                            </h1>
                            <div class="flex space-x-2">
                                @php
                                    $btnClassLeft = 'relative inline-flex items-center rounded-l-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-amber-400 focus:z-10 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500';
                                    $btnClassRight = 'relative -ml-px inline-flex items-center rounded-r-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-amber-400 focus:z-10 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500';
                                    $btnClassCenter = 'relative -ml-px inline-flex items-center border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-amber-400 focus:z-10 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500';
                                    $currentClass = 'bg-amber-500';
                                @endphp
                                <div class="isolate inline-flex rounded-md shadow-sm">
                                    @foreach($percentages as $percentage)
                                        @php
                                            $btnClass = $loop->first ? $btnClassLeft : ($loop->last ? $btnClassRight : $btnClassCenter);
                                        @endphp
                                        <button wire:key="percentage_{{ $loop->index }}" type="button" wire:click="setPercentage({{ $percentage }})"
                                                class="{{ $btnClass }} {{ $currentPercentage === $percentage ? $currentClass : ''}}">
                                            {{ $percentage }}%
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endif

        <div>
            @if($model?->simplified_geojson)
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6 grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900">OSM geojson</h3>
                            <div class="mt-2 text-sm text-gray-500">
                                @php
                                    $jsonEncodedSelectedItem = json_encode($selectedItem['geojson'], JSON_THROW_ON_ERROR);
                                @endphp

                                <div class="flex flex-col space-y-2 w-full">
                                <pre
                                    class="overflow-x-auto py-3">{{ $jsonEncodedSelectedItem }}</pre>
                                    <div>
                                        <x-button
                                            x-data="{
                                                textToCopy: '{{ $jsonEncodedSelectedItem }}',
                                            }"
                                            @click.prevent="window.navigator.clipboard.writeText(textToCopy);window.$wireui.notify({title:'{{ __('Copied!') }}',icon:'success'});"
                                            lg black>
                                            Copy to clipboard
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Simplified geojson</h3>
                            <div class="mt-2 text-sm text-gray-500">@php
                                    $jsonEncodedSimplifiedGeoJson = json_encode($model->simplified_geojson, JSON_THROW_ON_ERROR);
                                @endphp
                                <div class="flex flex-col space-y-2 w-full">
                                <pre
                                    class="overflow-x-auto py-3">{{ $jsonEncodedSimplifiedGeoJson }}</pre>
                                    <div>
                                        <x-button
                                            x-data="{
                                                textToCopy: '{{ $jsonEncodedSimplifiedGeoJson }}',
                                            }"
                                            @click.prevent="window.navigator.clipboard.writeText(textToCopy);window.$wireui.notify({title:'{{ __('Copied!') }}',icon:'success'});"
                                            lg black>
                                            Copy to clipboard
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-5 sm:p-6 flex flex-col space-y-4">
                        <div class="grid grid-cols-2 gap-1">
                            <div>
                                <h1 class="font-bold">
                                    OSM geojson
                                </h1>
                                <div wire:ignore
                                     class="my-4"
                                     x-data="{
                                            geojson: @entangle('selectedItem.geojson'),
                                            init() {
                                                var map = L.map($refs.mapOriginal)
                                                .setView([0, 0], 13);

                                                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png?{foo}', {foo: 'bar', attribution: '&copy; <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors'}).addTo(map);

                                                var geojsonFeature = {
                                                    'type': 'Feature',
                                                    'geometry': this.geojson
                                                };
                                                console.log(geojsonFeature);
                                                L.geoJSON(geojsonFeature).addTo(map);
                                                let geoJSON = L.geoJson(geojsonFeature).addTo(map);
                                                map.fitBounds(geoJSON.getBounds());

                                                $wire.on('geoJsonUpdated', () => {
                                                    map.eachLayer((layer) => {
                                                      layer.remove();
                                                    });
                                                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png?{foo}', {foo: 'bar', attribution: '&copy; <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors'}).addTo(map);
                                                    var geojsonFeature = {
                                                        'type': 'Feature',
                                                        'geometry': this.geojson
                                                    };
                                                    L.geoJSON(geojsonFeature).addTo(map);
                                                    let geoJSON = L.geoJson(geojsonFeature).addTo(map);
                                                    map.fitBounds(geoJSON.getBounds());
                                                });
                                            }
                                        }">
                                    <div x-ref="mapOriginal" style="height: 30vh;"></div>
                                </div>
                            </div>
                            <div>
                                <h1 class="font-bold">
                                    Simplified geojson
                                </h1>
                                <div wire:ignore
                                     class="my-4"
                                     x-data="{
                                            simplifiedGeojson: @entangle('model.simplified_geojson'),
                                            init() {
                                                var map = L.map($refs.map)
                                                .setView([0, 0], 13);

                                                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png?{foo}', {foo: 'bar', attribution: '&copy; <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors'}).addTo(map);

                                                var geojsonFeature = {
                                                    'type': 'Feature',
                                                    'geometry': this.simplifiedGeojson
                                                };
                                                console.log(geojsonFeature);
                                                L.geoJSON(geojsonFeature).addTo(map);
                                                let geoJSON = L.geoJson(geojsonFeature).addTo(map);
                                                map.fitBounds(geoJSON.getBounds());

                                                $wire.on('geoJsonUpdated', () => {
                                                    map.eachLayer((layer) => {
                                                      layer.remove();
                                                    });
                                                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png?{foo}', {foo: 'bar', attribution: '&copy; <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors'}).addTo(map);
                                                    var geojsonFeature = {
                                                        'type': 'Feature',
                                                        'geometry': this.simplifiedGeojson
                                                    };
                                                    L.geoJSON(geojsonFeature).addTo(map);
                                                    let geoJSON = L.geoJson(geojsonFeature).addTo(map);
                                                    map.fitBounds(geoJSON.getBounds());
                                                });
                                            }
                                        }">
                                    <div x-ref="map" style="height: 30vh;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="flex flex-col">
            @if($model?->osm_relation)
                <code>
                    osm_id: {{ $model->osm_relation['osm_id'] }}
                </code>
                <code>
                    display_name: {{ $model->osm_relation['display_name'] }}
                </code>
            @endif
        </div>
        <div>
            @if($search)
                <h1>Wikipedia Search</h1>
                <div class="flex space-x-2">
                    <a target="_blank" class="underline text-amber-500"
                       href="https://en.wikipedia.org/wiki/{{ urlencode($search) }}">Wikipedia EN: {{ $search }}</a>
                    <a target="_blank" class="underline text-amber-500"
                       href="https://de.wikipedia.org/wiki/{{ urlencode($search) }}">Wikipedia DE: {{ $search }}</a>
                </div>
            @endif
        </div>
    </div>
</div>
