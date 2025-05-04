<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;

class SendCacheAccess extends Command
{
    /**
     * Tên và mô tả của command.
     *
     * @var string
     */
    protected $signature = 'cache:send-access';

    protected $description = 'Gửi cache access và xóa nó đi';

    /**
     * Thực hiện công việc chính của command.
     */
    public function handle()
    {
        $accessData = Cache::get('access');

        $filePath = 'data.json';
        Storage::put($filePath, json_encode($accessData, JSON_PRETTY_PRINT));

        $fileContent = Storage::get($filePath);

        if ($accessData) {
            $telegram = new Api(config('services.telegram.bot_token'));

            $number = 'Số lượng truy cập shop hôm nay: ' .count($accessData);

            $telegram->sendMessage([
                'chat_id' => config('services.telegram.chat_id'),
                'text' => $number ."\n". "<pre>" . htmlspecialchars($fileContent) . "</pre>",
                'parse_mode' => 'HTML',
            ]);

            Cache::forget('access');
            Storage::delete($filePath);


            $this->info('Cache access đã được gửi và xóa.');
        } else {
            $this->info('Không có dữ liệu nào trong cache "access".');
        }
    }
}