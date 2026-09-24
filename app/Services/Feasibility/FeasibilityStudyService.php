<?php

namespace App\Services\Feasibility;

use App\Models\FeasibilityStudy;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeasibilityStudyService
{
    public function create(array $data, ?UploadedFile $pdfFile, ?UploadedFile $imageFile, int $userId): FeasibilityStudy
    {
        $data['user_id'] = $userId;
        $this->attachFile($data, 'pdf_file', 'feasibility_pdfs', $pdfFile);
        $this->attachFile($data, 'cover_image', 'feasibility_images', $imageFile);

        return FeasibilityStudy::create($data);
    }

    public function update(FeasibilityStudy $study, array $data, ?UploadedFile $pdfFile, ?UploadedFile $imageFile): FeasibilityStudy
    {
        unset($data['user_id']);

        if ($pdfFile?->isValid()) {
            $this->deleteStoredFile($study->pdf_file);
            $this->attachFile($data, 'pdf_file', 'feasibility_pdfs', $pdfFile);
        }

        if ($imageFile?->isValid()) {
            $this->deleteStoredFile($study->cover_image);
            $this->attachFile($data, 'cover_image', 'feasibility_images', $imageFile);
        }

        $study->update($data);

        return $study->refresh();
    }

    public function delete(FeasibilityStudy $study): bool
    {
        $this->deleteStoredFile($study->pdf_file);
        $this->deleteStoredFile($study->cover_image);

        return (bool) $study->delete();
    }

    private function attachFile(array &$data, string $attribute, string $directory, ?UploadedFile $file): void
    {
        if (!$file?->isValid()) {
            return;
        }

        $fileName = Str::uuid()->toString() . '_' . sha1_file($file->getRealPath()) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $fileName, 'supabase');
        $baseUrl = rtrim((string) (config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL', '')), '/');

        $data[$attribute] = $baseUrl !== '' ? $baseUrl . '/' . ltrim($path, '/') : $path;
    }

    private function deleteStoredFile(?string $url): void
    {
        if (!$url) {
            return;
        }

        try {
            $baseUrl = rtrim((string) (config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL', '')), '/');
            $path = $baseUrl !== '' ? Str::after($url, $baseUrl . '/') : $url;
            Storage::disk('supabase')->delete(ltrim($path, '/'));
        } catch (\Throwable $exception) {
            Log::warning('Unable to delete feasibility study file.', [
                'url' => $url,
                'exception' => $exception,
            ]);
        }
    }
}
