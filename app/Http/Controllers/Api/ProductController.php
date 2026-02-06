<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;


class ProductController extends Controller
{
    /**
     *  Get Product List
     *
     * @param ProductFilterRequest $request
     * @return JsonResponse
     */
    public function index(ProductFilterRequest $request): JsonResponse
    {

        $products = Product::with('category')
            ->search($request->q)
            ->priceRange($request->price_from, $request->price_to)
            ->byCategory($request->category_id)
            ->inStock($request->in_stock)
            ->minRating($request->rating_from)
            ->sortBy($request->sort)
            ->paginate($request->per_page ?? 10);

        return response()->json($products);
    }

    /**
     *  Get Product By ID
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $product = Product::with('category')->find($id);

        if (! $product) {
            return response()->json([
                'message' => __('validation.product.not_found'),
                'errors' => [
                    'id' => [__('validation.product.not_found_by_id')],
                ],
            ], 404);
        }

        return response()->json([
            'data' => $product,
        ]);
    }
}
