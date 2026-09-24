<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class SupabaseStorageService
{
    public function upload(string $path, $contents): string
    {
        Storage::disk('supabase')->put($path, $contents);
        return $path;
    }

    public function delete(string $path): bool
    {
        return Storage::disk('supabase')->delete($this->path($path));
    }

    public function url(string $path): string
    {
        return rtrim(config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL', ''), '/').'/'.ltrim($path, '/');
    }

    public function path(string $value): string
    {
        $url = rtrim(config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL', ''), '/');
        return ltrim(str_replace($url.'/', '', $value), '/');
    }
}
