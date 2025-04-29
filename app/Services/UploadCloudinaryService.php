<?php

namespace App\Services;

use App\Jobs\UploadImageJob;
use App\Models\GameAccount;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Queue;

class UploadCloudinaryService
{
    protected string $folder;

    protected Cloudinary $cloudinary;

    public function __construct(string $folder = 'firefoxgame')
    {
        $this->folder = $folder;
        $this->cloudinary = new Cloudinary();
    }

    public function getAsset(string $path = null): ApiResponse
    {
        return $this->adminApi()->asset($this->folder . '/' . $path);
    }

    public function adminApi(): AdminApi
    {
        return $this->cloudinary->adminApi();
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function uploadApi(): UploadApi
    {
        return $this->cloudinary->uploadApi();
    }

    public function deleteAssetsByFolder(array $publicIds, string $folder = null): ApiResponse
    {
        return $this->adminApi()->deleteAssets($publicIds, [
            'folder' => $folder ?? $this->folder,
        ]);
    }

    public function upload(array $images, GameAccount $account): void
    {
        dispatch(new UploadImageJob($images, $account));
    }
}