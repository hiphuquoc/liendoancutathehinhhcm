<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use App\Http\Requests\BlogRequest;
use App\Helpers\Upload;
use App\Models\CategoryBlog;
use App\Models\Document;
use App\Models\Seo;
use App\Models\RelationSeoDocumentInfo;
use App\Models\Prompt;
use App\Models\Page;
use App\Models\Tag;
use App\Models\Product;
use App\Helpers\Image;
use App\Services\BuildInsertUpdateModel;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;

class DocumentController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function list(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* paginate */
        $viewPerPage        = Cookie::get('viewDocumentInfo') ?? 50;
        $params['paginate'] = $viewPerPage;
        $list               = Document::getList($params);
        return view('admin.document.list', compact('list', 'params', 'viewPerPage'));
    }

    public function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = $request->get('language') ?? null;
        /* kiểm tra xem ngôn ngữ có nằm trong danh sách không */
        $flagView           = false;
        foreach(config('language') as $ld){
            if($ld['key']==$language) {
                $flagView   = true;
                break;
            }
        }
        /* tìm theo ngôn ngữ */
        $item               = Document::select('*')
                                ->where('id', $id)
                                ->with('seo', 'seos')
                                ->first();
        if(empty($item)) $flagView = false;
        if($flagView==true){
            /* lấy item seo theo ngôn ngữ được chọn */
            $itemSeo            = [];
            if(!empty($item->seos)){
                foreach($item->seos as $s){
                    if($s->infoSeo->language==$language) {
                        $itemSeo = $s->infoSeo;
                        break;
                    }
                }
            }
            /* prompts */
            $prompts            = Prompt::select('*')
                                    ->where('reference_table', 'document_info')
                                    ->get();
            /* type */
            $type               = !empty($itemSeo) ? 'edit' : 'create';
            $type               = $request->get('type') ?? $type;
            /* trang cha */
            $parents            = Page::all();
            /* category cha */
            return view('admin.document.view', compact('item', 'itemSeo', 'prompts', 'type', 'language', 'parents', 'message'));
        } else {
            return redirect()->route('admin.document.list');
        }
    }

    public function createAndUpdate(Request $request){
        try {
            DB::beginTransaction();
            /* ngôn ngữ */
            $idSeo              = $request->get('seo_id');
            $idSeoVI            = $request->get('seo_id_vi') ?? 0;
            $idBlog             = $request->get('document_info_id');
            $language           = $request->get('language');
            $typePage           = 'document_info';
            $type               = $request->get('type');
            /* check xem là create seo hay update seo */
            $action             = !empty($idSeo)&&$type=='edit' ? 'edit' : 'create';
            /* upload image & document */
            $dataPath           = [];
            $name               = !empty($request->get('slug')) ? $request->get('slug') : time();
            if($request->hasFile('image')) {
                $fileName       = $name.'.'.config('image.extension');
                $folderUpload   =  config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers');
                $dataPath       = Upload::uploadWallpaper($request->file('image'), $fileName, $folderUpload);
            }
            if($request->hasFile('document')){
                $fileName       = $name.'.pdf';
                $folderUpload   =  config('main_'.env('APP_NAME').'.google_cloud_storage.documents');
                $dataPathFile   = Upload::uploadDocument($request->file('document'), $fileName, $folderUpload);
            }
            /* update page */
            $seo                = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $typePage, $dataPath);
            if($action=='edit'){
                Seo::updateItem($idSeo, $seo);
            }else {
                $idSeo = Seo::insertItem($seo, $idSeoVI);
            }
            /* kiểm tra insert thành công không */
            if(!empty($idSeo)){
                /* insert seo_content */
                if(!empty($request->get('content'))) CategoryController::insertAndUpdateContents($idSeo, $request->get('content'));
                if($language=='vi'){
                    /* insert hoặc update document_info */
                    $status           = !empty($request->get('status'))&&$request->get('status')=='on' ? 1 : 0;
                    $outstanding      = !empty($request->get('outstanding'))&&$request->get('outstanding')=='on' ? 1 : 0;
                    if(empty($idBlog)){ /* check xem create category hay update category */
                        $idBlog          = Document::insertItem([
                            'status'        => $status,
                            'outstanding'   => $outstanding,
                            'seo_id'        => $idSeo,
                            'file_cloud'    => $dataPathFile,
                        ]);
                    }else {
                        $dataUpdate     = [
                            'status'        => $status,
                            'outstanding'   => $outstanding,
                        ];
                        if(!empty($dataPathFile)){
                            $dataUpdate['file_cloud'] = $dataPathFile;
                        }
                        Document::updateItem($idBlog, $dataUpdate);
                    }
                }
                /* relation_seo_document_info */
                $relationSeoDocumentInfo = RelationSeoDocumentInfo::select('*')
                                        ->where('seo_id', $idSeo)
                                        ->where('document_info_id', $idBlog)
                                        ->first();
                if(empty($relationSeoDocumentInfo)) RelationSeoDocumentInfo::insertItem([
                    'seo_id'        => $idSeo,
                    'document_info_id'   => $idBlog
                ]);
                DB::commit();
                /* Message */
                $message        = [
                    'type'      => 'success',
                    'message'   => '<strong>Thành công!</strong> Đã cập nhật Bài Viết!'
                ];
                /* nếu có tùy chọn index => gửi google index */
                if(!empty($request->get('index_google'))&&$request->get('index_google')=='on') {
                    $flagIndex = IndexController::indexUrl($idSeo);
                    if($flagIndex==200){
                        $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Bài Viết và Báo Google Index!';
                    }else {
                        $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Bài Viết <span style="color:red;">nhưng báo Google Index lỗi</span>';
                    }
                }
            }
        } catch (\Exception $exception){
            DB::rollBack();
        }
        /* có lỗi mặc định Message */
        if(empty($message)){
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.document.view', ['id' => $idBlog, 'language' => $language]);
    }

    public function delete(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Document::select('*')
                                ->where('id', $id)
                                ->with('seo', 'seos')
                                ->first();
                /* xóa ảnh đại diện trên google_clouds */ 
                if(!empty($info->seo->image)) Upload::deleteWallpaper($info->seo->image);
                /* xóa file tài liệu trên google_clouds */ 
                if(!empty($info->file_cloud)) Upload::deleteWallpaper($info->file_cloud);
                /* delete các trang seos ngôn ngữ */
                foreach($info->seos as $s){
                    /* xóa ảnh đại diện trên google_clouds */ 
                    if(!empty($s->infoSeo->image)) Upload::deleteWallpaper($s->infoSeo->image);
                    if(!empty($s->infoSeo->contents)) foreach($s->infoSeo->contents as $c) $c->delete();
                    $s->infoSeo()->delete();
                    $s->delete();
                }
                $info->delete();
                DB::commit();
                return true;
            } catch (\Exception $exception){
                DB::rollBack();
                return false;
            }
        }
    }
}
