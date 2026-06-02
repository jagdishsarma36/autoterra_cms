<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'company',
        'address', 'country', 'avatar', 'google_id', 'microsoft_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function licenseKeys()
    {
        return $this->hasMany(LicenseKey::class);
    }

    public static function findOrCreateFromSocial(SocialiteUser $socialUser, string $provider): self
    {
        $field = $provider . '_id';
        $user = static::where($field, $socialUser->getId())->first();

        if (!$user) {
            $user = static::where('email', $socialUser->getEmail())->first();
            if ($user) {
                $user->update([$field => $socialUser->getId()]);
            } else {
                $user = static::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    $field => $socialUser->getId(),
                ]);
            }
        }

        return $user;
    }
}
