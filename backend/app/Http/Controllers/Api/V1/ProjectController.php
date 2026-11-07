<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\SpecimenResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Project;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ProjectController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/projects",
     *     summary="List Projects",
     *     description="Retrieve paginated list of research projects.",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="search", in="query", description="Search by title, code, description", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="status", in="query", description="Filter by status (planning, active, completed, suspended)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer", default=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of projects",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Project"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::query()->with(['principalResearcher'])->withCount('specimens');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $regex = ['$regex' => $search, '$options' => 'i'];
            $query->where(function ($q) use ($regex) {
                $q->where('title', 'regex', $regex)
                  ->orWhere('project_code', 'regex', $regex)
                  ->orWhere('description', 'regex', $regex);
            });
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->respondPaginated(
            $paginated,
            ProjectResource::collection($paginated->items()),
            'Projects retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/projects",
     *     summary="Create Project",
     *     description="Register a new research project.",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"project_code","title"},
     *             @OA\Property(property="project_code", type="string", example="PRJ-RAPTOR-2026"),
     *             @OA\Property(property="title", type="string", example="Southeast Asian Raptor Evolutionary Morphology"),
     *             @OA\Property(property="description", type="string", example="Comparative morphology across island archipelagos"),
     *             @OA\Property(property="principal_researcher_id", type="string", example="67d280e5b7a5a8f4c201a050"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2026-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2027-12-31"),
     *             @OA\Property(property="status", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Project created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Project")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] = $data['status'] ?? 'active';

        $project = Project::create($data);

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'CREATE_PROJECT',
            resourceType: 'Project',
            resourceId: (string) $project->_id,
            description: "Created project {$project->project_code} ({$project->title})."
        );

        return $this->respondCreated(
            new ProjectResource($project->load('principalResearcher')),
            'Project created successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/projects/{id}",
     *     summary="Get Project Details",
     *     description="Retrieve details of a single research project by ID.",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Project ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Project details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Project")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Project not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $project = Project::with(['principalResearcher'])->withCount('specimens')->find($id);

        if (! $project) {
            return $this->respondNotFound('Project not found.');
        }

        return $this->respondSuccess(
            new ProjectResource($project),
            'Project retrieved successfully.'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/projects/{id}",
     *     summary="Update Project",
     *     description="Update details of an existing research project.",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Project ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Project Title"),
     *             @OA\Property(property="status", type="string", example="completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Project")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Project not found"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function update(UpdateProjectRequest $request, string $id): JsonResponse
    {
        $project = Project::find($id);

        if (! $project) {
            return $this->respondNotFound('Project not found.');
        }

        $project->update($request->validated());

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'UPDATE_PROJECT',
            resourceType: 'Project',
            resourceId: (string) $project->_id,
            description: "Updated project {$project->project_code}."
        );

        return $this->respondSuccess(
            new ProjectResource($project->fresh(['principalResearcher'])),
            'Project updated successfully.'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/projects/{id}",
     *     summary="Delete Project",
     *     description="Delete a research project.",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Project ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Project deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Project deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Project not found")
     * )
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $project = Project::find($id);

        if (! $project) {
            return $this->respondNotFound('Project not found.');
        }

        $code = $project->project_code;
        $projectId = (string) $project->_id;

        $project->delete();

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'DELETE_PROJECT',
            resourceType: 'Project',
            resourceId: $projectId,
            description: "Deleted project {$code}."
        );

        return $this->respondSuccess(null, 'Project deleted successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/projects/{id}/specimens",
     *     summary="List Specimens Belonging to Project",
     *     description="Retrieve all biological specimens associated with this project.",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Project ID", @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer", default=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of specimens in project",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Specimen"))
     *         )
     *     ),
     *     @OA\Response(response=404, description="Project not found")
     * )
     */
    public function specimens(Request $request, string $id): JsonResponse
    {
        $project = Project::find($id);

        if (! $project) {
            return $this->respondNotFound('Project not found.');
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $project->specimens()
            ->with(['taxon', 'collection', 'location'])
            ->orderBy('specimen_code', 'asc')
            ->paginate($perPage);

        return $this->respondPaginated(
            $paginated,
            SpecimenResource::collection($paginated->items()),
            "Specimens for project '{$project->project_code}' retrieved successfully."
        );
    }
}
