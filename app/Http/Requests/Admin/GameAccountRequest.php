<?php

namespace App\Http\Requests\Admin;


use Illuminate\Foundation\Http\FormRequest;

class GameAccountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'game_category_id' => 'required|exists:game_categories,id',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'registration_type' => 'required|in:TTX,TTT',
            'note' => 'nullable|string',
            'thumb' => 'image|mimes:jpeg,png,jpg,gif',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'status' => 'required|in:available,sold'
        ];
    }
}