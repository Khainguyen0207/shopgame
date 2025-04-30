<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GameAccountTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GameAccountRequest;
use App\Models\GameAccount;
use App\Models\GameCategory;
use App\Services\UploadCloudinaryService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameAccountController extends Controller
{
    private const UPLOAD_DIR = 'public/accounts/';

    public function index()
    {
        $title = 'Danh sách tài khoản game';
        $accounts = GameAccount::with(['category', 'buyer'])->orderBy('id', "DESC")->get();
        return view('admin.accounts.index', compact('title', 'accounts'));
    }

    public function create()
    {
        $title = 'Thêm tài khoản game mới';
        $categories = GameCategory::where('active', true)->get();
        return view('admin.accounts.create', compact('title', 'categories'));
    }

    public function store(GameAccountRequest $request)
    {
        $request->validate(
            [
                'thumb' => 'required',
                'images' => 'required'
            ]
        );

        try {
            DB::beginTransaction();

            $uploadCloudinaryService = new UploadCloudinaryService();

            $data = $request->except(['thumb', 'images']);

            $thumb = $data['thumb'] = $this->setFiles($request->file('thumb'), GameAccountTypeEnum::THUMBNAIL);
            $images = $data['images'] = $this->setFiles($request->file('images'), GameAccountTypeEnum::IMAGES);

            $account = GameAccount::query()->create($data);

            $uploadCloudinaryService->upload($thumb, $account);
            $uploadCloudinaryService->upload($images, $account);

            DB::commit();

            return redirect()->route('admin.accounts.index')
                ->with('success', 'Tài khoản game đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating game account: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit(GameAccount $account)
    {
        $title = 'Chỉnh sửa tài khoản game';
        $categories = GameCategory::where('active', true)->get();
        return view('admin.accounts.edit', compact('title', 'account', 'categories'));
    }

    public function update(GameAccountRequest $request, GameAccount $account)
    {
        try {
            DB::beginTransaction();
            $data = $request->except(['thumb', 'images']);
            $uploadCloudinaryService = new UploadCloudinaryService();

            if ($request->hasFile('thumb')) {

                $this->deleteThumbnailGameAccount($account);

                $publicId = Str::random(20);
                $url = [
                    'url_image' => $request->thumb->store(self::UPLOAD_DIR . 'thumbnails'),
                    'public_id' => $publicId,
                    'type' => 'thumb',
                ];
                $data['thumb'] = $url;
                $uploadCloudinaryService->upload([$url], $account);
            }

            if ($request->hasFile('images')) {

                $this->deleteImagesGameAccount($account);

                $imagePaths = [];

                foreach ($request->file('images') as $image) {
                    $publicId = Str::random(20);
                    $url = [
                        'url_image' => $image->store(self::UPLOAD_DIR . 'images'),
                        'public_id' => $publicId,
                        'type' => 'images',
                    ];

                    $imagePaths[] = $url;
                }

                $uploadCloudinaryService->upload($imagePaths, $account);

                $data['images'] = $imagePaths;
            }

            $account->update($data);

            DB::commit();

            return redirect()->route('admin.accounts.index')
                ->with('success', 'Tài khoản game đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating game account: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(GameAccount $account)
    {
        try {
            DB::beginTransaction();

            $this->deleteImagesGameAccount($account);
            $this->deleteThumbnailGameAccount($account);

            $account->delete();
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting game account: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa tài khoản game: ' . $e->getMessage()
            ]);
        }
    }

    public function setFiles(UploadedFile|array $files, string $type): ?array
    {
        if (in_array($type, GameAccountTypeEnum::cases())) {
            return null;
        }

        if ($type === GameAccountTypeEnum::THUMBNAIL) {
            $publicId = Str::random(20);

            return [
                'url_image' => $files->store(self::UPLOAD_DIR . 'thumbnails'),
                'public_id' => $publicId,
                'type' => GameAccountTypeEnum::THUMBNAIL,
            ];
        }

        if ($type === GameAccountTypeEnum::IMAGES) {
            $imagePaths = [];

            foreach ($files as $image) {
                $publicId = Str::random(20);
                $url = [
                    'url_image' => $image->store(self::UPLOAD_DIR . 'images'),
                    'public_id' => $publicId,
                    'type' => GameAccountTypeEnum::IMAGES,
                ];

                $imagePaths[] = $url;
            }

            return $imagePaths;
        }

        return null;
    }

    public function deleteThumbnailGameAccount(GameAccount $account)
    {
        $uploadCloudinaryService = new UploadCloudinaryService();

        if ($thumb = $account->thumb) {
            $url_image = $thumb['url_image'];

            if (Storage::exists($url_image)) {
                Storage::delete($url_image);
            } else {
                $publicId = Str::between($url_image, 'firefoxgame/', '.');
                $uploadCloudinaryService->deleteAssetsByFolder([$publicId]);
            }
        }

    }

    public function deleteImagesGameAccount(GameAccount $account)
    {
        $uploadCloudinaryService = new UploadCloudinaryService();

        if (($images = $account->images) && (is_array($images))) {
            $publicIds = [];
            foreach ($images as $image) {
                $url_image = $image['url_image'];
                if (Storage::exists($url_image)) {
                    Storage::delete($url_image);
                } else {
                    $publicId = Str::between($url_image, 'firefoxgame/', '.');
                    $publicIds[] = $publicId;
                }
            }

            if (count($publicIds) > 0) {
                $uploadCloudinaryService->deleteAssetsByFolder($publicIds);
            }
        }
    }
}
