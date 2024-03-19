<?php

namespace App\Domains\Auth\Models;

use App\Domains\Auth\Models\Traits\Attribute\UserAttribute;
use App\Domains\Auth\Models\Traits\Method\UserMethod;
use App\Domains\Auth\Models\Traits\Relationship\UserRelationship;
use App\Domains\Auth\Models\Traits\Scope\UserScope;
use App\Domains\Auth\Notifications\Frontend\ResetPasswordNotification;
use App\Domains\Auth\Notifications\Frontend\VerifyEmail;
use DarkGhostHunter\Laraguard\Contracts\TwoFactorAuthenticatable;
use DarkGhostHunter\Laraguard\TwoFactorAuthentication;
use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\Image\PngImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use App\Domains\Auth\Models\User;

/**
 * Class User.
 */
class User extends Authenticatable implements MustVerifyEmail, TwoFactorAuthenticatable
{
    use HasApiTokens,
        HasFactory,
        HasRoles,
        Impersonate,
        MustVerifyEmailTrait,
        Notifiable,
        SoftDeletes,
        TwoFactorAuthentication,
        UserAttribute,
        UserMethod,
        UserRelationship,
        UserScope;

    public const TYPE_ADMIN = 'admin';
    public const TYPE_USER = 'user';


    /**
     * Create a new instance of the User model with two-factor authentication enabled.
     *
     * @param  array  $attributes
     * @return static
     */
    public static function createTwoFactorAuth2(array $attributes = [])
    {
        $attributes['two_factor_secret'] = static::generateTwoFactorSecret();
        $attributes['two_factor_recovery_codes'] = static::generateTwoFactorRecoveryCodes();

        return static::create($attributes);
    }
     /**
     * Generate a new two-factor authentication secret.
     *
     * @return string
     */
    public static function generateTwoFactorSecret()
    {
        return \Illuminate\Support\Str::random(64); 
    }

      /**
     * Generate recovery codes for two-factor authentication.
     *
     * @return array
     */
    public static function generateTwoFactorRecoveryCodes()
    {
        $codes = [];

        for ($i = 0; $i < 6; $i++) {
            $codes[] = Str::random(8);
        }

        return $codes;
    }
public function toQr()
{
    if (!empty($this->email)) {
        $uri = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            urlencode(config('app.name')),
            urlencode($this->email),
            urlencode($this->two_factor_secret),
            urlencode(config('app.name'))
        );

        // Create a renderer style
        $rendererStyle = new RendererStyle(400);

        // Create an image renderer with the SVG image backend
        $renderer = new ImageRenderer(
            $rendererStyle,
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        return $writer->writeString($uri);
    } else {
        return null; // Or handle the case when email is null or empty
    }
}
    /**
     * Check if two-factor authentication is enabled for the user.
     *
     * @return bool
     */
    public function hasTwoFactorAuthenticationEnabled()
    {
        return !empty($this->two_factor_secret);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login_at', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'last_login_at',
        'email_verified_at',
        'password_changed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    
    /**
     * @var array
     */
    protected $appends = [
        'avatar',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'permissions',
        'roles',
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
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Return true or false if the user can impersonate an other user.
     *
     * @param void
     * @return bool
     */
    public function canImpersonate(): bool
    {
        return $this->can('admin.access.user.impersonate');
    }

    /**
     * Return true or false if the user can be impersonate.
     *
     * @param void
     * @return bool
     */
    public function canBeImpersonated(): bool
    {
        return ! $this->isMasterAdmin();
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
