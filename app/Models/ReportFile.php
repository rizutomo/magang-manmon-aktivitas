<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ReportFile extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';
    protected $fillable = ['report_id', 'name', 'file_path'];

    public function report()
    {
        return $this->belongsTo(Supervisor::class);
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
