<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileActivityImage extends Model
{
    protected $table = 'profile_activity_images';

    protected $fillable = [
        'owner_type',
        'owner_id',
        'image',
        'ordering',
    ];

    public $timestamps = true;

    const OWNER_TYPE_TRAINER = 'trainer_info';

    const OWNER_TYPE_REFEREE = 'referee_info';

    const OWNER_TYPE_ATHLETE = 'athlete_info';

    /**
     * Full URL ảnh (Google Cloud) — theo GCS_PUBLIC_URL / config dự án.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return '';
        }
        return (string) \App\Helpers\Image::getUrlImageCloud($this->image);
    }
}
