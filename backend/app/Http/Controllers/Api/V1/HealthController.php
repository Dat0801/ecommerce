<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

/**
 * @OA\OpenApi(
 *     info=@OA\Info(
 *         version="1.0.0",
 *         title="E-Commerce API",
 *         description="Production-ready E-Commerce REST API",
 *         contact=@OA\Contact(
 *             email="support@ecommerce.local"
 *         ),
 *         license=@OA\License(
 *             name="MIT"
 *         )
 *     ),
 *     servers={
 *         @OA\Server(
 *             url="http://localhost:8000/api/v1",
 *             description="Local Server"
 *         )
 *     }
 * )
 */
class HealthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/ping",
     *     operationId="healthCheck",
     *     tags={"Health"},
     *     summary="Health check endpoint",
     *     description="Returns API status",
     *     @OA\Response(
     *         response=200,
     *         description="API is healthy",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="healthy"),
     *             @OA\Property(property="timestamp", type="string", example="2024-01-11T12:00:00Z")
     *         )
     *     )
     * )
     */
    public function ping()
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'api_version' => 'v1',
        ]);
    }
}
