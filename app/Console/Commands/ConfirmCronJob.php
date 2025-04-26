<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Api;

class ConfirmCronJob extends Command
{
    /**
     * Tên và mô tả của command.
     *
     * @var string
     */
    protected $signature = 'app:confirm-cronjob';

    protected $description = 'app:confirm-cronjob';

    /**
     * Thực hiện công việc chính của command.
     */
    public function handle()
    {
        $this->info('Cron job đang chạy ngon cơm');

        $telegram = new Api(config('services.telegram.bot_token'));

        $telegram->sendMessage([
            'chat_id' => config('services.telegram.chat_id'),
            'text' => 'App is running'
        ]);
    }
}