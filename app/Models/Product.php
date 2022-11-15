<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Get the category associated with the product.
     */
    public function category(): object
    {
        return $this->belongsTo(Category::class);
    }
}
