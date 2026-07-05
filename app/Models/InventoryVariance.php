<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InventoryVariance extends Model
{
    use HasUuids;
    protected $fillable = [
        'product_id',
        'location_id',
        'expected_quantity',
        'counted_quantity',
        'status',
        'counted_by',
        'resolved_by',
        'resolved_at',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function counter()
    {
        return $this->belongsTo(User::class, 'counted_by');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
