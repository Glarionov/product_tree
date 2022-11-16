<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public $fillable = ['name', 'description', 'index', 'parent_id'];

    /**
     * Get the products for the Category.
     */
    public function products(): object
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
