<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeWithBank($query, $status)
    {
        return $query->whereIn($status);
    }

    public function scopeWithoutStatus($query, $status)
    {
        return $query->whereNotIn('status', $status);
    }

    public function scopeWithStatus($query, $status)
    {
        return $query->whereIn('status', $status);
    }
}
