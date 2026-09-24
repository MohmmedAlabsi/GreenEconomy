<?php

namespace App\Services\Settings;

use App\Models\PlatformSetting;
use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PlatformSettingService
{
    public function __construct(private readonly SupabaseStorageService $storage) {}

    public function queryForUser(int $userId)
    {
        return PlatformSetting::query()->where('user_id', $userId);
    }

    public function update(PlatformSetting $setting, array $data, ?UploadedFile $asset = null): PlatformSetting
    {
        return DB::transaction(function () use ($setting, $data, $asset) {
            if ($asset?->isValid()) {
                $old = $setting->value;
                if ($old) $this->storage->delete($old);
                $path = $asset->store('platform-settings', 'supabase');
                $data['value'] = $this->storage->url($path);
            }
            $setting->update($data);
            Cache::forget('platform_settings');
            return $setting->fresh();
        });
    }
}
