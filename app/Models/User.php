<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verification_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Generar token de verificación
     */
    public function generateVerificationToken()
    {
        $this->email_verification_token = \Str::random(60);
        $this->save();

        return $this->email_verification_token;
    }

    /**
     * Verificar email con token
     */
    public function verifyEmail($token)
    {
        if ($this->email_verification_token === $token) {
            $this->email_verified_at = now();
            $this->email_verification_token = null;
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Verificar si el usuario tiene email verificado
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
