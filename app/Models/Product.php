<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'name',
        'slug',
        'price',
        'weight',
        'short_description',
        'description',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = static::generateUniqueSlug($product->name);
        });

        static::updating(function ($product){
            if($product->isDirty('name')){
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
            }
        });
        
    }

    public static function generateUniqueSlug($name, $id = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (self::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function productCategories(){
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id','category_id')->withPivot('id');
    }

    public function productImage(){
        return $this->hasOne(productsImage::class, 'product_id', 'id');
    }

    public function productInventory() {
        return $this->hasOne(ProductsInventory::class, 'product_id', 'id');
    }

    public function productsWishlists(){
        return $this->belongsToMany(User::class, 'wishlists', 'product_id', 'user_id')->withPivot('id');
    }

    public function cartItmes(){
        return $this->belongsToMany(Cart::class, 'cart_items', 'product_id','cart_id')->withPivot('id');
    }
}
