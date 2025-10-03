<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RebateProgramResource extends JsonResource
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
            'rebate_rate' => (float) $this->rebate_rate,
            'max_rebate_amount' => (float) $this->max_rebate_amount,
            'min_purchase_amount' => (float) $this->min_purchase_amount,
            'featured' => (bool) $this->featured,
            'image' => $this->image ? asset('assets/images/rebate_programs/' . $this->image) : null,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'icon' => $this->category->icon
            ],
            'terms_conditions' => $this->terms_conditions,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString()
        ];
    }
}