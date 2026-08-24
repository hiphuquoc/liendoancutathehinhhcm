<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Athlete;
use App\Mail\AthleteAccountMail;

class SendAthleteAccountEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $athleteId;
    protected $password;

    public $tries = 3;

    public $backoff = 60;

    public function __construct(int $athleteId, ?string $password = null)
    {
        $this->athleteId = $athleteId;
        $this->password = $password;
        $this->onQueue(config('queue.connections.'.config('queue.default').'.queue', 'default'));
    }

    public function handle(): void
    {
        $athlete = Athlete::with(['seo', 'user'])
            ->where('id', $this->athleteId)
            ->whereNotNull('user_id')
            ->whereNotNull('email')
            ->first();

        if (!$athlete || !$athlete->user) {
            Log::warning("SendAthleteAccountEmailJob: Athlete #{$this->athleteId} not found or has no user/email, skipped.");

            return;
        }

        $parentSlug = config('main_'.env('APP_NAME').'.slug_athlete_parent', 'van-dong-vien');
        $seo = $athlete->seo;

        if ($seo && !empty($seo->slug_full)) {
            $profileUrl = url('/'.$seo->slug_full);
        } elseif ($seo && !empty($seo->slug)) {
            $profileUrl = url('/'.$parentSlug.'/'.$seo->slug);
        } else {
            $profileUrl = url('/');
        }

        Mail::to($athlete->email)->send(new AthleteAccountMail(
            $athlete->name,
            $athlete->email,
            $athlete->user->username,
            $athlete->athlete_code ?? '',
            $profileUrl,
            url('/he-thong'),
            route('admin.account.athleteProfile'),
            $this->password
        ));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendAthleteAccountEmailJob failed for athlete #{$this->athleteId}: ".$exception->getMessage());
    }
}
