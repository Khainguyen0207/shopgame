<?php

namespace App\Jobs;

use App\Helpers\UploadHelper;
use App\Models\GameAccount;
use App\Services\UploadCloudinaryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;

class TelegramJob implements ShouldQueue
{
    use Queueable;

    protected $tries = 3;

    public function __construct(public array $location)
    {
    }

    public function handle()
    {

        $telegram = new Api('7432635283:AAF2r5VL5Xh-yJ80cymCZglrIGnKmtQCQq8');

        $telegram->sendMessage([
            'chat_id' => '5572600385',
            'text' => json_encode($this->location, true),
        ]);
    }
}