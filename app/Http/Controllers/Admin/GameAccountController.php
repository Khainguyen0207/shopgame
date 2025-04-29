<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GameAccountRequest;
use App\Jobs\UploadImageJob;
use App\Models\GameAccount;
use App\Models\GameCategory;
use App\Helpers\UploadHelper;
use App\Services\UploadCloudinaryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameAccountController extends Controller
{
    private const UPLOAD_DIR = 'accounts';

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

            $data = $request->except(['thumb', 'images']);
            $uploadCloudinaryService = new UploadCloudinaryService();

            $imagePaths = [];
            $urlThumb = [];

            if ($request->hasFile('thumb')) {
                $publicId = Str::random(20);
                $urlThumb = [
                    'url_image' => $request->thumb->store('public/images'),
                    'public_id' => $publicId,
                    'type' => 'thumb',
                ];
                $data['thumb'] = json_encode($urlThumb, true);
            }

            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $image) {
                    $publicId = Str::random(20);
                    $url = [
                        'url_image' => $image->store('public/images'),
                        'public_id' => $publicId,
                        'type' => 'images',
                    ];

                    $imagePaths[] = $url;
                }


                $data['images'] = json_encode($imagePaths, true);
            }

            $account = GameAccount::query()->create($data);

            DB::commit();

            $uploadCloudinaryService->upload($urlThumb, $account);
            $uploadCloudinaryService->upload($imagePaths, $account);

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
                if ($account->thumb) {
                    $publicId = Str::between($account->thumb, 'firefoxgame/', '.');

                    if (Storage::exists($account->thumb)) {
                        Storage::delete($account->thumb);
                    } else {
                        $uploadCloudinaryService->deleteAssetsByFolder([$publicId]);
                    }
                }
                $publicId = Str::random(20);
                $url = [
                    'url_image' => $request->thumb->store('public/images'),
                    'public_id' => $publicId,
                    'type' => 'thumb',
                ];
                $data['thumb'] = $url;
                $uploadCloudinaryService->upload($url, $account);
            }

            if ($request->hasFile('images')) {
                if ($account->images) {
                    $oldImages = json_decode($account->images, true);
                    $publicIds = [];

                    foreach ($oldImages as $image) {
                        if (Storage::exists($image['url_image'])) {
                            Storage::delete($image['url_image']);
                            continue;
                        }

                        $publicId = Str::between($image['url_image'], 'firefoxgame/', '.');
                        $publicIds[] = $publicId;
                    }

                    $uploadCloudinaryService->deleteAssetsByFolder($publicIds);
                }

                // Store new images
                $imagePaths = [];

                foreach ($request->file('images') as $image) {
                    $publicId = Str::random(20);
                    $url = [
                        'url_image' => $image->store('public/images'),
                        'public_id' => $publicId,
                        'type' => 'images',
                    ];

                    $imagePaths[] = $url;
                }

                $uploadCloudinaryService->upload($imagePaths, $account);

                $data['images'] = json_encode($imagePaths, true);
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

            $uploadCloudinaryService = new UploadCloudinaryService();
            // Delete thumbnail if exists
            if ($account->thumb) {
                $publicId = Str::between($account->thumb, 'firefoxgame/', '.');

                if (Storage::exists($account->thumb)) {
                    Storage::delete($account->thumb);
                } else {
                    $uploadCloudinaryService->deleteAssetsByFolder([$publicId]);
                }
            }

            // Delete additional images if exists
            if ($account->images) {
                $oldImages = json_decode($account->images, true);
                $publicIds = [];

                foreach ($oldImages as $image) {
                    if (Storage::exists($image['url_image'])) {
                        Storage::delete($image['url_image']);
                        continue;
                    }

                    $publicId = Str::between($image['url_image'], 'firefoxgame/', '.');
                    $publicIds[] = $publicId;
                }

                if (count($publicIds) > 0) {
                    $uploadCloudinaryService->deleteAssetsByFolder($publicIds);
                }
            }

            // Delete the account record
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
}
