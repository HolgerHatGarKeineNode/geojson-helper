<?php

namespace App\Http\Livewire\GeoJson;

use App\Models\CommunityModel;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class StandaloneHelperView extends Component
{
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

    public function rules()
    {
        return [
            'search'                   => 'required|string',
            'model.simplified_geojson' => 'nullable',
        ];
    }

    public function mount()
    {
        $this->model = new CommunityModel();
        $this->getSearchResults();
    }

    private function getSearchResults()
    {
        $responses = Http::pool(fn(Pool $pool) => [
            $pool->acceptJson()
                 ->get(
                     'https://nominatim.openstreetmap.org/search?city='.$this->search.'&format=json&polygon_geojson=1'
                 ),
            $pool->acceptJson()
                 ->get(
                     'https://nominatim.openstreetmap.org/search?state='.$this->search.'&format=json&polygon_geojson=1'
                 ),
        ]);

        $this->osmSearchResultsCity = $responses[0]->json();
        $this->osmSearchResultsState = $responses[1]->json();
    }

    public function submit()
    {
        $this->validate();
        $this->getSearchResults();
    }

    public function selectItem($index, bool $isState = false, $isCountry = false)
    {
        if ($isState) {
            $this->selectedItem = $this->osmSearchResultsState[$index];
        } elseif ($isCountry) {
            $this->selectedItem = $this->osmSearchResultsCountry[$index];
        } else {
            $this->selectedItem = $this->osmSearchResultsCity[$index];
        }
        $this->model->osm_relation = $this->selectedItem;

        $this->executeMapshaper(4);
    }

    private function executeMapshaper($percentage = 4)
    {
        Storage::disk('geo')
               ->put('geojson_'.$this->selectedItem['osm_id'].'.json',
                   json_encode($this->selectedItem['geojson'], JSON_THROW_ON_ERROR));
        $input = storage_path('app/geo/geojson_'.$this->selectedItem['osm_id'].'.json');
        $output = storage_path('app/geo/output_'.$this->selectedItem['osm_id'].'.json');
        $mapshaperBinary = base_path('node_modules/mapshaper/bin/mapshaper');
        exec($mapshaperBinary.' '.$input.' -simplify dp '.$percentage.'% -o '.$output);
        Storage::disk('geo')
               ->put(
                   'trimmed_'.$this->selectedItem['osm_id'].'.json',
                   str(Storage::disk('geo')
                              ->get('output_'.$this->selectedItem['osm_id'].'.json'))
                       ->after('{"type":"GeometryCollection", "geometries": [')
                       ->beforeLast(']}')
                       ->toString()
               );
        $this->model->simplified_geojson = json_decode(trim(Storage::disk('geo')
                                                                   ->get('trimmed_'.$this->selectedItem['osm_id'].'.json')),
            false, 512, JSON_THROW_ON_ERROR);

        $this->emit('geoJsonUpdated');
    }

    public function setPercent($percent)
    {
        $this->currentPercentage = $percent;
        $this->executeMapshaper($percent);
    }

    public function render()
    {
        return view('livewire.geo-json.standalone-helper-view')->layout('layouts.guest');
    }
}
