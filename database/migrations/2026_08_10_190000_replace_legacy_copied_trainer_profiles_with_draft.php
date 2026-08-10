<?php

use App\Support\TrainerDraftProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * Ghi đè hồ sơ HLV vẫn đang giữ dữ liệu mẫu cũ (copy từ cao-quoc-viet)
 * bằng dữ liệu nháp [NHÁP] đầy đủ các cấp.
 *
 * Nhận diện: còn thành tích "Giải Khuyến khích Cuộc thi Thể hình Toàn tỉnh Lâm Đồng 2006..."
 * và kỹ năng "Huấn luyện GYM & Pilates". Không đụng HLV gốc cao-quoc-viet.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $trainerIds = TrainerDraftProfile::findTrainerIdsStillUsingLegacySample();

        $updated = 0;
        $failed = 0;

        foreach ($trainerIds as $trainerId) {
            try {
                if (TrainerDraftProfile::applyToTrainer((int) $trainerId)) {
                    $updated++;
                } else {
                    $failed++;
                }
            } catch (\Throwable $e) {
                $failed++;
                Log::error("Replace legacy trainer draft profile failed for trainer #{$trainerId}: " . $e->getMessage());
            }
        }

        Log::info("Replace legacy trainer draft profile: updated={$updated}, failed={$failed}, matched=" . $trainerIds->count());
    }

    /**
     * Reverse the migrations.
     *
     * Không khôi phục dữ liệu mẫu cũ (tránh tái tạo trùng hồ sơ HLV thật).
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
