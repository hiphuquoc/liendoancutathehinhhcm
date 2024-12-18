<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use App\Helpers\Url;
use App\Http\Controllers\CategoryMoneyController;
use App\Models\Blog;
use App\Models\Customer;
use App\Models\Page;
use App\Models\CategoryBlog;
use App\Models\Trainer;
use Illuminate\Support\Facades\Auth;


class RoutingController extends Controller{
    public function routing(Request $request, $slug, $slug2 = null, $slug3 = null, $slug4 = null, $slug5 = null, $slug6 = null, $slug7 = null, $slug8 = null, $slug9 = null, $slug10 = null){
        /* dùng request uri */
        $slug           = $request->path();
        // Giải mã các ký tự URL-encoded
        $decodedSlug    = urldecode($slug);
        $tmpSlug        = explode('/', $decodedSlug);
        /* loại bỏ phần tử rỗng */
        $arraySlug      = [];
        foreach($tmpSlug as $slug) if(!empty($slug)&&$slug!='public') $arraySlug[] = $slug;
        /* loại bỏ hashtag và request trước khi check */
        $arraySlug[count($arraySlug)-1] = preg_replace('#([\?|\#]+).*$#imsU', '', end($arraySlug));
        $urlRequest     = implode('/', $arraySlug);
        /* check url có tồn tại? => lấy thông tin */
        $itemSeo    = Url::checkUrlExists(end($arraySlug));
        /* nếu sai => redirect về link đúng */
        if(!empty($itemSeo->slug_full)&&$itemSeo->slug_full!=$urlRequest){
            /* ko rút gọn trên 1 dòng được => lỗi */
            return Redirect::to($itemSeo->slug_full, 301);
        }
        /* ============== nếu đúng => xuất dữ liệu */
        if(!empty($itemSeo->type)){
            /* ngôn ngữ */
            $language               = $itemSeo->language;
            SettingController::settingLanguage($language);
            /* đưa biến search lên để xử lý với cache */
            $search                 = request('search') ?? null;
            /* cache HTML */
            $paramsSlug             = [];
            if(!empty($search)) $paramsSlug['search'] = $search;
            $nameCache              = self::buildNameCache($itemSeo['slug_full'], $paramsSlug).'.'.config('main_'.env('APP_NAME').'.cache.extension');
            $pathCache              = Storage::path(config('main_'.env('APP_NAME').'.cache.folderSave')).$nameCache;
            $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
            $flagHandle             = true;
            if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
                $xhtml              = file_get_contents($pathCache);
                if(!empty(env('HTTP_URL'))&&strpos($xhtml, env('HTTP_URL'))==false) $flagHandle = false;
            }
            /* xử lý */
            if($flagHandle==false){
                echo $xhtml;
            }else {
                /* breadcrumb */
                $breadcrumb         = Url::buildBreadcrumb($itemSeo->slug_full);
                /* thông tin */
                $tableName          = $itemSeo->type;
                $modelName          = config('tablemysql.'.$itemSeo->type.'.model_name');
                $modelInstance      = resolve("\App\Models\\$modelName");
                $idSeo              = $itemSeo->id;
                $item               = $modelInstance::select('*')
                                        ->whereHas('seos', function($query) use($idSeo){
                                            $query->where('seo_id', $idSeo);
                                        })
                                        ->with('seo', 'seos')
                                        ->first();
                $flagMatch          = false;
                /* ===== Trang ==== */
                if($itemSeo->type=='page_info'){
                    $flagMatch          = true;
                    $item               = Page::select('*')
                                            ->whereHas('seos.infoSeo', function($query) use($idSeo){
                                                $query->where('id', $idSeo);
                                            })
                                            ->with('type')
                                            ->first();
                    if(!empty($item->type->code)&&$item->type->code=='my_download'&&!empty(Auth::user()->email)){
                        $emailCustomer  = Auth::user()->email;
                        $infoCustomer   = Customer::select('*')
                                            ->where('email', $emailCustomer)
                                            ->with('orders')
                                            ->first();
                        /* trang tài khoản */
                        $xhtml  = view('wallpaper.account.myDownload', compact('item', 'itemSeo', 'infoCustomer', 'language', 'breadcrumb'))->render();
                    }else {
                        /* trang bình thường */
                        $xhtml  = view('wallpaper.page.index', compact('item', 'itemSeo', 'language', 'breadcrumb'))->render();
                    }                    
                }
                /* ===== Category Blog ==== */
                if($itemSeo->type=='category_blog'){
                    $flagMatch          = true;
                    /* thông tin trang category blog */
                    $item               = CategoryBlog::select('*')
                                            ->whereHas('seos.infoSeo', function($query) use($idSeo){
                                                $query->where('id', $idSeo);
                                            })
                                            ->with('seo', 'seos')
                                            ->first();
                    /* tạo mảng để lấy blogs */
                    $params             = [];
                    if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
                    $params['request_load'] = 10;
                    $params['sort_by']  = Cookie::get('sort_by') ?? null;
                    $categoryTree       = CategoryBlog::getTreeCategoryByInfoCategory($item, []);
                    $arrayIdCategory    = [$item->id];
                    foreach($categoryTree as $t) $arrayIdCategory[] = $t->id;
                    $params['array_category_blog_id'] = $arrayIdCategory;
                    $blogs              = \App\Http\Controllers\CategoryBlogController::getBlogs($params, $language);
                    /* lấy danh sách chuyên mục level 2 */
                    $categoriesLv2      = CategoryBlog::select('*')
                                            ->whereHas('seos.infoSeo', function ($query) use ($language) {
                                                $query->where('level', 2);
                                            })
                                            ->get();
                    /* blog nổi bật - sidebar */
                    $blogFeatured       = BlogController::getBlogFeatured($language);
                    $xhtml              = view('wallpaper.categoryBlog.index', compact('item', 'itemSeo', 'blogs', 'blogFeatured', 'categoriesLv2', 'language', 'breadcrumb'))->render();
                }
                /* ===== Blog ==== */
                if($itemSeo->type=='blog_info'){
                    $flagMatch          = true;
                    /* thông tin trang category blog */
                    $item               = Blog::select('*')
                                            ->whereHas('seos.infoSeo', function($query) use($idSeo){
                                                $query->where('id', $idSeo);
                                            })
                                            ->with('seo', 'seos.infoSeo.contents')
                                            ->first();
                    /* blog nổi bật - sidebar */
                    $blogFeatured       = BlogController::getBlogFeatured($language);
                    /* xây dựng toc_content */
                    $htmlContent        = '';
                    foreach($itemSeo->contents as $content) $htmlContent .= $content->content;
                    $dataContent        = CategoryMoneyController::buildTocContentMain($htmlContent, $language);
                    $htmlContent        = str_replace('<div id="tocContentMain"></div>', '<div id="tocContentMain">'.$dataContent['toc_content'].'</div>', $dataContent['content']);
                    $xhtml              = view('wallpaper.blog.index', compact('item', 'itemSeo', 'blogFeatured', 'language', 'breadcrumb', 'htmlContent'))->render();
                }
                /* ===== Trainer ==== */
                if($itemSeo->type=='trainer_info'){
                    $flagMatch          = true;
                    /* thông tin trang */
                    $item               = Trainer::select('*')
                                            ->whereHas('seos.infoSeo', function($query) use($idSeo){
                                                $query->where('id', $idSeo);
                                            })
                                            ->with('seo', 'seos.infoSeo.contents')
                                            ->first();
                    $xhtml              = view('wallpaper.teacherDetail.index', compact('item', 'itemSeo', 'language', 'breadcrumb'))->render();
                }
                /* Ghi dữ liệu - Xuất kết quả */
                if($flagMatch==true){
                    if(env('APP_CACHE_HTML')==true) Storage::put(config('main_'.env('APP_NAME').'.cache.folderSave').$nameCache, $xhtml);
                    echo $xhtml;
                }else {
                    return \App\Http\Controllers\ErrorController::error404();
                }
            }
            // return false;
        }else {
            return \App\Http\Controllers\ErrorController::error404();
        }
    }

    public static function buildNameCache($slugFull, $params = []){
        $response     = '';
        if(!empty($slugFull)){
             /* xây dựng  slug */
             $tmp    = explode('/', $slugFull);
             $result = [];
             foreach($tmp as $t) if(!empty($t)) $result[] = $t;
             $response = implode('-', $result);
            /* duyệt params để lấy prefix hay # */
            if(!empty($params)){
                $part   = '';
                foreach($params as $key => $param) $part .= $key.'-'.$param;
                if(!empty($part)) $response = $response.'-'.$part;
            }
        }
        return $response;
    }
}
