<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Models\Trainer;
use App\Mail\TrainerAccountMail;

class SendTrainerAccountEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int */
    protected $trainerId;
    protected $password;

    /** Số lần thử lại khi job thất bại */
    public $tries = 3;

    /** Thời gian chờ (giây) trước mỗi lần thử lại */
    public $backoff = 60;

    /**
     * Create a new job instance.
     *
     * @param int $trainerId
     * @return void
     */
    public function __construct(int $trainerId, ?string $password = null)
    {
        $this->trainerId = $trainerId;
        $this->password = $password;
        $this->onQueue(config('queue.connections.' . config('queue.default') . '.queue', 'default'));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $trainer = Trainer::with(['seo', 'user'])
            ->where('id', $this->trainerId)
            ->whereNotNull('user_id')
            ->whereNotNull('email')
            ->first();

        if (!$trainer || !$trainer->user) {
            Log::warning("SendTrainerAccountEmailJob: Trainer #{$this->trainerId} not found or has no user/email, skipped.");
            return;
        }

        $parentSlug = config('main_' . env('APP_NAME') . '.slug_trainer_parent', 'huan-luyen-vien');
        $seo = $trainer->seo;

        if ($seo && !empty($seo->slug_full)) {
            $profileUrl = url('/' . $seo->slug_full);
        } elseif ($seo && !empty($seo->slug)) {
            $profileUrl = url('/' . $parentSlug . '/' . $seo->slug);
        } else {
            $profileUrl = url('/');
        }

        Mail::to($trainer->email)->send(new TrainerAccountMail(
            $trainer->name,
            $trainer->email,
            $trainer->user->username,
            $trainer->trainer_code ?? '',
            $profileUrl,
            url('/he-thong'),
            route('admin.account.trainerProfile'),
            $this->password
        ));
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SendTrainerAccountEmailJob failed for trainer #{$this->trainerId}: " . $exception->getMessage());
    }
}
