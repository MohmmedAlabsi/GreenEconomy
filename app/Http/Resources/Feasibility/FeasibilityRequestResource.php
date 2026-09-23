<?php

namespace App\Http\Resources\Feasibility;

use Illuminate\Http\Resources\Json\JsonResource;

class FeasibilityRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = parent::toArray($request);
        unset($data["password"], $data["remember_token"]);
        return $data;
    }
}
