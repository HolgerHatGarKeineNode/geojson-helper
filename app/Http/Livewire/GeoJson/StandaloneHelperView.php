<?php

namespace App\Http\Livewire\GeoJson;

use App\Models\CommunityModel;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use WireUi\Traits\Actions;

class StandaloneHelperView extends Component
{
    use Actions;

    public $model;

    public string $search = '';

    public array $osmSearchResultsCity = [];
    public array $osmSearchResultsState = [];
    public array $osmSearchResultsCountry = [];

    public $selectedItem;
    public $currentPercentage = 4;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function rules(): array
    {
        return [
            'search'                   => 'required|string',
            'currentPercentage'        => 'required|numeric',
            'model.simplified_geojson' => 'nullable',
        ];
    }

    public function mount(): void
    {
        $this->model = new CommunityModel();
        $this->getSearchResults();
    }

    private function getSearchResults(): void
    {
        $responses = Http::pool(fn(Pool $pool) => [
            $pool->acceptJson()
                 ->get(
                     'https://nominatim.openstreetmap.org/search?city='.$this->search.'&format=json&polygon_geojson=1'
                 ),
            $pool->acceptJson()
                 ->get(
                     'https://nominatim.openstreetmap.org/search?state='.$this->search.'&format=json&polygon_geojson=1&polygon_threshold=0.001'
                 ),
            $pool->acceptJson()
                 ->get(
                     'https://nominatim.openstreetmap.org/search?country='.$this->search.'&format=json&polygon_geojson=1&polygon_threshold=0.01'
                 ),
        ]);

        $this->osmSearchResultsCity = $responses[0]->json();
        $this->osmSearchResultsState = $responses[1]->json();
        $this->osmSearchResultsCountry = $responses[2]->json();
    }

    public function submit(): void
    {
        $this->validate();
        $this->getSearchResults();
    }

    public function selectItem($index, bool $isState = false, $isCountry = false): void
    {
        match (true) {
            $isState => $this->selectedItem = $this->osmSearchResultsState[$index],
            $isCountry => $this->selectedItem = $this->osmSearchResultsCountry[$index],
            default => $this->selectedItem = $this->osmSearchResultsCity[$index],
        };
        $this->model->osm_relation = $this->selectedItem;

        $this->executeMapshaper(4);
    }

    private function executeMapshaper($percentage = 4): void
    {
        try {

            // put OSM geojson to storage
            Storage::disk('geo')
                   ->put('geojson_'.$this->selectedItem['osm_id'].'.json',
                       json_encode($this->selectedItem['geojson'], JSON_THROW_ON_ERROR)
                   );

            // execute mapshaper
            $input = storage_path('app/geo/geojson_'.$this->selectedItem['osm_id'].'.json');
            $output = storage_path('app/geo/output_'.$this->selectedItem['osm_id'].'.json');
            $mapshaperBinary = base_path('node_modules/mapshaper/bin/mapshaper');
            exec($mapshaperBinary.' '.$input.' -simplify dp '.$percentage.'% -o '.$output);
            $this->currentPercentage = $percentage;

            // trim geojson
            Storage::disk('geo')
                   ->put(
                       'trimmed_'.$this->selectedItem['osm_id'].'.json',
                       str(Storage::disk('geo')
                                  ->get('output_'.$this->selectedItem['osm_id'].'.json'))
                           ->after('{"type":"GeometryCollection", "geometries": [')
                           ->beforeLast(']}')
                           ->toString()
                   );

            // put trimmed geojson to model
            $this->model->simplified_geojson = json_decode(
                trim(
                    Storage::disk('geo')
                           ->get('trimmed_'.$this->selectedItem['osm_id'].'.json')
                ),
                false, 512, JSON_THROW_ON_ERROR
            );

            // emit event for AlpineJS
            $this->emit('geoJsonUpdated');

        } catch (\Exception $e) {
            $this->notification()
                 ->error('Error', $e->getMessage());
        }
    }

    public function updatedCurrentPercentage($value)
    {
        $this->executeMapshaper((float) $value);
    }

    public function setPercentage($percent): void
    {
        $this->executeMapshaper($percent);
    }

    public function render()
    {
        return view('livewire.geo-json.standalone-helper-view', [
            'percentages' => collect([
                0.5,
                0.75,
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                8,
                9,
                10,
                15,
                20,
                25,
                30,
                40,
                50,
            ])
                ->reverse()
                ->values()
                ->toArray(),
        ])->layout('layouts.guest');
    }
}
