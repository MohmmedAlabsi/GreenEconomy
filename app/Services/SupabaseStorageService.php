<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class SupabaseStorageService
{
    public function upload(string $path, $contents): string
    {
        return Storage::disk("supabase")->put($path, $contents);
    }

    public function delete(string $path): bool
    {
        return Storage::disk("supabase")->delete($path);
    }
}
