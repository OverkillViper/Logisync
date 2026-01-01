<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Models\Employee;
use App\Models\ProjectComment;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factory_recovery_codes',
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

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function comments()
    {
        return $this->hasMany(ProjectComment::class);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            Employee::create([
                'user_id'                    => $user->id,
                'employee_id'                => null     ,
                'profile_picture'            => null     ,
                'joining_date'               => null     ,
                'contact_number'             => null     ,
                'emergency_contact_number'   => null     ,
                'emergency_contact_relation' => null     ,
                'designation'                => null     ,
                'role'                       => 'trainee',
            ]);
        });
    }

}
