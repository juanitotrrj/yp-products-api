<?php

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Products API",
 *         description="Products API",
 *         termsOfService="",
 *         @OA\Contact(
 *             email="juanitotarroja@gmail.com"
 *         ),
 *     )
 * )
 * @
 * OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="Bearer",
 *         type="apiKey",
 *         name="Authorization",
 *         in="header",
 *         bearerFormat="JWT"
 *     ),
 * )
 */

/**
 * Common schemas
 *
 * @OA\Schema(
 *     type="object",
 *     schema="HttpResponseNotFound",
 *     title="HttpResponseNotFound",
 *     description="Not found http response",
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="status_code", type="integer", format="int32", example="404")
 * )
 *
 * @OA\Schema(
 *     type="object",
 *     schema="HttpResponseNotAcceptable",
 *     title="HttpResponseNotAcceptable",
 *     description="Not acceptable http response",
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="status_code", type="integer", format="int32", example="406")
 * )
 *
 * @OA\Schema(
 *     type="object",
 *     schema="HttpResponseValidationError",
 *     title="HttpResponseValidationError",
 *     description="Validation Error http response",
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="status_code", type="integer", format="int32", example="422"),
 *     @OA\Property(property="errors", type="object")
 * )
 */
