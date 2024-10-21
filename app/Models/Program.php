<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Program extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    public function supervisors()
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'teams')->withPivot('role');
    }

    //UUID boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }
}
