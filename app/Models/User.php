<?php

namespace App\Models;

use App\Support\AvatarHelper;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'profile_image', 'nickname', 'volunteer_status', 'city', 'phone',
    ];

    protected $appends = ['profile_image_url', 'has_profile_image', 'avatar_initials', 'avatar_color'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function organization()
    {
        return $this->hasOne(Organization::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function refundLogs()
    {
        return $this->hasMany(RefundLog::class, 'admin_id');
    }

    public function isOrganization(): bool
    {
        return $this->role === 'organization';
    }

    public function hasProfileImage(): bool
    {
        return \App\Support\StorageImage::exists($this->profile_image);
    }

    public function getHasProfileImageAttribute(): bool
    {
        return $this->hasProfileImage();
    }

    public function getProfileImageUrlAttribute(): ?string
    {
        return \App\Support\StorageImage::url($this->profile_image);
    }

    public function getAvatarInitialsAttribute(): string
    {
        return AvatarHelper::initials($this->name);
    }

    public function getAvatarColorAttribute(): string
    {
        return AvatarHelper::colorFor($this->email ?: $this->name);
    }

    public function allRegistrations()
    {
        return EventRegistration::whereIn(
            'participant_id',
            $this->participants()->pluck('id')
        );
    }
}
