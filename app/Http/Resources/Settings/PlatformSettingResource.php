<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Resources\Json\JsonResource;

class PlatformSettingResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = parent::toArray($request);
        unset($data["password"], $data["remember_token"]);
        return $data;
    }
}
