<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Modules\User\Models\Role;

class BusinessRelation extends Model
{
    use HasFactory;

    protected $table = 'anisth_business_relations';

    protected $fillable = [
        'business_id',
        'role_id',
        'user_id',
        'service_id',
        'service_type',
        'sub_region',
        'country',
        'start_date',
        'end_date',
        'status',
    ];

    public function business() {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function service() {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function hotel() {
        if($this->service_type === 'hotel') {
            return $this->belongsTo(\modules\Hotel\Models\Hotel::class, 'service_id');
        }
        return null;
    }

    public function vendor() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeValid(Builder $query, int $business_id)
    {
        $query->where('service_type', 'hotel')
        ->where('business_id', $business_id);
    }

    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
