<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

enum GameAccountTypeEnum: string
{
    const THUMBNAIL = 'thumbnail';
    const IMAGES = 'images';


}