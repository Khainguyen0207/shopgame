<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JetBrains\PhpStorm\NoReturn;

class GameAccount extends Model
{
    protected $fillable = [
        'game_category_id',
        'username',
        'password',
        'price',
        'status',
        'registration_type',
        'note',
        'thumb',
        'images'
    ];

    protected $casts = [
        'images' => 'array',
        'thumb' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(GameCategory::class, 'game_category_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
