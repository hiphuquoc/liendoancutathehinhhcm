<?php

namespace App\Services;

use App\Helpers\Upload;
use App\Http\Controllers\Admin\RedirectController;
use App\Http\Controllers\RoutingController;
use App\Models\Athlete;
use App\Models\JobAutoTranslate;
use App\Models\JobAutoTranslateLinks;
use App\Models\RedirectInfo;
use App\Models\Referee;
use App\Models\Role;
use App\Models\Seo;
use App\Models\Trainer;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ProfileDeletionService
{
    public const TYPE_TRAINER = 'trainer';
    public const TYPE_ATHLETE = 'athlete';
    public const TYPE_REFEREE = 'referee';

    /**
     * @return array{ok: bool, message: string}
     */
    public function deleteTrainer(int $id): array
    {
        return $this->deleteProfile(self::TYPE_TRAINER, $id);
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function deleteAthlete(int $id): array
    {
        return $this->deleteProfile(self::TYPE_ATHLETE, $id);
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function deleteReferee(int $id): array
    {
        return $this->deleteProfile(self::TYPE_REFEREE, $id);
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function deleteByType(string $type, int $id): array
    {
        return $this->deleteProfile($type, $id);
    }

    /**
     * @param  array<int, int|string>  $ids
     * @return array{deleted: array<int, int>, failed: array<int, array{id: int, message: string}>}
     */
    public function deleteMany(string $type, array $ids): array
    {
        $deleted = [];
        $failed = [];
        $uniqueIds = array_values(array_unique(array_filter(array_map('intval', $ids))));

        foreach ($uniqueIds as $id) {
            $result = $this->deleteProfile($type, $id);
            if (!empty($result['ok'])) {
                $deleted[] = $id;
            } else {
                $failed[] = [
                    'id' => $id,
                    'message' => $result['message'] ?? 'Không xóa được hồ sơ.',
                ];
            }
        }

        return [
            'deleted' => $deleted,
            'failed' => $failed,
        ];
    }

    /**
     * @return array{ok: bool, message: string}
     */
    private function deleteProfile(string $type, int $id): array
    {
        $config = $this->typeConfig($type);
        $label = $config['label'];

        if ($id <= 0) {
            return ['ok' => false, 'message' => 'Thiếu mã hồ sơ cần xóa.'];
        }

        $ownsTransaction = DB::transactionLevel() === 0;
        if ($ownsTransaction) {
            DB::beginTransaction();
        }

        try {
            /** @var Model|null $info */
            $info = $config['model']::query()
                ->with([
                    'seo',
                    'seos.infoSeo.contents',
                    'activityImages',
                    'achievements',
                    'skills',
                    'experiences.contents',
                    'degrees.contents',
                    'user',
                ])
                ->find($id);

            if (empty($info)) {
                if ($ownsTransaction) {
                    DB::rollBack();
                }

                return ['ok' => false, 'message' => "Không tìm thấy hồ sơ {$label}."];
            }

            $user = $this->resolveLinkedUser($info, $type);
            $slugFulls = $this->collectSlugFulls($info);

            $this->deleteActivityImages($info);
            $this->deleteRepeaterRelations($info);
            $this->deleteSeoTree($info);
            $this->deleteHtmlCaches($slugFulls);
            $this->deleteRedirects($slugFulls);

            $info->delete();
            $this->unlinkOrDeleteUser($user, $config['role']);

            if ($ownsTransaction) {
                DB::commit();
            }

            return ['ok' => true, 'message' => "Đã xóa hồ sơ {$label} và toàn bộ dữ liệu liên quan."];
        } catch (\Throwable $exception) {
            if ($ownsTransaction) {
                DB::rollBack();
            }

            Log::error('ProfileDeletionService failed', [
                'type' => $type,
                'id' => $id,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return ['ok' => false, 'message' => 'Không thể xóa hồ sơ. Vui lòng thử lại hoặc kiểm tra nhật ký lỗi.'];
        }
    }

    private function typeConfig(string $type): array
    {
        return match ($type) {
            self::TYPE_TRAINER => [
                'model' => Trainer::class,
                'role' => 'trainer',
                'label' => 'huấn luyện viên',
            ],
            self::TYPE_ATHLETE => [
                'model' => Athlete::class,
                'role' => 'athlete',
                'label' => 'vận động viên',
            ],
            self::TYPE_REFEREE => [
                'model' => Referee::class,
                'role' => 'referee',
                'label' => 'trọng tài',
            ],
            default => throw new \InvalidArgumentException('Loại hồ sơ không hợp lệ.'),
        };
    }

    private function resolveLinkedUser(Model $info, string $type): ?User
    {
        if (!empty($info->user_id)) {
            $user = User::find($info->user_id);
            if ($user) {
                return $user;
            }
        }

        $candidates = collect();
        if (!empty($info->email)) {
            $candidates->push(User::where('email', $info->email)->first());
        }

        $slug = $info->seo->slug ?? null;
        if (!empty($slug)) {
            $legacyKey = str_replace('-', '', $slug);
            $candidates->push(User::where('username', $legacyKey)->first());
            $candidates->push(User::where('email', $legacyKey)->first());
        }

        foreach ($candidates->filter() as $user) {
            if ($this->userBelongsToOtherProfile($user, $info, $type)) {
                continue;
            }

            return $user;
        }

        return null;
    }

    private function userBelongsToOtherProfile(User $user, Model $info, string $type): bool
    {
        $query = match ($type) {
            self::TYPE_TRAINER => Trainer::where('user_id', $user->id),
            self::TYPE_ATHLETE => Athlete::where('user_id', $user->id),
            self::TYPE_REFEREE => Referee::where('user_id', $user->id),
            default => null,
        };

        if (empty($query)) {
            return false;
        }

        return $query->where('id', '!=', $info->id)->exists();
    }

    private function collectSlugFulls(Model $info): array
    {
        $slugs = [];
        if (!empty($info->seo->slug_full)) {
            $slugs[] = $info->seo->slug_full;
        }
        foreach ($info->seos as $relation) {
            $slugFull = $relation->infoSeo->slug_full ?? null;
            if (!empty($slugFull)) {
                $slugs[] = $slugFull;
            }
        }

        return array_values(array_unique($slugs));
    }

    private function deleteActivityImages(Model $info): void
    {
        foreach ($info->activityImages as $actImg) {
            $this->safeDeleteWallpaper($actImg->image ?? null);
        }
        $info->activityImages()->delete();
    }

    private function deleteRepeaterRelations(Model $info): void
    {
        $info->achievements()->delete();
        $info->skills()->delete();

        foreach ($info->experiences as $experience) {
            $experience->contents()->delete();
        }
        $info->experiences()->delete();

        foreach ($info->degrees as $degree) {
            $degree->contents()->delete();
        }
        $info->degrees()->delete();
    }

    private function deleteSeoTree(Model $info): void
    {
        $seoIds = collect();
        foreach ($info->seos as $relation) {
            if (!empty($relation->seo_id)) {
                $seoIds->push((int) $relation->seo_id);
            }
        }
        if (!empty($info->seo_id)) {
            $seoIds->push((int) $info->seo_id);
        }
        $seoIds = $seoIds->unique()->filter()->values();

        foreach ($info->seos as $relation) {
            $seo = $relation->infoSeo;
            if ($seo) {
                $this->deleteOneSeo($seo);
            }
            $relation->delete();
        }

        foreach ($seoIds as $seoId) {
            $seo = Seo::with('contents')->find($seoId);
            if ($seo) {
                $this->deleteOneSeo($seo);
            }
        }
    }

    private function deleteOneSeo(Seo $seo): void
    {
        $this->safeDeleteWallpaper($seo->image ?? null);
        if (!empty($seo->image_small) && $seo->image_small !== ($seo->image ?? null)) {
            $this->safeDeleteWallpaper($seo->image_small);
        }

        foreach ($seo->contents as $content) {
            $content->delete();
        }

        JobAutoTranslate::where('seo_id', $seo->id)->delete();
        JobAutoTranslateLinks::where('seo_id', $seo->id)->delete();

        $seo->delete();
    }

    private function deleteHtmlCaches(array $slugFulls): void
    {
        $folder = config('main_'.env('APP_NAME').'.cache.folderSave');
        $extension = config('main_'.env('APP_NAME').'.cache.extension', 'html');
        if (empty($folder)) {
            return;
        }

        foreach ($slugFulls as $slugFull) {
            $nameCache = RoutingController::buildNameCache($slugFull).'.'.$extension;
            try {
                $path = Storage::path($folder).$nameCache;
                if (is_file($path)) {
                    @unlink($path);
                }
            } catch (\Throwable $e) {
                Log::warning('Không xóa được file cache HTML của hồ sơ', [
                    'slug_full' => $slugFull,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function deleteRedirects(array $slugFulls): void
    {
        foreach ($slugFulls as $slugFull) {
            $url = RedirectController::filterUrl($slugFull);
            if (empty($url)) {
                continue;
            }
            RedirectInfo::where(function ($query) use ($url) {
                $query->where('old_url', $url)->orWhere('new_url', $url);
            })->delete();
        }
    }

    private function unlinkOrDeleteUser(?User $user, string $roleSlug): void
    {
        if (empty($user)) {
            return;
        }

        $isAdmin = ($user->role === 'admin') || $user->hasRole('admin');
        $this->removeProfileRole($user, $roleSlug);

        if ($isAdmin) {
            return;
        }

        $hasTrainer = Trainer::where('user_id', $user->id)->exists();
        $hasAthlete = Athlete::where('user_id', $user->id)->exists();
        $hasReferee = Referee::where('user_id', $user->id)->exists();

        if ($hasTrainer || $hasAthlete || $hasReferee) {
            if ($user->role === $roleSlug) {
                if ($hasTrainer) {
                    $user->role = 'trainer';
                } elseif ($hasReferee) {
                    $user->role = 'referee';
                } elseif ($hasAthlete) {
                    $user->role = 'athlete';
                }
                $user->save();
            }

            return;
        }

        UserRole::where('user_id', $user->id)->delete();
        if (Schema::hasTable('users_permissions')) {
            DB::table('users_permissions')->where('user_id', $user->id)->delete();
        }
        $user->delete();
    }

    private function removeProfileRole(User $user, string $roleSlug): void
    {
        $roleId = Role::where('slug', $roleSlug)->value('id');
        if (!empty($roleId)) {
            UserRole::where('user_id', $user->id)->where('role_id', $roleId)->delete();
        }
    }

    private function safeDeleteWallpaper(?string $url): void
    {
        if (empty($url)) {
            return;
        }
        try {
            Upload::deleteWallpaper($url);
        } catch (\Throwable $e) {
            Log::warning('Không xóa được ảnh cloud khi xóa hồ sơ', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
