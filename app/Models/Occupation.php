<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Occupation extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }
}
