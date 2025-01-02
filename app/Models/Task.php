<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Task extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_teams');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    public function report()
    {
        return $this->hasOne(Report::class);
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
