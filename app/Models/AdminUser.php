<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AdminUser extends Authenticatable
{
    use CanResetPassword, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role_id',
        'avatar',
        'is_active',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the email address used for password resets.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->email;
    }

    /**
     * Relations
     */
    public function createdNews()
    {
        return $this->hasMany(\App\Models\News::class, 'creator_id');
    }

    /**
     * Get the role that owns the admin user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the admin has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role->slug === $roleName;
    }

    /**
     * Get all points of interest created by the admin.
     */
    /* public function pointsOfInterest()
    {
        return $this->hasMany(PointOfInterest::class, 'creator_id');
    } */

    /**
     * Get all events created by the admin.
     */
    /* public function events()
    {
        return $this->hasMany(Event::class, 'creator_id');
    } */

    /**
     * Get all news articles created by the admin.
     */
    /* public function news()
    {
        return $this->hasMany(News::class, 'creator_id');
    } */

    /**
     * Get all review replies created by the admin.
     */
    /* public function reviewReplies()
    {
        return $this->hasMany(ReviewReply::class, 'admin_id');
    } */

    /**
     * Record the last login timestamp.
     */
    public function recordLogin(): void
    {
        $this->last_login_at = now();
        $this->save();
    }
}
