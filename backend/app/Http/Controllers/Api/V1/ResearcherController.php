<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Researcher\StoreResearcherRequest;
use App\Http\Requests\Researcher\UpdateResearcherRequest;
use App\Http\Resources\ResearcherResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Researcher;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ResearcherController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/researchers",
     *     summary="List Researchers",
     *     description="Retrieve paginated list of researchers and scientists.",
     *     tags={"Researchers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="search", in="query", description="Search by name, email, institution, specialization", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="status", in="query", description="Filter by status (active, inactive)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer", default=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of researchers",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Researcher"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Researcher::query()->withCount(['loans', 'projects']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $regex = ['$regex' => $search, '$options' => 'i'];
            $query->where(function ($q) use ($regex) {
                $q->where('name', 'regex', $regex)
                  ->orWhere('email', 'regex', $regex)
                  ->orWhere('institution', 'regex', $regex)
                  ->orWhere('specialization', 'regex', $regex);
            });
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $query->orderBy('name', 'asc')->paginate($perPage);

        return $this->respondPaginated(
            $paginated,
            ResearcherResource::collection($paginated->items()),
            'Researchers retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/researchers",
     *     summary="Register Researcher",
     *     description="Register a new researcher or external investigator.",
     *     tags={"Researchers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email"},
     *             @OA\Property(property="name", type="string", example="Dr. Elena Rostova"),
     *             @OA\Property(property="email", type="string", format="email", example="e.rostova@university.edu"),
     *             @OA\Property(property="institution", type="string", example="State University Department of Biology"),
     *             @OA\Property(property="department", type="string", example="Zoology Division"),
     *             @OA\Property(property="specialization", type="string", example="Raptor Osteology"),
     *             @OA\Property(property="contact_information", type="string", example="+1-555-0199 / Lab 402"),
     *             @OA\Property(property="status", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Researcher registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Researcher")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(StoreResearcherRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] = $data['status'] ?? 'active';

        $researcher = Researcher::create($data);

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'CREATE_RESEARCHER',
            resourceType: 'Researcher',
            resourceId: (string) $researcher->_id,
            description: "Registered researcher {$researcher->name} ({$researcher->email})."
        );

        return $this->respondCreated(
            new ResearcherResource($researcher),
            'Researcher registered successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/researchers/{id}",
     *     summary="Get Researcher Details",
     *     description="Retrieve details of a single researcher by ID.",
     *     tags={"Researchers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Researcher ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Researcher details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Researcher")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Researcher not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $researcher = Researcher::withCount(['loans', 'projects'])->find($id);

        if (! $researcher) {
            return $this->respondNotFound('Researcher not found.');
        }

        return $this->respondSuccess(
            new ResearcherResource($researcher),
            'Researcher retrieved successfully.'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/researchers/{id}",
     *     summary="Update Researcher",
     *     description="Update details of an existing researcher.",
     *     tags={"Researchers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Researcher ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="institution", type="string", example="National Institute of Biodiversity"),
     *             @OA\Property(property="specialization", type="string", example="Avian Genomics and Morphology")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Researcher updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Researcher")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Researcher not found"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function update(UpdateResearcherRequest $request, string $id): JsonResponse
    {
        $researcher = Researcher::find($id);

        if (! $researcher) {
            return $this->respondNotFound('Researcher not found.');
        }

        $researcher->update($request->validated());

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'UPDATE_RESEARCHER',
            resourceType: 'Researcher',
            resourceId: (string) $researcher->_id,
            description: "Updated researcher {$researcher->name}."
        );

        return $this->respondSuccess(
            new ResearcherResource($researcher->fresh()),
            'Researcher updated successfully.'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/researchers/{id}",
     *     summary="Delete Researcher",
     *     description="Delete a researcher record.",
     *     tags={"Researchers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Researcher ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Researcher deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Researcher deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Researcher not found")
     * )
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $researcher = Researcher::find($id);

        if (! $researcher) {
            return $this->respondNotFound('Researcher not found.');
        }

        $name = $researcher->name;
        $researcherId = (string) $researcher->_id;

        $researcher->delete();

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'DELETE_RESEARCHER',
            resourceType: 'Researcher',
            resourceId: $researcherId,
            description: "Deleted researcher {$name}."
        );

        return $this->respondSuccess(null, 'Researcher deleted successfully.');
    }
}
