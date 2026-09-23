<?php

namespace App\Http\Resources\Knowledge;

use Illuminate\Http\Resources\Json\JsonResource;

class DiseaseTreatmentResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = parent::toArray($request);
        unset($data["password"], $data["remember_token"]);
        return $data;
    }
}
