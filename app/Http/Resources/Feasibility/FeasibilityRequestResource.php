<?php

namespace App\Http\Resources\Feasibility;

use Illuminate\Http\Resources\Json\JsonResource;

class FeasibilityRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'project_title' => $this->project_title,
            'category_id' => $this->category_id,
            'region_id' => $this->region_id,
            'estimated_budget' => $this->estimated_budget,
            'land_area' => $this->land_area,
            'description' => $this->description,
            'status' => $this->status,
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
