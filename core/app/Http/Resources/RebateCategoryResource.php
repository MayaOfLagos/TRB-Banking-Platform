<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RebateCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'image' => $this->image ? asset('assets/images/rebate_categories/' . $this->image) : null,
            'active_programs_count' => $this->rebate_programs_count ?? 0,
            'status' => $this->status,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString()
        ];
    }
}