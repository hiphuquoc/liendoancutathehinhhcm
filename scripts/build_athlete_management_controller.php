<?php

$src = __DIR__.'/../app/Http/Controllers/Admin/TrainerManagementController.php';
$dst = __DIR__.'/../app/Http/Controllers/Admin/AthleteManagementController.php';

$s = file_get_contents($src);

$replacements = [
    'TrainerManagementController' => 'AthleteManagementController',
    'TrainerManagement' => 'AthleteManagement',
    'admin.trainerManagement' => 'admin.athleteManagement',
    'TrainerController' => 'AthleteController',
    'TrainerRequest' => 'AthleteRequest',
    'use App\Models\Trainer;' => 'use App\Models\Athlete;',
    'Trainer::' => 'Athlete::',
    'trainer_info' => 'athlete_info',
    'trainer_code' => 'athlete_code',
    'repeater_trainer_' => 'repeater_athlete_',
    'generateTrainerCode' => 'generateAthleteCode',
    '/HLV-HWBF' => '/VDV-HWBF',
    'slug_trainer_parent' => 'slug_athlete_parent',
    "'trainer'" => "'athlete'",
    'Huấn luyện viên cá nhân (PT)' => 'Vận động viên',
    'admin.trainer.' => 'admin.athlete.',
    '(Huấn luyện viên)' => '(Vận động viên)',
    'Không thể tạo trainer' => 'Không thể tạo vận động viên',
    "\\App\Models\Trainer::where('user_id'" => "\\App\Models\Athlete::where('user_id'",
];

foreach ($replacements as $a => $b) {
    $s = str_replace($a, $b, $s);
}

$s = str_replace(
    "Huấn luyện viên {\$nameCover} của Liên Đoàn",
    "Vận động viên {\$nameCover} của Liên Đoàn",
    $s
);

$s = preg_replace(
    '/\$trainerExample = Athlete::whereHas\([^;]+;/s',
    '$athleteExample = Athlete::with(\'achievements\', \'skills\', \'experiences.contents\', \'degrees.contents\')->orderBy(\'id\')->first();',
    $s,
    1
);

$s = str_replace('$trainerExample', '$athleteExample', $s);
$s = str_replace('if ($trainerExample)', 'if ($athleteExample)', $s);
$s = str_replace('foreach ($trainerExample->', 'foreach ($athleteExample->', $s);

$s = str_replace('$trainersData', '$athletesData', $s);
$s = str_replace('trainersData', 'athletesData', $s);

$s = str_replace('// Lấy parent seo (huan-luyen-vien)', '// Lấy parent seo (van-dong-vien)', $s);
$s = str_replace(
    'throw new \\Exception(\'Không tìm thấy trang parent "huan-luyen-vien"\');',
    'throw new \\Exception(\'Không tìm thấy trang parent van-dong-vien\');',
    $s
);

file_put_contents($dst, $s);
echo "OK $dst\n";
