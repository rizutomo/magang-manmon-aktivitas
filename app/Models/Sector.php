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
        return $this->hasOne(Supervisor::class);
    }
    public function programs()
    {
        return $this->hasMany(Program::class);
    }
    public function occupations()
    {
        return $this->hasMany(Occupation::class);
    }
    public function users()
    {
        return $this->hasManyThrough(User::class,Occupation::class);
    }
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }
}
