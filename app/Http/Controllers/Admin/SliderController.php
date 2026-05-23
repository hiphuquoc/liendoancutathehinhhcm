<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic;
use App\Models\SystemFile;
use App\Models\Slider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SliderController extends Controller {

    /**
     * Danh sách slider
     */
    public static function list(Request $request)
    {
        $params = [];
        if (!empty($request->get('search_name'))) {
            $params['search_name'] = $request->get('search_name');
        }
        
        // Lấy số item mỗi trang từ session hoặc mặc định
        $viewPerPage = session('viewSlider', 20);
        if ($request->has('viewPerPage')) {
            $viewPerPage = (int) $request->get('viewPerPage');
            session(['viewSlider' => $viewPerPage]);
        }
        
        // Query với search và pagination
        $query = Slider::select('*');
        
        if (!empty($params['search_name'])) {
            $query->where(function($q) use ($params) {
                $q->where('title', 'like', '%' . $params['search_name'] . '%')
                  ->orWhere('description', 'like', '%' . $params['search_name'] . '%');
            });
        }
        
        $list = $query->orderBy('ordering', 'ASC')
            ->orderBy('id', 'DESC')
            ->paginate($viewPerPage);
        
        // Statistics
        $statistics = [
            'total' => Slider::count(),
            'active' => Slider::where('flag_show', 1)->count(),
            'hidden' => Slider::where('flag_show', '!=', 1)->orWhereNull('flag_show')->count(),
        ];
            
        return view('admin.slider.list', compact('list', 'params', 'viewPerPage', 'statistics'));
    }

    /**
     * Form thêm/sửa slider
     */
    public static function view(Request $request)
    {
        $message = $request->get('message') ?? null;
        $id = $request->get('id') ?? 0;
        $type = $request->get('type') ?? 'create';
        
        $item = null;
        if ($id > 0) {
            $item = Slider::getById($id);
            if (empty($item)) {
                return redirect()->route('admin.slider.list');
            }
            $type = 'edit';
        }
        
        return view('admin.slider.view', compact('item', 'type', 'message'));
    }

    /**
     * Tạo mới hoặc cập nhật slider
     */
    public function createAndUpdate(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $id = $request->get('id') ?? 0;
            $type = $request->get('type') ?? 'create';
            
            $data = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'position' => $request->get('position') ?? 'left',
                'button_text' => $request->get('button_text'),
                'button_icon' => $request->get('button_icon'),
                'button_link' => $request->get('button_link'),
                'ordering' => $request->get('ordering') ?? 0,
                'flag_show' => $request->has('flag_show') ? 1 : 0,
                'notes' => $request->get('notes'),
            ];
            
            // Upload ảnh desktop
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadSliderImage($request->file('image'), $request->get('title'), 'desktop');
            }
            
            // Upload ảnh mobile
            if ($request->hasFile('image_mobile')) {
                $data['image_mobile'] = $this->uploadSliderImage($request->file('image_mobile'), $request->get('title'), 'mobile');
            }
            
            if ($type == 'edit' && $id > 0) {
                Slider::updateItem($id, $data);
                $message = 'Cập nhật slider thành công!';
            } else {
                $id = Slider::insertItem($data);
                $message = 'Thêm slider thành công!';
            }
            
            DB::commit();
            
            return redirect()->route('admin.slider.view', ['id' => $id])
                ->with('message', ['type' => 'success', 'message' => $message]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload slider image
     */
    private function uploadSliderImage($image, $title, $suffix = 'desktop')
    {
        $folderUpload = config('image.folder_upload');
        $extension = config('image.extension');
        $name = !empty($title) ? \Str::slug($title) : time();
        $filename = $name . '-slider-' . $suffix . '-' . time();
        $filepath = $folderUpload . $filename . '.' . $extension;
        
        $img = ImageManagerStatic::make($image->getRealPath())
            ->encode($extension, config('image.quality'));

        Storage::disk('gcs')->put($filepath, (string)$img, 'public');
        
        return Storage::disk('gcs')->url($filepath);
    }

    /**
     * Xóa slider
     */
    public static function delete(Request $request)
    {
        $id = $request->get('id') ?? 0;
        if ($id > 0) {
            $flag = Slider::deleteItem($id);
            return $flag;
        }
        return false;
    }

    /**
     * ===========================================
     * Legacy methods (for backward compatibility)
     * ===========================================
     */

    public static function upload($arrayImage, $params = null){
        $result     = [];
        if(!empty($arrayImage)){
            // ===== folder upload
            $folderUpload       = config('image.folder_upload');
            $extension          = config('image.extension');
            $name               = $params['name'] ?? time();
            $i                  = 0;
            foreach($arrayImage as $image){
                // ===== set filename & checkexists (Small)
                $filename       = $name.'-slider-'.time().'-'.$i;
                $filepath       = $folderUpload.$filename.'.'.$extension;
                ImageManagerStatic::make($image->getRealPath())
                    ->encode($extension, config('image.quality'))
                    ->save(Storage::path($filepath));
                $result[$i]['file_url']         = Storage::url($filepath);
                /* cập nhật thông tin CSDL */
                $arrayInsert                    = [];
                $arrayInsert['attachment_id']   = $params['attachment_id'] ?? 0;
                $arrayInsert['relation_table']  = $params['relation_table'] ?? null;
                $arrayInsert['file_name']       = $filename;
                $arrayInsert['file_path']       = $filepath;
                $arrayInsert['file_extension']  = $extension;
                $arrayInsert['file_type']       = 'slider';
                $idInsert                       = SystemFile::insertItem($arrayInsert);
                $result[$i]['file_id']          = $idInsert;
                ++$i;
            }
        }
        return $result;
    }

    public static function remove(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                /* xóa file */
                $infofile   = SystemFile::find($request->get('id'));
                $filePath   = Storage::path($infofile['file_path']);
                if(file_exists($filePath)) @unlink($filePath);
                /* xóa khỏi CSDL */
                $flag       = SystemFile::removeItem($request->get('id'));
                DB::commit();
                return $flag;
            } catch(\Exception $exception) {
                DB::rollBack();
                return false;
            }
        }
    }

    public static function removeById($id){
        if(!empty($id)){
            try {
                DB::beginTransaction();
                /* xóa file */
                $infofile   = SystemFile::find($id);
                $filePath   = Storage::path($infofile['file_path']);
                if(file_exists($filePath)) @unlink($filePath);
                /* xóa khỏi CSDL */
                $flag       = SystemFile::removeItem($id);
                DB::commit();
                return $flag;
            } catch(\Exception $exception) {
                DB::rollBack();
                return false;
            }
        }
    }

}
