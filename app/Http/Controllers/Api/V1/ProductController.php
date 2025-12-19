<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Http\Filters\ProductFilter; 

class ProductController extends ApiController
{

    public function __construct()
    {
        $this->authorizeResource(Product::class);
    }

    /**
     * List all products
     * 
     * @group Product API Resource
     * @queryParam sort by product name
     * @queryParam filter[title] Filter by name. Wildcards are supported. Example: *fix*
     */
    public function index(ProductFilter $productFilter)
    {
        return ProductResource::collection(
            Product::filter($productFilter)->orderBy('created_at', 'DESC')->paginate()
        );
    } 

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

}
