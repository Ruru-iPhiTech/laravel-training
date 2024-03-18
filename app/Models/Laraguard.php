<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmail;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Laraguard extends Model implements MustVerifyEmail
{
    use HasApiTokens,
        HasFactory,
        HasRoles,
        Impersonate,
        MustVerifyEmailTrait,
        Notifiable,
        SoftDeletes;

    // Add any other traits you need

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // Fillable attributes here
        'shared_secret',
        'enabled_at',
        'label',
        'digits',
        'seconds',
        'window',
        'algorithm',
        'recovery_codes',
        'recovery_codes_generated_at',
        'safe_devices',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        // Hidden attributes here
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // Casts here
        'recovery_codes' => 'array',
        'safe_devices' => 'array',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        // Appends here
        'avatar',
    ];

    /**
     * Eager load relationships.
     *
     * @var array
     */
    protected $with = [
        // Eager loading relationships here
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the registration verification email.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Get the two-factor authentication record associated with the user.
     */
    public function laraguard(): MorphOne
    {
        return $this->morphOne(Laraguard::class, 'authenticatable');
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disableTwoFactorAuth(): void
    {
        // Retrieve the associated Laraguard record
        $laraguard = $this->laraguard()->first();

        // If a Laraguard record exists, delete it
        if ($laraguard !== null) {
            $laraguard->delete();
        }
    }
}
