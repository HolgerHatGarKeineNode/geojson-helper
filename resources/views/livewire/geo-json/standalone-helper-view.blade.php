<div class="p-6 w-full" wire:loading.class="opacity-50">
    <div class="max-w-none text-black flex flex-col space-y-4">
        <form wire:submit.prevent="submit">
            <div class="flex space-x-2">
                <x-input wire:model.defer="search"/>
                <x-button type="submit">Search</x-button>
            </div>
        </form>
        <div class="grid grid-cols-3 gap-2">
            <div>
                <h1>Search city: {{ $search }}</h1>
                <h1>OSM API Response</h1>
                <div class="flex flex-col space-y-2">
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
            <div>
                <h1>Search state: {{ $search }}</h1>
                <h1>OSM API Response</h1>
                <div class="flex flex-col space-y-2">
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
            <div>
                <h1>Search country: {{ $search }}</h1>
                <h1>OSM API Response</h1>
                <div class="flex flex-col space-y-2">
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
        <div>
            @if($selectedItem)
                geojson created
            @endif
        </div>
        <h1>Current data [points: {{ count($model->simplified_geojson['coordinates'][0] ?? []) }}]</h1>
        <div class="flex space-x-2">
            <div class="cursor-pointer font-bold underline" wire:click="setPercent(7)">7%</div>
            <div class="cursor-pointer font-bold underline" wire:click="setPercent(6)">6%</div>
            <div class="cursor-pointer font-bold underline" wire:click="setPercent(5)">5%</div>
            <div class="cursor-pointer font-bold underline" wire:click="setPercent(4)">4%</div>
            <div class="cursor-pointer font-bold underline" wire:click="setPercent(3)">3%</div>
            <div class="cursor-pointer font-bold underline" wire:click="setPercent(2)">2%</div>
            <div class="cursor-pointer font-bold underline" wire:click="setPercent(1)">1%</div>
            <div class="cursor-pointer font-bold underline" wire:click="setPercent(0.75)">0.75%</div>
            <div class="cursor-pointer font-bold underline" wire:click="setPercent(0.5)">0.5%</div>
        </div>
        <div>
            @if($model?->simplified_geojson)
                <h1>Simplified geojson</h1>
                <pre
                    class="overflow-x-auto py-4">{{ json_encode($model->simplified_geojson, JSON_THROW_ON_ERROR) }}</pre>
                <div wire:ignore
                     class="my-4"

                     x-data="{
                        simplifiedGeojson: @entangle('model.simplified_geojson'),
                        init() {
                            var map = L.map($refs.map)
                            .setView([0, 0], 13);

                            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

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
                                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                                var geojsonFeature = {
                                    'type': 'Feature',
                                    'geometry': this.simplifiedGeojson
                                };
                                L.geoJSON(geojsonFeature).addTo(map);
                                let geoJSON = L.geoJson(geojsonFeature).addTo(map);
                                map.fitBounds(geoJSON.getBounds());
                            })
                        }
                    }">
                    <div x-ref="map" style="width: 80vw; height: 30vh;"></div>
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
        <h1>Wikipedia Search Results</h1>
        <div class="flex space-x-2">
            @if($search)
                <a target="_blank" class="underline text-amber-500"
                   href="https://de.wikipedia.org/wiki/{{ urlencode($search) }}">Wikipedia: {{ $search }}</a>
            @endif
        </div>
    </div>
</div>
