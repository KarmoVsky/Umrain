<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = [
        'state_id',
        'name'
    ];
    public $timestamps = false;

    public function state()
    {
        return $this->belongsTo(State::class);
    }
    
}
