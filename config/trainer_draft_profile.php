<?php

/**
 * Hồ sơ nháp đầy đủ cho HLV khi import Excel.
 * Nội dung cố ý đánh dấu [NHÁP] để HLV/người dùng nhận biết cần cập nhật thật.
 */
return [
    'description' => '[NHÁP] Đây là phần giới thiệu mẫu. Vui lòng cập nhật đoạn giới thiệu về bản thân, chuyên môn và định hướng nghề nghiệp của bạn.',
    'seo_description' => '[NHÁP] Đây là mô tả SEO mẫu. Vui lòng cập nhật mô tả giới thiệu ngắn gọn về huấn luyện viên.',
    'position' => 'Huấn luyện viên cá nhân (PT)',
    'area' => '[NHÁP] Cập nhật khu vực hoạt động (ví dụ: Quận 1, TP.HCM)',
    'years_experience' => 0,
    'languages' => ['Tiếng Việt'],
    'total_learner' => 0,
    'total_teaching_hour' => 0,
    'total_prize' => 0,

    'achievements' => [
        ['content' => '[NHÁP] Thành tích 1 — Ví dụ: Giải nhất giải thể hình cấp tỉnh năm … (thay bằng thành tích thật của bạn).'],
        ['content' => '[NHÁP] Thành tích 2 — Ví dụ: Top 3 cuộc thi thể hình / fitness khu vực …'],
        ['content' => '[NHÁP] Thành tích 3 — Ví dụ: Chứng nhận HLV quốc gia / quốc tế …'],
        ['content' => '[NHÁP] Thành tích 4 — Ví dụ: Huấn luyện viên xuất sắc tại đơn vị …'],
        ['content' => '[NHÁP] Thành tích 5 — Ví dụ: Đóng góp chuyên môn / hội thảo / cộng đồng …'],
    ],

    'skills' => [
        ['skill' => '[NHÁP] Kỹ năng chuyên môn 1 (ví dụ: Huấn luyện GYM)', 'percent' => 50],
        ['skill' => '[NHÁP] Kỹ năng chuyên môn 2 (ví dụ: Huấn luyện thể hình)', 'percent' => 50],
        ['skill' => '[NHÁP] Kỹ năng chuyên môn 3 (ví dụ: Tư vấn dinh dưỡng)', 'percent' => 50],
        ['skill' => '[NHÁP] Kỹ năng mềm 1 (ví dụ: Giao tiếp với học viên)', 'percent' => 50],
        ['skill' => '[NHÁP] Kỹ năng mềm 2 (ví dụ: Lập giáo án tập luyện)', 'percent' => 50],
    ],

    'experiences' => [
        [
            'title' => '[NHÁP] Chức danh / Vai trò — (năm bắt đầu - năm kết thúc)',
            'company' => '[NHÁP] Tên đơn vị / phòng gym / tổ chức',
            'content' => "[NHÁP] Mô tả trách nhiệm chính trong vai trò này.\r\n[NHÁP] Thành quả nổi bật hoặc số lượng học viên đã hỗ trợ.\r\n[NHÁP] Kỹ năng / chuyên môn đã áp dụng tại đơn vị.",
        ],
        [
            'title' => '[NHÁP] Chức danh / Vai trò khác — (năm bắt đầu - năm kết thúc)',
            'company' => '[NHÁP] Tên đơn vị / phòng gym / tổ chức khác',
            'content' => "[NHÁP] Mô tả công việc và phạm vi phụ trách.\r\n[NHÁP] Các chương trình / lớp học đã triển khai.\r\n[NHÁP] Điểm nhấn trong quá trình làm việc.",
        ],
    ],

    'degrees' => [
        [
            'title' => '[NHÁP] Tên bằng cấp / chứng chỉ — (năm)',
            'school' => '[NHÁP] Tên trường / tổ chức cấp chứng chỉ',
            'content' => "[NHÁP] Chuyên ngành / hạng chứng chỉ.\r\n[NHÁP] Kỹ năng / kiến thức đạt được.",
        ],
        [
            'title' => '[NHÁP] Tên bằng cấp / chứng chỉ khác — (năm)',
            'school' => '[NHÁP] Tên trường / tổ chức cấp chứng chỉ khác',
            'content' => "[NHÁP] Chuyên ngành / hạng chứng chỉ.\r\n[NHÁP] Kỹ năng / kiến thức đạt được.",
        ],
    ],

    /**
     * Dấu hiệu nhận diện dữ liệu mẫu cũ (copy từ HLV cao-quoc-viet).
     * Dùng trong migration để ghi đè HLV chưa cập nhật hồ sơ thật.
     */
    'legacy_sample_fingerprints' => [
        'achievement' => 'Giải Khuyến khích Cuộc thi Thể hình Toàn tỉnh Lâm Đồng 2006 (Hạng cân 60kg).',
        'skill' => 'Huấn luyện GYM & Pilates',
        'exclude_slugs' => ['cao-quoc-viet'],
    ],
];
