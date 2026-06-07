<?php

namespace App\Models;

use App\Support\AvatarHelper;
use Illuminate\Database\Eloquent\Model;
use App\Support\StorageImage;

class Organization extends Model
{
    protected $table = 'organizations';

    protected $fillable = [
        'user_id', 'organization_category_id',
        'org_name', 'description', 'image',
        'phone', 'email', 'address',
    ];

    protected $appends = ['image_url', 'has_stored_image', 'avatar_initials', 'avatar_color'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(OrganizationCategory::class, 'organization_category_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function hasStoredImage(): bool
    {
        return StorageImage::exists($this->image);
    }

    public function getHasStoredImageAttribute(): bool
    {
        return $this->hasStoredImage();
    }

    public function getImageUrlAttribute(): ?string
    {
        return StorageImage::url($this->image);
    }

    public function getAvatarInitialsAttribute(): string
    {
        return AvatarHelper::initials($this->org_name);
    }

    public function getAvatarColorAttribute(): string
    {
        return AvatarHelper::colorFor($this->org_name);
    }

    public function getLogoUrlAttribute(): string
    {
        return config('volunteerhub.logo_url');
    }

    public function getActiveEventsCountAttribute(): int
    {
        return $this->events()
            ->where('start_date', '>=', now())
            ->count();
    }
}
