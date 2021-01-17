<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ProductService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    /**
     * @var App\Services\ProductService
     */
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/products",
     *      summary="Get list of Products",
     *      tags={"Products"},
     *      description="Returns list of created Products",
     *      operationId="",
     *      @OA\Parameter(
     *          name="page",
     *          description="Target Page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          description="Rows per page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="filter[name]",
     *          description="Product name",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="filter[url_segment]",
     *          description="URL segment",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="filter[sku]",
     *          description="Product SKU",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *          description="successful operation",
     *          @OA\JsonContent(
     *             example={
     *                 "current_page": 1,
     *                 "data": {
     *                     {
     *                         "id": 1,
     *                         "name": "FooBar DDR4 RAM 3200Mhz",
     *                         "url_segment": "foobar-ddr4-ram-3200mhz",
     *                         "sku": "32424234234",
     *                         "price": 7200.00,
     *                         "created_at": "2021-01-17 18:21:22",
     *                         "updated_at": "2021-01-17 18:21:22"
     *                     },
     *                 },
     *                 "from": 1,
     *                 "last_page": 4,
     *                 "per_page": 1,
     *                 "to": 1,
     *                 "total": 4
     *             }
     *          )
     *       ),
     *       @OA\Response(
     *          response=Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *             example={
     *                 "errors": {
     *                     "page": {
     *                          "The page must be an integer.",
     *                     },
     *                     "per_page": {
     *                          "The per page must be an integer.",
     *                     }
     *                 },
     *             }
     *          )
     *       ),
     *     )
     */
    public function index(Request $request)
    {
        $attributes = $request->all();

        $validator = Validator::make($attributes, [
            'page' => 'integer',
            'per_page' => 'integer',
            'filter.name' => 'nullable|string',
            'filter.url_segment' => 'nullable|string',
            'filter.sku' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response(
                [
                    'errors' => $validator->errors()->getMessages()
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $page = $attributes['page'] ?? self::DEFAULT_PAGE;
        $perPage = $attributes['per_page'] ?? self::DEFAULT_RESULTS_PER_PAGE;

        return Arr::only(
            $this->productService->index($page, $perPage, $attributes['filter'] ?? [])->toArray(),
            [
                "current_page",
                "per_page",
                "total",
                "from",
                "to",
                "last_page",
                "data",
            ]
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Create Products",
     *     tags={"Products"},
     *     description="Creates Products. Note that similar url_segments shall be suffixed by an integer at the end since url_segments MUST BE UNIQUE.",
     *     operationId="",
     *     @OA\RequestBody(
     *         description="ProductsPostRequest",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductsPostRequest")
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_CREATED,
     *          description="successful operation",
     *          @OA\JsonContent(
     *             example={
     *                 "data": {
     *                     "id": 1,
     *                     "name": "FooBar DDR4 RAM 3200Mhz",
     *                     "url_segment": "foobar-ddr4-ram-3200mhz",
     *                     "sku": "32424234234",
     *                     "price": 7200.00,
     *                     "created_at": "2021-01-17 18:21:22",
     *                     "updated_at": "2021-01-17 18:21:22"
     *                 }
     *             }
     *          )
     *       ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "The given data was invalid.",
     *                 "errors": {
     *                     "name": {
     *                         "The name must be a string."
     *                     },
     *                     "url_segment": {
     *                         "The url segment must be a string."
     *                     },
     *                     "sku": {
     *                         "The sku must be an integer."
     *                     },
     *                     "price": {
     *                         "The price must be a number."
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     * )
     *
     * @OA\Schema(
     *     type="object",
     *     schema="ProductsPostRequest",
     *     title="ProductsPostRequest",
     *     description="ProductsPostRequest",
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         format="string",
     *         description="Product Name",
     *         title="product_name",
     *     ),
     *     @OA\Property(
     *         property="url_segment",
     *         type="string",
     *         format="string",
     *         description="Product URL Segment",
     *         title="url_segment",
     *     ),
     *     @OA\Property(
     *         property="sku",
     *         type="string",
     *         format="string",
     *         description="Product SKU",
     *         title="sku",
     *     ),
     *     @OA\Property(
     *         property="price",
     *         type="number",
     *         format="double",
     *         description="Product price",
     *         title="price",
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name' => 'required|string|unique:products,name',
            'url_segment' => 'required|string',
            'sku' => 'required|integer|unique:products,sku',
            'price' => 'required|numeric',
        ]);

        $product = $this->productService->create($attributes);

        return response(['data' => $product->toArray()], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/products/{product_id}",
     *      summary="Get Product by ID",
     *      tags={"Products"},
     *      description="Returns Product details",
     *      operationId="",
     *      @OA\Parameter(
     *          name="product_id",
     *          description="Product ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *          description="successful operation",
     *          @OA\JsonContent(
     *             example={
     *                 "data": {
     *                     "id": 1,
     *                     "name": "FooBar DDR4 RAM 3200Mhz",
     *                     "url_segment": "foobar-ddr4-ram-3200mhz",
     *                     "sku": "32424234234",
     *                     "price": 7200.00,
     *                     "created_at": "2021-01-17 18:21:22",
     *                     "updated_at": "2021-01-17 18:21:22"
     *                 },
     *             }
     *          )
     *       ),
     *       @OA\Response(
     *          response=Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *             example={
     *                 "errors": {
     *                     "id": {
     *                          "The id must be an integer.",
     *                     },
     *                 },
     *             }
     *          )
     *       ),
     *     )
     */
    public function show($productId)
    {
        $validator = Validator::make(['id' => $productId], [
            'id' => 'integer|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response(
                [
                    'errors' => $validator->errors()->getMessages()
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return [
            'data' => $this->productService->get($productId)->toArray(),
        ];
    }

    /**
     * @OA\Get(
     *      path="/api/v1/products/url_segment/{url_segment}",
     *      summary="Get Product by URL Segment",
     *      tags={"Products"},
     *      description="Get Product by URL Segment. Returns Product details",
     *      operationId="",
     *      @OA\Parameter(
     *          name="url_segment",
     *          description="Product URL Segment",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *          description="successful operation",
     *          @OA\JsonContent(
     *             example={
     *                 "data": {
     *                     "id": 1,
     *                     "name": "FooBar DDR4 RAM 3200Mhz",
     *                     "url_segment": "foobar-ddr4-ram-3200mhz",
     *                     "sku": "32424234234",
     *                     "price": 7200.00,
     *                     "created_at": "2021-01-17 18:21:22",
     *                     "updated_at": "2021-01-17 18:21:22"
     *                 },
     *             }
     *          )
     *       ),
     *       @OA\Response(
     *          response=Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *             example={
     *                 "errors": {
     *                     "url_segment": {
     *                          "The url_segment must be a string.",
     *                     },
     *                 },
     *             }
     *          )
     *       ),
     *     )
     */
    public function showByUrlSegment($urlSegment)
    {
        $validator = Validator::make(['url_segment' => $urlSegment], [
            'url_segment' => 'string|exists:products,url_segment',
        ]);

        if ($validator->fails()) {
            return response(
                [
                    'errors' => $validator->errors()->getMessages()
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return [
            'data' => $this->productService->getByUrlSegment($urlSegment)->toArray(),
        ];
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{product_id}",
     *     summary="Update Product details",
     *     tags={"Products"},
     *     description="Update Product details.",
     *     operationId="",
     *     @OA\Parameter(
     *         name="product_id",
     *         description="Product ID",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="ProductsPutRequest",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductsPutRequest")
     *     ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_OK,
     *          description="successful operation",
     *          @OA\JsonContent(
     *             example={
     *                 "data": {
     *                     "id": 1,
     *                     "name": "FooBar DDR4 RAM 3200Mhz",
     *                     "url_segment": "foobar-ddr4-ram-3200mhz",
     *                     "sku": "32424234234",
     *                     "price": 7200.00,
     *                     "created_at": "2021-01-17 18:21:22",
     *                     "updated_at": "2021-01-17 18:21:22"
     *                 }
     *             }
     *          )
     *       ),
     *     @OA\Response(
     *         response=Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "The given data was invalid.",
     *                 "errors": {
     *                     "name": {
     *                         "The name must be a string."
     *                     },
     *                     "url_segment": {
     *                         "The url segment must be a string."
     *                     },
     *                     "sku": {
     *                         "The sku must be an integer."
     *                     },
     *                     "price": {
     *                         "The price must be a number."
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     * )
     *
     * @OA\Schema(
     *     type="object",
     *     schema="ProductsPutRequest",
     *     title="ProductsPutRequest",
     *     description="ProductsPutRequest",
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         format="string",
     *         description="Product Name",
     *         title="product_name",
     *     ),
     *     @OA\Property(
     *         property="url_segment",
     *         type="string",
     *         format="string",
     *         description="Product URL Segment",
     *         title="url_segment",
     *     ),
     *     @OA\Property(
     *         property="sku",
     *         type="string",
     *         format="string",
     *         description="Product SKU",
     *         title="sku",
     *     ),
     *     @OA\Property(
     *         property="price",
     *         type="number",
     *         format="double",
     *         description="Product price",
     *         title="price",
     *     ),
     * )
     */
    public function update(Request $request, $productId)
    {
        $attributes = array_merge($request->all(), ['id' => $productId]);

        $validator = Validator::make($attributes, [
            'id' => 'integer|exists:products,id',
            'name' => 'string',
            'url_segment' => 'string',
            'sku' => 'integer',
            'price' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response(
                [
                    'errors' => $validator->errors()->getMessages()
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $product = $this->productService->update($productId, $attributes);

        return response(['data' => $product->toArray()], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/products/{product_id}",
     *      summary="Delete Product by ID",
     *      tags={"Products"},
     *      description="Deletes Products by ID",
     *      operationId="",
     *      @OA\Parameter(
     *          name="product_id",
     *          description="Product ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT,
     *          description="successful operation",
     *       ),
     *       @OA\Response(
     *          response=Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *             example={
     *                 "errors": {
     *                     "id": {
     *                          "The id must be an integer.",
     *                     },
     *                 },
     *             }
     *          )
     *       ),
     *     )
     */
    public function destroy($productId)
    {
        $validator = Validator::make(['id' => $productId], [
            'id' => 'integer|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response(
                [
                    'errors' => $validator->errors()->getMessages()
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->productService->delete($productId);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
