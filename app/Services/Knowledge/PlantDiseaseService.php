<?php

namespace App\Services\Knowledge;

use App\Models\PlantDisease;
use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PlantDiseaseService
{
    public function __construct(private readonly SupabaseStorageService $storage) {}
    public function query() { return PlantDisease::query()->with(['plants', 'treatments']); }
    public function find(string|int $id): PlantDisease { return $this->query()->findOrFail($id); }
    public function queryForUser(int $userId) { return $this->query()->where('user_id', $userId); }
    public function create(array $data, ?UploadedFile $image = null): PlantDisease { return DB::transaction(function () use ($data, $image) { $plantIds = $data['plant_ids'] ?? []; unset($data['plant_ids'], $data['image']); if ($image?->isValid()) { $path = $image->store('plant_diseases', 'supabase'); $data['image_url'] = $this->storage->url($path); } $disease = PlantDisease::create($data); if ($plantIds) $disease->plants()->sync($plantIds); return $disease->load('plants'); }); }
    public function update(PlantDisease $disease, array $data, ?UploadedFile $image = null): PlantDisease { return DB::transaction(function () use ($disease, $data, $image) { $plantIds = $data['plant_ids'] ?? null; unset($data['plant_ids'], $data['image']); if ($image?->isValid()) { if ($disease->image_url) $this->storage->delete($disease->image_url); $path = $image->store('plant_diseases', 'supabase'); $data['image_url'] = $this->storage->url($path); } $disease->update($data); if ($plantIds !== null) $disease->plants()->sync($plantIds); return $disease->load('plants'); }); }
    public function destroy(PlantDisease $disease): bool { return DB::transaction(function () use ($disease) { if ($disease->image_url) $this->storage->delete($disease->image_url); $disease->plants()->detach(); $disease->treatments()->delete(); return (bool) $disease->delete(); }); }
}
