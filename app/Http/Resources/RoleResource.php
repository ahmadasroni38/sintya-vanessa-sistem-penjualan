<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->whenPivotLoaded('user_roles', function () {
                return $this->pivot->is_active;
            }),
            'assigned_at' => $this->whenPivotLoaded('user_roles', function () {
                return $this->pivot->assigned_at;
            }),
            'expires_at' => $this->whenPivotLoaded('user_roles', function () {
                return $this->pivot->expires_at;
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
