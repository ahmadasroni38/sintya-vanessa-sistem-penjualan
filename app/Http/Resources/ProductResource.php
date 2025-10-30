<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'product_code' => $this->product_code,
            'product_name' => $this->product_name,
            'description' => $this->description,
            'product_type' => $this->product_type,
            'product_type_label' => $this->getProductTypeLabel(),

            // Relationships
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return $this->category ? [
                    'id' => $this->category->id,
                    'code' => $this->category->code,
                    'name' => $this->category->name,
                    'full_name' => $this->category->full_name,
                ] : null;
            }),

            'unit_id' => $this->unit_id,
            'unit' => $this->whenLoaded('unit', function () {
                return [
                    'id' => $this->unit->id,
                    'code' => $this->unit->code,
                    'name' => $this->unit->name,
                    'symbol' => $this->unit->symbol,
                    'display_name' => $this->unit->display_name,
                ];
            }) ?: null,

            'location_id' => $this->location_id,
            'location' => $this->whenLoaded('location', function () {
                return $this->location ? [
                    'id' => $this->location->id,
                    'name' => $this->location->name,
                    'code' => $this->location->code,
                    'description' => $this->location->description,
                    'address' => $this->location->address,
                    'city' => $this->location->city,
                    'state' => $this->location->state,
                    'country' => $this->location->country,
                    'postal_code' => $this->location->postal_code,
                    'latitude' => $this->location->latitude,
                    'longitude' => $this->location->longitude,
                    'color' => $this->location->color,
                    'is_active' => $this->location->is_active,
                    'parent_id' => $this->location->parent_id,
                    'metadata' => $this->location->metadata,
                    'created_at' => $this->location->created_at?->toISOString(),
                    'updated_at' => $this->location->updated_at?->toISOString(),
                ] : null;
            }),

            // Pricing
            'purchase_price' => $this->purchase_price ? number_format($this->purchase_price, 2, '.', '') : null,
            'selling_price' => $this->selling_price ? number_format($this->selling_price, 2, '.', '') : null,
            'profit_margin' => $this->profit_margin ? (float) $this->profit_margin : 0,
            'profit_margin_percentage' => $this->profit_margin_percentage ? round($this->profit_margin_percentage, 2) : 0,

            // Stock levels
            'minimum_stock' => $this->minimum_stock,
            'maximum_stock' => $this->maximum_stock,

            // Stock information (if loaded)
            'current_stock' => $this->when(
                $request->has('with_stock'),
                fn() => $this->getCurrentStock($request->input('location_id'))
            ),
            'stock_status' => $this->when(
                $request->has('with_stock'),
                fn() => $this->getStockStatus($request->input('location_id'))
            ),
            'is_below_minimum' => $this->when(
                $request->has('with_stock'),
                fn() => $this->isBelowMinimum($request->input('location_id'))
            ),
            'is_above_maximum' => $this->when(
                $request->has('with_stock'),
                fn() => $this->isAboveMaximum($request->input('location_id'))
            ),

            // Status
            'is_active' => $this->is_active,

            // Counts
            'stock_cards_count' => $this->when(
                $this->relationLoaded('stockCards'),
                fn() => $this->stockCards->count()
            ) ?: 0,

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),

            // Permissions
            'can' => $this->when($request->user(), function () use ($request) {
                return [
                    'update' => $request->user()->can('update', $this->resource),
                    'delete' => $request->user()->can('delete', $this->resource),
                ];
            }),
        ];
    }

    /**
     * Get the product type label.
     */
    protected function getProductTypeLabel(): string
    {
        return match ($this->product_type) {
            'finished_goods' => 'Finished Goods',
            'raw_material' => 'Raw Material',
            'consumable' => 'Consumable',
            default => ucwords(str_replace('_', ' ', $this->product_type)),
        };
    }
}
