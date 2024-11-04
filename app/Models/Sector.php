<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Sector extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';
    public function supervisors()
    {
        return $this->hasMany(Supervisor::class);
    }
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }
}
