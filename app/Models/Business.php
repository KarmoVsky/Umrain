<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $table = 'anisth_business';

    protected $fillable = [
        'business_name',
        'business_name_id',
        'status',
        'email',
        'email_verified_at',
        'phone',
        'country_code',
        'address',
        'address2',
        'country',
        'state',
        'city',
        'zip_code',
        'avatar_id',
        'create_user',
        'approved_by',
        'approved_time',
    ];
    public function user() {
        return $this->belongsTo(User::class, 'create_user')->withDefault();
    }
    public function approvedBy() {
        return $this->belongsTo(\App\User::class, 'approved_by')->withDefault();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'anisth_business_service', 'business_id', 'service_id');
    }

    public function service() {
        return $this->hasMany(Service::class);
    }

    public function businessRelations()
    {
        return $this->hasMany(\App\Models\BusinessRelation::class, 'business_id');
    }

}
