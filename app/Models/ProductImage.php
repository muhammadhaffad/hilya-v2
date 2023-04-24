<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $appends = ['imagefullpath'];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function getImagefullpathAttribute() {
        return asset('storage/'.$this->image);
    }
}
