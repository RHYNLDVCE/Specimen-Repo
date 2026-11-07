<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AdminResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     summary="Admin Login",
     *     description="Authenticate administrator with email and password to receive a Bearer API token.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@specimenrepo.local"),
     *             @OA\Property(property="password", type="string", format="password", example="SecretPassword123!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful authentication",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="admin", type="object",
     *                     @OA\Property(property="id", type="string", example="67d280e5b7a5a8f4c201a011"),
     *                     @OA\Property(property="name", type="string", example="Super Administrator"),
     *                     @OA\Property(property="email", type="string", example="admin@specimenrepo.local"),
     *                     @OA\Property(property="role", type="string", example="super_admin"),
     *                     @OA\Property(property="status", type="string", example="active")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|2abf8e819b5b244ce2f8319f61b0c034509172")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return $this->respondSuccess([
            'admin' => new AdminResource($result['admin']),
            'token' => $result['token'],
        ], 'Login successful.');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     summary="Admin Logout",
     *     description="Invalidate current administrator authentication token.",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully logged out.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->respondSuccess(null, 'Successfully logged out.');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     summary="Current Authenticated Admin",
     *     description="Retrieve details of the currently authenticated administrator.",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Current administrator profile",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Authenticated administrator profile."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="67d280e5b7a5a8f4c201a011"),
     *                 @OA\Property(property="name", type="string", example="Super Administrator"),
     *                 @OA\Property(property="email", type="string", example="admin@specimenrepo.local"),
     *                 @OA\Property(property="role", type="string", example="super_admin"),
     *                 @OA\Property(property="status", type="string", example="active")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return $this->respondSuccess(
            new AdminResource($request->user()),
            'Authenticated administrator profile.'
        );
    }
}
