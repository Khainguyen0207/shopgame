<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

enum GameAccountStatusEnum: string
{
    case TTT = 'ttt';
    case TTX = 'ttx';
    case TTS = 'tts';

    public function label(): string
    {
        return match ($this) {
            self::TTT => 'Trắng thông tin',
            self::TTX => 'Thông tin xấu',
            self::TTS => 'Mỗi số',
        };
    }
}