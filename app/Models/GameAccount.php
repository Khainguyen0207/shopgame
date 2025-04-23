<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function category()
    {
        return $this->belongsTo(GameCategory::class, 'game_category_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
