<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Collection\StoreCollectionRequest;
use App\Http\Requests\Collection\UpdateCollectionRequest;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\SpecimenResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Collection;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CollectionController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/collections",
     *     summary="List Collections",
     *     description="Retrieve paginated list of specimen collections.",
     *     tags={"Collections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="search", in="query", description="Search collection name or collector", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="status", in="query", description="Filter by status (active, archived, closed)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer", default=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of collections",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Collection"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Collection::query()->withCount('specimens')->with('creator');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $regex = ['$regex' => $search, '$options' => 'i'];
            $query->where(function ($q) use ($regex) {
                $q->where('name', 'regex', $regex)
                  ->orWhere('collector', 'regex', $regex)
                  ->orWhere('collection_type', 'regex', $regex);
            });
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $query->orderBy('name', 'asc')->paginate($perPage);

        return $this->respondPaginated(
            $paginated,
            CollectionResource::collection($paginated->items()),
            'Collections retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/collections",
     *     summary="Create Collection",
     *     description="Create a new specimen collection.",
     *     tags={"Collections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Mindanao Highland Flora & Fauna"),
     *             @OA\Property(property="description", type="string", example="Biodiversity survey of high-elevation taxa"),
     *             @OA\Property(property="collection_type", type="string", example="Regional Biodiversity"),
     *             @OA\Property(property="collector", type="string", example="Prof. Juan dela Cruz"),
     *             @OA\Property(property="date_started", type="string", format="date", example="2025-06-01"),
     *             @OA\Property(property="status", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Collection created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Collection")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(StoreCollectionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->_id;
        $data['status'] = $data['status'] ?? 'active';

        $collection = Collection::create($data);

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'CREATE_COLLECTION',
            resourceType: 'Collection',
            resourceId: (string) $collection->_id,
            description: "Created collection {$collection->name}."
        );

        return $this->respondCreated(
            new CollectionResource($collection->load('creator')),
            'Collection created successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/collections/{id}",
     *     summary="Get Collection Details",
     *     description="Retrieve details of a single collection by ID.",
     *     tags={"Collections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Collection ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Collection details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Collection")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Collection not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $collection = Collection::withCount('specimens')->with('creator')->find($id);

        if (! $collection) {
            return $this->respondNotFound('Collection not found.');
        }

        return $this->respondSuccess(
            new CollectionResource($collection),
            'Collection retrieved successfully.'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/collections/{id}",
     *     summary="Update Collection",
     *     description="Update details of an existing collection.",
     *     tags={"Collections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Collection ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string", example="Expanded survey of high-elevation taxa"),
     *             @OA\Property(property="status", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collection updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Collection")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Collection not found"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function update(UpdateCollectionRequest $request, string $id): JsonResponse
    {
        $collection = Collection::find($id);

        if (! $collection) {
            return $this->respondNotFound('Collection not found.');
        }

        $collection->update($request->validated());

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'UPDATE_COLLECTION',
            resourceType: 'Collection',
            resourceId: (string) $collection->_id,
            description: "Updated collection {$collection->name}."
        );

        return $this->respondSuccess(
            new CollectionResource($collection->fresh(['creator'])),
            'Collection updated successfully.'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/collections/{id}",
     *     summary="Delete Collection",
     *     description="Delete a collection record.",
     *     tags={"Collections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Collection ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Collection deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Collection deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Collection not found")
     * )
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $collection = Collection::find($id);

        if (! $collection) {
            return $this->respondNotFound('Collection not found.');
        }

        $name = $collection->name;
        $collectionId = (string) $collection->_id;

        $collection->delete();

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'DELETE_COLLECTION',
            resourceType: 'Collection',
            resourceId: $collectionId,
            description: "Deleted collection {$name}."
        );

        return $this->respondSuccess(null, 'Collection deleted successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/collections/{id}/specimens",
     *     summary="List Specimens Belonging to Collection",
     *     description="Retrieve all specimens categorized under a specific collection.",
     *     tags={"Collections"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Collection ID", @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer", default=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated specimens in collection",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Specimen"))
     *         )
     *     ),
     *     @OA\Response(response=404, description="Collection not found")
     * )
     */
    public function specimens(Request $request, string $id): JsonResponse
    {
        $collection = Collection::find($id);

        if (! $collection) {
            return $this->respondNotFound('Collection not found.');
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $collection->specimens()
            ->with(['taxon', 'location', 'project'])
            ->orderBy('specimen_code', 'asc')
            ->paginate($perPage);

        return $this->respondPaginated(
            $paginated,
            SpecimenResource::collection($paginated->items()),
            "Specimens for collection '{$collection->name}' retrieved successfully."
        );
    }
}
