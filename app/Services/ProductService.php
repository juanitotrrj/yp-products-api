<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProductService
{
    public function index(int $page = 1, int $perPage = 5, $filters = [])
    {
        $query = Product::query();

        if (isset($filters['name']) && !empty($filters['name'])) {
            $query = $query->where('name', 'like', "%{$filters['name']}%");
        }

        if (isset($filters['url_segment']) && !empty($filters['url_segment'])) {
            $query = $query->where('url_segment', 'like', "%{$filters['url_segment']}%");
        }

        if (isset($filters['sku']) && !empty($filters['sku'])) {
            $query = $query->where('sku', 'like', "%{$filters['sku']}%");
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function get($id)
    {
        return Product::find($id);
    }

    public function create(array $attributes)
    {
        $attributes['url_segment'] = $this->getUrlSegment($attributes);

        return Product::create($attributes);
    }

    public function delete($productId)
    {
        $product = Product::find($productId);
        $product->delete();
    }

    public function update($productId, array $attributes)
    {
        $attributes['url_segment'] = $this->getUrlSegment($attributes);

        $product = Product::find($productId);
        $product->update($attributes);
        $product->refresh();

        return $product;
    }

    private function getUrlSegment($attributes)
    {
        $urlSegment = Str::slug($attributes['url_segment'], '-');
        $numberOfSimilarUrlSegments = Product::where('url_segment', 'like', "{$urlSegment}%")->count();
        if ($numberOfSimilarUrlSegments > 0) {
            $numberOfSimilarUrlSegments++;
            $urlSegment .= "-{$numberOfSimilarUrlSegments}";
        }

        return $urlSegment;
    }
}
