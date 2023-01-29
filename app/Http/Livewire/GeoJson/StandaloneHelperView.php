<?php

namespace App\Http\Livewire\GeoJson;

use App\Models\CommunityModel;
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
    }

    public function submit()
    {
        $this->validate();
        $response = Http::acceptJson()
                        ->get(
                            'https://nominatim.openstreetmap.org/search?city='.$this->search.'&format=json&polygon_geojson=1'
                        );
        $this->osmSearchResultsCity = $response->json();

        $response = Http::acceptJson()
                        ->get(
                            'https://nominatim.openstreetmap.org/search?state='.$this->search.'&format=json&polygon_geojson=1'
                        );
        $this->osmSearchResultsState = $response->json();

        $response = Http::acceptJson()
                        ->get(
                            'https://nominatim.openstreetmap.org/search?country='.$this->search.'&format=json&polygon_geojson=1'
                        );
        $this->osmSearchResultsCountry = $response->json();
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
        Storage::disk('geo')
               ->put('geojson_'.$this->selectedItem['osm_id'].'.json',
                   json_encode($this->selectedItem['geojson'], JSON_THROW_ON_ERROR));
        $input = storage_path('app/geo/geojson_'.$this->selectedItem['osm_id'].'.json');
        $output = storage_path('app/geo/output_'.$this->selectedItem['osm_id'].'.json');
        $mapshaperBinary = base_path('node_modules/mapshaper/bin/mapshaper');
        exec($mapshaperBinary.' '.$input.' -simplify dp 4% -o '.$output);
        Storage::disk('geo')
               ->put(
                   'trimmed_'.$this->selectedItem['osm_id'].'.json',
                   str(Storage::disk('geo')
                              ->get('output_'.$this->selectedItem['osm_id'].'.json'))
                       ->after('{"type":"GeometryCollection", "geometries": [')
                       ->beforeLast(']}')
                       ->toString()
               );
        $this->model->osm_relation = $this->selectedItem;
        $this->model->simplified_geojson = json_decode(trim(Storage::disk('geo')
                                                                   ->get('trimmed_'.$this->selectedItem['osm_id'].'.json')),
            false, 512, JSON_THROW_ON_ERROR);
        $this->model->population = 0;
        $this->model->population_date = '2021-12-31';
    }

    public function setPercent($percent)
    {
        $input = storage_path('app/geo/geojson_'.$this->selectedItem['osm_id'].'.json');
        $output = storage_path('app/geo/output_'.$this->selectedItem['osm_id'].'.json');
        $mapshaperBinary = base_path('node_modules/mapshaper/bin/mapshaper');
        exec($mapshaperBinary.' '.$input.' -simplify dp '.$percent.'% -o '.$output);
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

    public function render()
    {
        return view('livewire.geo-json.standalone-helper-view')->layout('layouts.guest');
    }
}
