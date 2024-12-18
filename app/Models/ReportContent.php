<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportContent extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;


    protected $fillable = [
        'id',
        'report_id',
        'photo',
        'description',
        'longitude',
        'latitude',
        'date',
    ];


    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function files()
    {
        return $this->hasMany(ReportFile::class);
    }
}
