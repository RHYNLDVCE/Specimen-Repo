<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Taxon\StoreTaxonRequest;
use App\Http\Requests\Taxon\UpdateTaxonRequest;
use App\Http\Resources\TaxonResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Taxon;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class TaxonController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/taxa",
     *     summary="List Taxa",
     *     description="Retrieve paginated list of taxonomic records with optional search.",
     *     tags={"Taxonomy"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="search", in="query", description="Search by scientific name, common name, family, genus", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="kingdom", in="query", description="Filter by kingdom", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer", default=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of taxa",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Taxa retrieved successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Taxon"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Taxon::query()->withCount('specimens');

        if ($request->filled('kingdom')) {
            $query->where('kingdom', $request->input('kingdom'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $regex = ['$regex' => $search, '$options' => 'i'];
            $query->where(function ($q) use ($regex) {
                $q->where('scientific_name', 'regex', $regex)
                  ->orWhere('common_name', 'regex', $regex)
                  ->orWhere('family', 'regex', $regex)
                  ->orWhere('genus', 'regex', $regex);
            });
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $query->orderBy('scientific_name', 'asc')->paginate($perPage);

        return $this->respondPaginated(
            $paginated,
            TaxonResource::collection($paginated->items()),
            'Taxa retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/taxa",
     *     summary="Create Taxon",
     *     description="Register a new taxonomic classification.",
     *     tags={"Taxonomy"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"scientific_name"},
     *             @OA\Property(property="scientific_name", type="string", example="Tarsius syrichta"),
     *             @OA\Property(property="common_name", type="string", example="Philippine Tarsier"),
     *             @OA\Property(property="kingdom", type="string", example="Animalia"),
     *             @OA\Property(property="phylum", type="string", example="Chordata"),
     *             @OA\Property(property="class", type="string", example="Mammalia"),
     *             @OA\Property(property="order", type="string", example="Primates"),
     *             @OA\Property(property="family", type="string", example="Tarsiidae"),
     *             @OA\Property(property="genus", type="string", example="Tarsius"),
     *             @OA\Property(property="species", type="string", example="syrichta"),
     *             @OA\Property(property="authority", type="string", example="Linnaeus, 1758")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Taxon created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Taxon created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Taxon")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(StoreTaxonRequest $request): JsonResponse
    {
        $taxon = Taxon::create($request->validated());

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'CREATE_TAXON',
            resourceType: 'Taxon',
            resourceId: (string) $taxon->_id,
            description: "Created taxonomic entry {$taxon->scientific_name}."
        );

        return $this->respondCreated(
            new TaxonResource($taxon),
            'Taxon created successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/taxa/{id}",
     *     summary="Get Taxon Details",
     *     description="Retrieve details of a single taxon by ID.",
     *     tags={"Taxonomy"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Taxon ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Taxon details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Taxon")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Taxon not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $taxon = Taxon::withCount('specimens')->find($id);

        if (! $taxon) {
            return $this->respondNotFound('Taxon not found.');
        }

        return $this->respondSuccess(
            new TaxonResource($taxon),
            'Taxon retrieved successfully.'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/taxa/{id}",
     *     summary="Update Taxon",
     *     description="Update details of an existing taxon.",
     *     tags={"Taxonomy"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Taxon ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="common_name", type="string", example="Philippine Tarsier"),
     *             @OA\Property(property="authority", type="string", example="Linnaeus, 1758")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Taxon updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Taxon")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Taxon not found"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function update(UpdateTaxonRequest $request, string $id): JsonResponse
    {
        $taxon = Taxon::find($id);

        if (! $taxon) {
            return $this->respondNotFound('Taxon not found.');
        }

        $taxon->update($request->validated());

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'UPDATE_TAXON',
            resourceType: 'Taxon',
            resourceId: (string) $taxon->_id,
            description: "Updated taxonomic entry {$taxon->scientific_name}."
        );

        return $this->respondSuccess(
            new TaxonResource($taxon->fresh()),
            'Taxon updated successfully.'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/taxa/{id}",
     *     summary="Delete Taxon",
     *     description="Delete a taxon record.",
     *     tags={"Taxonomy"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Taxon ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Taxon deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Taxon deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Taxon not found")
     * )
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $taxon = Taxon::find($id);

        if (! $taxon) {
            return $this->respondNotFound('Taxon not found.');
        }

        $name = $taxon->scientific_name;
        $taxonId = (string) $taxon->_id;

        $taxon->delete();

        $this->auditLogService->log(
            admin: $request->user(),
            action: 'DELETE_TAXON',
            resourceType: 'Taxon',
            resourceId: $taxonId,
            description: "Deleted taxon {$name}."
        );

        return $this->respondSuccess(null, 'Taxon deleted successfully.');
    }
}
