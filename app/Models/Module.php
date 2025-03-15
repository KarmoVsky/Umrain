<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $table = 'anisth_modules';
    protected $fillable = ['name', 'is_enabled', 'message'];
    protected $casts = [
        'message' => 'array',
    ];

    public function translate($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $messageKey = 'name_'.$locale;
        $this->message = json_decode($this->message, true);
        return $this->message[$messageKey];
    }
}
