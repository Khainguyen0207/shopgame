<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
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
        // Lấy cache "access"
        $accessData = Cache::get('access');

        if ($accessData) {
            $telegram = new Api(config('services.telegram.bot_token'));

            $number = 'Số lượng truy cập shop hôm nay: ' .count($accessData);
            $info_log = 'Thông tin người đăng nhập: ' .json_encode($accessData, true);

            $telegram->sendMessage([
                'chat_id' => config('services.telegram.chat_id'),
                'text' => $number ."\n". $info_log
            ]);

            Cache::forget('access');

            $this->info('Cache access đã được gửi và xóa.');
        } else {
            $this->info('Không có dữ liệu nào trong cache "access".');
        }
    }
}