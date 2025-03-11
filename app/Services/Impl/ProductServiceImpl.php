<?php

namespace App\Services\Impl;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\Storage;

class ProductServiceImpl implements ProductService
{
    public function saveProduct(array $product, array $categories,string $path, int $qty)
    {
        $product = Product::create($product);

        $product->productCategories()->attach($categories);

        $product->productImage()->create([
            'path' => $path
        ]);

        $product->productInventory()->create([
            'qty' => $qty
        ]);

    }
    
    public function getProduct()
    {
        // return Product::query()->get()->toArray();
        return Product::with('productImage')->get();
    }

    public function removeProduct(int $id)
    {
        $product = Product::query()->find($id);
        if($product != null){
            $product->productCategories;

            $pivot = $product->pivot;
            $product->productCategories()->detach($pivot);

            $image = $product->productImage->path;
            Storage::disk('public')->delete($image);

            $product->productsWishlists()->detach($pivot);

            $product->delete();
        }
    }

    public function updateProduct(string $slug,array $product, array $categories, string $path, int $qty )
    {
        Product::where('slug',$slug)->update($product);
        $product = Product::where('slug', $slug)->first();
        $product->productCategories()->sync($categories);
        $product->productImage()->update([
            'path' => $path
        ]);
        $product->productInventory()->update([
            'qty' => $qty
        ]);

    }

}