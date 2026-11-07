<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AuditLogController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/v1/audit-logs",
     *     summary="List Audit Logs",
     *     description="Retrieve paginated administrative audit logs tracking system actions.",
     *     tags={"Audit Logs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="action", in="query", description="Filter by action (e.g. LOGIN, CREATE_SPECIMEN, APPROVE_LOAN)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="resource_type", in="query", description="Filter by resource type (e.g. Specimen, Loan)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer", default=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of audit logs",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = AuditLog::query();

        if ($request->filled('action')) {
            $query->where('action', strtoupper($request->input('action')));
        }

        if ($request->filled('resource_type')) {
            $query->where('resource_type', $request->input('resource_type'));
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->respondPaginated(
            $paginated,
            AuditLogResource::collection($paginated->items()),
            'Audit logs retrieved successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/audit-logs/{id}",
     *     summary="Get Audit Log Details",
     *     description="Retrieve details of a single audit log entry by ID.",
     *     tags={"Audit Logs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Audit Log ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Audit log details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Audit log not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $log = AuditLog::find($id);

        if (! $log) {
            return $this->respondNotFound('Audit log entry not found.');
        }

        return $this->respondSuccess(
            new AuditLogResource($log),
            'Audit log retrieved successfully.'
        );
    }
}
