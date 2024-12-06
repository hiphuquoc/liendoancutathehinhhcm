<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SettingController;
use App\Models\Seo;
use Illuminate\Support\Str;

class Url {

    public static function checkUrlExists($slug){
        $infoPage           = new \Illuminate\Database\Eloquent\Collection;
        /* check ngôn ngữ Việt */
        $infoPage           = Seo::select('*')
                                ->where('slug', $slug)
                                ->first();
        return $infoPage;
    }

    public static function buildBreadcrumb($slugFull){
        $tmp            = explode('/', $slugFull);
        $result         = new \Illuminate\Database\Eloquent\Collection;
        foreach($tmp as $item){
            $infoItem   = Seo::select('*')
                                ->where('slug', $item)
                                ->first();
            if(empty($infoItem)) return null;
            $result[]   = $infoItem;
        }
        return $result;
    }

    public static function getSlugCurrent() {
        // $currentUrl = request()->url();
        // $unicodeSlug = Str::slug(parse_url($currentUrl, PHP_URL_PATH));
        $unicodeSlug = urldecode(request()->path());
        return $unicodeSlug;
    }
}