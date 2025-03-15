<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
    ];
    public $timestamps = false;
    public $incrementing = false;
    
    public function states()
    {
        return $this->hasMany(State::class);
    }
}
