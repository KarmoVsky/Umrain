<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'anisth_services';

    protected $fillable = [
        'name',
        'status',
        'business_id',
        'service_id',
    ];

    public function business() {
        return $this->belongsToMany(Business::class, 'anisth_business_service', 'service_id', 'business_id');
    }
}
