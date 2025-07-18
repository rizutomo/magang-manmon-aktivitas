<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class Report extends Model
{
    protected $fillable = [
        'task_id',
        'date',
        'description',
        'latitude', 
        'longitude',
        'modified_by'
    ];
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'status' => ReportStatus::class, // Menggunakan enum untuk casting
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function files()
    {
        return $this->hasMany(ReportFile::class);
    }

    public function modifiedBy()
    {

        return $this->belongsTo(User::class, 'modified_by');
    }

        public function photos()
    {
        return $this->hasMany(ReportPhoto::class);
    }


    //UUID boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid(); // Menghasilkan UUID saat entri baru dibuat
        });
    }
}
