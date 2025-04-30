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

class UploadImageJob implements ShouldQueue
{
    use Queueable;

    protected $tries = 3;

    private UploadCloudinaryService $cloudinary;

    public function __construct(public array $images, public GameAccount $account)
    {
        $this->cloudinary = new UploadCloudinaryService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $cloudinary = $this->cloudinary->uploadApi();
            Log::channel('upload_image')->info('Upload file');

            $imagePath = [];

            foreach ($this->images as $valueImage) {
                $urlImage = $valueImage['url_image'];
                $publicId = $valueImage['public_id'];
                $type = $valueImage['type'];

                if (!$urlImage || !$publicId || !$type) {
                    continue;
                }

                if (!Storage::exists($urlImage)) {
                    Log::channel('upload_image')->warning('Temp file not found: ' . $urlImage);
                    continue;
                }

                $absolutePathForCloudinary = 'file://' . realpath(Storage::path($urlImage));

                $resultUpload = $cloudinary->upload($absolutePathForCloudinary, [
                    'folder' => $this->cloudinary->getFolder(),
                    'public_id' => $publicId,
                ]);

                $secureUrl = Arr::get($resultUpload, 'secure_url') ?? false;

                if (!$secureUrl) {
                    Log::channel('upload_image')->info('Upload file fail: ' . $secureUrl);
                } else {
                    if ($type === 'thumb') {
                        $this->account->thumb = [
                            'url_image' => $secureUrl,
                            'public_id' => $publicId,
                            'type' => $type,
                        ];
                        $this->account->save();
                    } else {
                        $imagePath[] = [
                            'url_image' => $secureUrl,
                            'public_id' => $publicId,
                            'type' => $type,
                        ];
                    }
                    Storage::delete($urlImage);
                    UploadHelper::deleteByUrl($urlImage);
                    Log::channel('upload_image')->info('Upload file success: ' . $secureUrl);
                }
            }

            $this->account->images = $imagePath;
            $this->account->save();

        } catch (\Exception $e) {
            Log::channel('upload_image')->error('UploadImageJobs failed: ' . json_encode([
                    $e->getMessage(), $e->getTrace(), $e->getLine(), $e->getFile(), $e->getCode(),
                ]));
        }
    }
}
