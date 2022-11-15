<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Get the products for the Category.
     */
    public function products(): object
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
