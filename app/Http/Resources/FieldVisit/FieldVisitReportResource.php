<?php

namespace App\Http\Resources\FieldVisit;

use Illuminate\Http\Resources\Json\JsonResource;

class FieldVisitReportResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = parent::toArray($request);
        unset($data["password"], $data["remember_token"]);
        return $data;
    }
}
