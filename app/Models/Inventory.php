<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'location_id',
        'quantity',
    ];

    /**
     * Get the product associated with this inventory record.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the physical location of this inventory record.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
