<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Uuid;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
// use App\Notifications\ResetPasswordNotification as resetpwnotif;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public $incrementing = false;

    protected $keyType = 'string';

    

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'teams')->withPivot('role');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'reports')->withPivot('photo', 'description', 'longitude', 'latitude', 'date', 'status');
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class)->with('sector');
    }

    // public function sendPasswordResetNotification($token)
    // {
    //     $this->notify(new resetpwnotif($token));
    // }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }
}
