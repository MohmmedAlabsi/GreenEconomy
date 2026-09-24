<?php

namespace App\Http\Resources\Feasibility;

use Illuminate\Http\Resources\Json\JsonResource;

class FeasibilityStudyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'region_id' => $this->region_id,
            'cover_image' => $this->cover_image,
            'capital_required' => $this->capital_required,
            'expected_roi' => $this->expected_roi,
            'payback_period' => $this->payback_period,
            'risk_level' => $this->risk_level,
            'status' => $this->status,
            'pdf_file' => $this->pdf_file,
            'user_id' => $this->user_id,
            'category' => $this->whenLoaded('category'),
            'region' => $this->whenLoaded('region'),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
