<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityModel extends Model
{
    protected $casts = [
        'simplified_geojson' => 'json',
    ];
}
