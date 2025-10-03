<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRebateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'program' => [
                'id' => $this->program->id,
                'name' => $this->program->name,
                'image' => $this->program->image ? asset('assets/images/rebate_programs/' . $this->program->image) : null
            ],
            'purchase_amount' => (float) $this->purchase_amount,
            'rebate_amount' => (float) $this->rebate_amount,
            'tier_multiplier' => (float) $this->tier_multiplier,
            'status' => $this->status,
            'status_label' => ucfirst($this->status),
            'status_color' => $this->getStatusColor(),
            'product_upload' => $this->when($this->product_upload, [
                'id' => $this->product_upload?->id,
                'product_image' => $this->product_upload?->product_image ? 
                    asset('storage/' . $this->product_upload->product_image) : null,
                'receipt_image' => $this->product_upload?->receipt_image ? 
                    asset('storage/' . $this->product_upload->receipt_image) : null,
                'store_location' => $this->product_upload?->store_location,
                'purchase_date' => $this->product_upload?->purchase_date
            ]),
            'review_notes' => $this->review_notes,
            'processed_at' => $this->processed_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString()
        ];
    }

    /**
     * Get status color for UI
     */
    private function getStatusColor()
    {
        return match($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'pending' => 'warning',
            'review' => 'info',
            default => 'secondary'
        };
    }
}