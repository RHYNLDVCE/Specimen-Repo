<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Location\StoreLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Location;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class LocationController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/locations",
     *     summary="List Locations",
     *     description="Retrieve paginated list of collection sites and repository storage locations.",
     *     tags={"Locations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="search", in="query", description="Search location name, province, municipality", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="province", in="query", description="Filter by province", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer", default=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of locations",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Location"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Location::query()->withCount('specimens');

        if ($request->filled('province')) {
            $query->where('province', $request->input('province'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $regex = ['$regex' => $search, '$options' => 'i'];
            $query->where(function ($q) use ($regex) {
                $q->where('name', 'regex', $regex)
                  ->orWhere('province', 'regex', $regex)
                  ->orWhere('municipality', 'regex', $regex)
                  ->orWhere('habitat', 'regex', $regex);
            });
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $query->orderBy('name', 'asc')->paginate($perPage);

        return $this->respondPaginated(
            $paginated,
            LocationResource::collection($paginated->items()),
            'Locations retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/locations",
     *     summary="Create Location",
     *     description="Register a new collection site or repository location.",
     *     tags={"Locations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Mount Apo Natural Park"),
     *             @OA\Property(property="description", type="string", example="Primary forest collection site"),
     *             @OA\Property(property="province", type="string", example="Davao del Sur"),
     *             @OA\Property(property="municipality", type="string", example="Bansalan"),
     *             @OA\Property(property="barangay", type="string", example="Managa"),
     *             @OA\Property(property="latitude", type="number", format="float", example=6.9875),
     *             @OA\Property(property="longitude", type="number", format="float", example=125.2711),
     *             @OA\Property(property="habitat", type="string", example="Montane mossy forest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Location created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Location")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(StoreLocationRequest $request): JsonResponse
    {
        $location = Location::create($request->validated());

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'CREATE_LOCATION',
            resourceType: 'Location',
            resourceId: (string) $location->_id,
            description: "Created location {$location->name}."
        );

        return $this->respondCreated(
            new LocationResource($location),
            'Location created successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/locations/{id}",
     *     summary="Get Location Details",
     *     description="Retrieve details of a single location by ID.",
     *     tags={"Locations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Location ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Location details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Location")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Location not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $location = Location::withCount('specimens')->find($id);

        if (! $location) {
            return $this->respondNotFound('Location not found.');
        }

        return $this->respondSuccess(
            new LocationResource($location),
            'Location retrieved successfully.'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/locations/{id}",
     *     summary="Update Location",
     *     description="Update details of an existing location.",
     *     tags={"Locations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Location ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="habitat", type="string", example="Subalpine and mossy forest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Location")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Location not found"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function update(UpdateLocationRequest $request, string $id): JsonResponse
    {
        $location = Location::find($id);

        if (! $location) {
            return $this->respondNotFound('Location not found.');
        }

        $location->update($request->validated());

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'UPDATE_LOCATION',
            resourceType: 'Location',
            resourceId: (string) $location->_id,
            description: "Updated location {$location->name}."
        );

        return $this->respondSuccess(
            new LocationResource($location->fresh()),
            'Location updated successfully.'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/locations/{id}",
     *     summary="Delete Location",
     *     description="Delete a location record.",
     *     tags={"Locations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Location ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Location deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Location deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Location not found")
     * )
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $location = Location::find($id);

        if (! $location) {
            return $this->respondNotFound('Location not found.');
        }

        $name = $location->name;
        $locationId = (string) $location->_id;

        $location->delete();

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'DELETE_LOCATION',
            resourceType: 'Location',
            resourceId: $locationId,
            description: "Deleted location {$name}."
        );

        return $this->respondSuccess(null, 'Location deleted successfully.');
    }
}
