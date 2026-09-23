<?php

namespace App\Http\Resources\Engineer;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = parent::toArray($request);
        unset($data["password"], $data["remember_token"]);
        return $data;
    }
}
