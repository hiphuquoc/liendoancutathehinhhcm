<?php

namespace App\Support;

use App\Helpers\SpokenLanguage;
use App\Models\Trainer;
use App\Models\TrainerAchievement;
use App\Models\TrainerDegree;
use App\Models\TrainerDegreeContent;
use App\Models\TrainerExperience;
use App\Models\TrainerExperienceContent;
use App\Models\TrainerSkill;
use Illuminate\Support\Facades\DB;

class TrainerDraftProfile
{
    public static function config(): array
    {
        return config('trainer_draft_profile', []);
    }

    /**
     * Payload dùng khi import Excel (map vào TrainerRequest / createAndUpdate).
     */
    public static function forImport(): array
    {
        $config = self::config();

        return [
            'description' => $config['description'] ?? null,
            'seo_description' => $config['seo_description'] ?? null,
            'position' => $config['position'] ?? 'Huấn luyện viên cá nhân (PT)',
            'area' => $config['area'] ?? null,
            'years_experience' => $config['years_experience'] ?? 0,
            'languages' => $config['languages'] ?? ['Tiếng Việt'],
            'total_learner' => $config['total_learner'] ?? 0,
            'total_teaching_hour' => $config['total_teaching_hour'] ?? 0,
            'total_prize' => $config['total_prize'] ?? 0,
            'repeater_trainer_achievement' => $config['achievements'] ?? [],
            'repeater_trainer_skill' => $config['skills'] ?? [],
            'repeater_trainer_experience' => $config['experiences'] ?? [],
            'repeater_trainer_degree' => $config['degrees'] ?? [],
        ];
    }

    public static function legacyAchievementFingerprint(): ?string
    {
        return self::config()['legacy_sample_fingerprints']['achievement'] ?? null;
    }

    public static function legacySkillFingerprint(): ?string
    {
        return self::config()['legacy_sample_fingerprints']['skill'] ?? null;
    }

    public static function excludeSlugs(): array
    {
        return self::config()['legacy_sample_fingerprints']['exclude_slugs'] ?? ['cao-quoc-viet'];
    }

    /**
     * Tìm HLV vẫn đang giữ dữ liệu mẫu cũ (chưa cập nhật hồ sơ thật).
     *
     * @return \Illuminate\Support\Collection<int, int> trainer_info.id
     */
    public static function findTrainerIdsStillUsingLegacySample()
    {
        $achievementFingerprint = self::legacyAchievementFingerprint();
        $skillFingerprint = self::legacySkillFingerprint();
        $excludeSlugs = self::excludeSlugs();

        if (empty($achievementFingerprint)) {
            return collect();
        }

        $query = Trainer::query()
            ->whereHas('achievements', function ($q) use ($achievementFingerprint) {
                $q->where('content', $achievementFingerprint);
            })
            ->when(!empty($skillFingerprint), function ($q) use ($skillFingerprint) {
                $q->whereHas('skills', function ($sub) use ($skillFingerprint) {
                    $sub->where('skill', $skillFingerprint);
                });
            })
            ->whereDoesntHave('seo', function ($q) use ($excludeSlugs) {
                $q->whereIn('slug', $excludeSlugs);
            });

        return $query->pluck('id');
    }

    /**
     * Ghi đè toàn bộ các cấp hồ sơ bằng dữ liệu nháp.
     */
    public static function applyToTrainer(int $trainerId): bool
    {
        $trainer = Trainer::find($trainerId);
        if (empty($trainer)) {
            return false;
        }

        $config = self::config();
        $draft = self::forImport();

        DB::transaction(function () use ($trainer, $config, $draft) {
            // Xóa nội dung con trước khi xóa experience/degree
            $experienceIds = TrainerExperience::where('trainer_info_id', $trainer->id)->pluck('id');
            if ($experienceIds->isNotEmpty()) {
                TrainerExperienceContent::whereIn('trainer_experience_id', $experienceIds)->delete();
            }
            TrainerExperience::where('trainer_info_id', $trainer->id)->delete();

            $degreeIds = TrainerDegree::where('trainer_info_id', $trainer->id)->pluck('id');
            if ($degreeIds->isNotEmpty()) {
                TrainerDegreeContent::whereIn('trainer_degree_id', $degreeIds)->delete();
            }
            TrainerDegree::where('trainer_info_id', $trainer->id)->delete();

            TrainerAchievement::where('trainer_info_id', $trainer->id)->delete();
            TrainerSkill::where('trainer_info_id', $trainer->id)->delete();

            foreach (($config['achievements'] ?? []) as $index => $achi) {
                if (empty($achi['content'])) {
                    continue;
                }
                TrainerAchievement::insertItem([
                    'trainer_info_id' => $trainer->id,
                    'content' => $achi['content'],
                    'ordering' => $index,
                ]);
            }

            foreach (($config['skills'] ?? []) as $index => $skill) {
                if (empty($skill['skill'])) {
                    continue;
                }
                TrainerSkill::insertItem([
                    'trainer_info_id' => $trainer->id,
                    'skill' => $skill['skill'],
                    'percent' => $skill['percent'] ?? 50,
                    'ordering' => $index,
                ]);
            }

            foreach (($config['experiences'] ?? []) as $index => $exper) {
                if (empty($exper['title']) || empty($exper['company']) || empty($exper['content'])) {
                    continue;
                }
                $experienceId = TrainerExperience::insertItem([
                    'trainer_info_id' => $trainer->id,
                    'title' => $exper['title'],
                    'company' => $exper['company'],
                    'ordering' => $index,
                ]);
                foreach (preg_split("/\r\n|\n|\r/", $exper['content']) as $line) {
                    $line = trim($line);
                    if ($line === '') {
                        continue;
                    }
                    TrainerExperienceContent::insertItem([
                        'trainer_experience_id' => $experienceId,
                        'content' => $line,
                    ]);
                }
            }

            foreach (($config['degrees'] ?? []) as $index => $degree) {
                if (empty($degree['title']) || empty($degree['school']) || empty($degree['content'])) {
                    continue;
                }
                $degreeId = TrainerDegree::insertItem([
                    'trainer_info_id' => $trainer->id,
                    'title' => $degree['title'],
                    'school' => $degree['school'],
                    'ordering' => $index,
                ]);
                foreach (preg_split("/\r\n|\n|\r/", $degree['content']) as $line) {
                    $line = trim($line);
                    if ($line === '') {
                        continue;
                    }
                    TrainerDegreeContent::insertItem([
                        'trainer_degree_id' => $degreeId,
                        'content' => $line,
                    ]);
                }
            }

            $languagesJson = SpokenLanguage::fromRequest($draft['languages'] ?? []);

            Trainer::updateItem($trainer->id, [
                'description' => $draft['description'],
                'area' => $draft['area'],
                'years_experience' => $draft['years_experience'],
                'languages' => $languagesJson,
                'total_learner' => $draft['total_learner'],
                'total_teaching_hour' => $draft['total_teaching_hour'],
                'total_prize' => $draft['total_prize'],
            ]);

            // Cập nhật trực tiếp để tránh side-effect rebuild slug_full của Seo::updateItem
            if (!empty($trainer->seo_id)) {
                DB::table('seo')->where('id', $trainer->seo_id)->update([
                    'description' => $draft['description'],
                    'seo_description' => $draft['seo_description'],
                ]);
            }
        });

        return true;
    }
}
