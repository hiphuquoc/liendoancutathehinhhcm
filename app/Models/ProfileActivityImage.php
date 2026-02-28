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

    /**
     * Full URL ảnh (Google Cloud).
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return '';
        }
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        $domain = config('main_'.env('APP_NAME').'.google_cloud_storage.default_domain', 'https://liendoancutathehinhhcm.storage.googleapis.com/');
        return rtrim($domain, '/') . '/' . ltrim($this->image, '/');
    }
}
