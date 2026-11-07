<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Specimen\StoreSpecimenRequest;
use App\Http\Requests\Specimen\UpdateSpecimenRequest;
use App\Http\Requests\Specimen\UploadDocumentRequest;
use App\Http\Requests\Specimen\UploadImageRequest;
use App\Http\Resources\SpecimenDocumentResource;
use App\Http\Resources\SpecimenImageResource;
use App\Http\Resources\SpecimenResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Specimen;
use App\Repositories\SpecimenRepository;
use App\Services\MediaService;
use App\Services\SpecimenService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class SpecimenController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected SpecimenRepository $specimenRepository,
        protected SpecimenService $specimenService,
        protected MediaService $mediaService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/specimens",
     *     summary="List Specimens",
     *     description="Retrieve paginated list of biological specimens with optional search, filtering, and sorting.",
     *     tags={"Specimens"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer", default=20)),
     *     @OA\Parameter(name="search", in="query", description="Search query across scientific name, common name, code, genus, species", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="status", in="query", description="Filter by status (active, loaned, damaged, lost, deaccessioned)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="collection_id", in="query", description="Filter by collection ID", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="taxon_id", in="query", description="Filter by taxon ID", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="location_id", in="query", description="Filter by location ID", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort_by", in="query", description="Field to sort by (created_at, specimen_code, scientific_name, collection_date)", required=false, @OA\Schema(type="string", default="created_at")),
     *     @OA\Parameter(name="sort_order", in="query", description="Sort direction (asc or desc)", required=false, @OA\Schema(type="string", default="desc")),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of specimens",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Specimens retrieved successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Specimen")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=20),
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="last_page", type="integer", example=5)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'status', 'collection_id', 'taxon_id', 'location_id',
            'project_id', 'kingdom', 'family', 'genus', 'search',
            'sort_by', 'sort_order'
        ]);

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $this->specimenRepository->paginate($filters, $perPage);

        return $this->respondPaginated(
            $paginated,
            SpecimenResource::collection($paginated->items()),
            'Specimens retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/specimens",
     *     summary="Create Specimen",
     *     description="Register a new biological specimen.",
     *     tags={"Specimens"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"specimen_code","scientific_name"},
     *             @OA\Property(property="specimen_code", type="string", example="SPEC-2026-001"),
     *             @OA\Property(property="catalog_number", type="string", example="CAT-00123"),
     *             @OA\Property(property="scientific_name", type="string", example="Pithecophaga jefferyi"),
     *             @OA\Property(property="common_name", type="string", example="Philippine Eagle"),
     *             @OA\Property(property="kingdom", type="string", example="Animalia"),
     *             @OA\Property(property="phylum", type="string", example="Chordata"),
     *             @OA\Property(property="class", type="string", example="Aves"),
     *             @OA\Property(property="order", type="string", example="Accipitriformes"),
     *             @OA\Property(property="family", type="string", example="Accipitridae"),
     *             @OA\Property(property="genus", type="string", example="Pithecophaga"),
     *             @OA\Property(property="species", type="string", example="jefferyi"),
     *             @OA\Property(property="taxon_id", type="string", example="67d280e5b7a5a8f4c201a020"),
     *             @OA\Property(property="collection_id", type="string", example="67d280e5b7a5a8f4c201a030"),
     *             @OA\Property(property="location_id", type="string", example="67d280e5b7a5a8f4c201a040"),
     *             @OA\Property(property="collector", type="string", example="Dr. Maria Santos"),
     *             @OA\Property(property="collection_date", type="string", format="date", example="2026-03-15"),
     *             @OA\Property(property="preservation_method", type="string", example="skeletal"),
     *             @OA\Property(property="status", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Specimen created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Specimen created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Specimen")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(StoreSpecimenRequest $request): JsonResponse
    {
        $specimen = $this->specimenService->create($request->validated(), $request->user());

        return $this->respondCreated(
            new SpecimenResource($specimen),
            'Specimen created successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/specimens/{id}",
     *     summary="Get Specimen Details",
     *     description="Retrieve complete record for a single specimen by ID.",
     *     tags={"Specimens"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Specimen ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Specimen record",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Specimen retrieved successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Specimen")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Specimen not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $specimen = $this->specimenRepository->findById($id);

        if (! $specimen) {
            return $this->respondNotFound('Specimen not found.');
        }

        return $this->respondSuccess(
            new SpecimenResource($specimen),
            'Specimen retrieved successfully.'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/specimens/{id}",
     *     summary="Update Specimen",
     *     description="Update details of an existing specimen.",
     *     tags={"Specimens"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Specimen ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="common_name", type="string", example="Great Philippine Eagle"),
     *             @OA\Property(property="preservation_method", type="string", example="dry"),
     *             @OA\Property(property="status", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Specimen updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Specimen updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Specimen")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Specimen not found"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function update(UpdateSpecimenRequest $request, string $id): JsonResponse
    {
        $specimen = Specimen::find($id);

        if (! $specimen) {
            return $this->respondNotFound('Specimen not found.');
        }

        $updated = $this->specimenService->update($specimen, $request->validated(), $request->user());

        return $this->respondSuccess(
            new SpecimenResource($updated),
            'Specimen updated successfully.'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/specimens/{id}",
     *     summary="Delete Specimen",
     *     description="Remove a specimen record (super_admin or admin role required).",
     *     tags={"Specimens"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Specimen ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Specimen deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Specimen deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Cannot delete loaned specimen"),
     *     @OA\Response(response=404, description="Specimen not found")
     * )
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $specimen = Specimen::find($id);

        if (! $specimen) {
            return $this->respondNotFound('Specimen not found.');
        }

        try {
            $this->specimenService->delete($specimen, $request->user());

            return $this->respondSuccess(null, 'Specimen deleted successfully.');
        } catch (DomainException $e) {
            return $this->respondError($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/specimens/{id}/images",
     *     summary="List Specimen Images",
     *     description="Retrieve all uploaded images for a specimen.",
     *     tags={"Specimens"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Specimen ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="List of specimen images",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Specimen images retrieved successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function images(string $id): JsonResponse
    {
        $specimen = Specimen::with(['images.uploader'])->find($id);

        if (! $specimen) {
            return $this->respondNotFound('Specimen not found.');
        }

        return $this->respondSuccess(
            SpecimenImageResource::collection($specimen->images),
            'Specimen images retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/specimens/{id}/images",
     *     summary="Upload Specimen Image",
     *     description="Upload and associate an image file with a specimen.",
     *     tags={"Specimens"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Specimen ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image"},
     *                 @OA\Property(property="image", type="string", format="binary", description="Image file (jpg, png, webp)"),
     *                 @OA\Property(property="caption", type="string", example="Dorsal view of specimen"),
     *                 @OA\Property(property="type", type="string", enum={"dorsal","ventral","lateral","habitat","microscopic","other"}, example="dorsal")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Image uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Image uploaded successfully.")
     *         )
     *     )
     * )
     */
    public function uploadImage(UploadImageRequest $request, string $id): JsonResponse
    {
        $specimen = Specimen::find($id);

        if (! $specimen) {
            return $this->respondNotFound('Specimen not found.');
        }

        $image = $this->mediaService->uploadImage(
            $specimen,
            $request->file('image'),
            $request->validated(),
            $request->user()
        );

        return $this->respondCreated(
            new SpecimenImageResource($image->load('uploader')),
            'Image uploaded successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/specimens/{id}/documents",
     *     summary="List Specimen Documents",
     *     description="Retrieve all attached documents and field notes for a specimen.",
     *     tags={"Specimens"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Specimen ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="List of specimen documents",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Specimen documents retrieved successfully.")
     *         )
     *     )
     * )
     */
    public function documents(string $id): JsonResponse
    {
        $specimen = Specimen::with(['documents.uploader'])->find($id);

        if (! $specimen) {
            return $this->respondNotFound('Specimen not found.');
        }

        return $this->respondSuccess(
            SpecimenDocumentResource::collection($specimen->documents),
            'Specimen documents retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/specimens/{id}/documents",
     *     summary="Upload Specimen Document",
     *     description="Upload and associate a document (PDF, notes, sequencing) with a specimen.",
     *     tags={"Specimens"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Specimen ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"document"},
     *                 @OA\Property(property="document", type="string", format="binary", description="Document file (pdf, docx, txt)"),
     *                 @OA\Property(property="document_type", type="string", enum={"field_notes","dna_sequence","permits","publication","report","other"}, example="field_notes"),
     *                 @OA\Property(property="description", type="string", example="Field collection log and GPS logs")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Document uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Document uploaded successfully.")
     *         )
     *     )
     * )
     */
    public function uploadDocument(UploadDocumentRequest $request, string $id): JsonResponse
    {
        $specimen = Specimen::find($id);

        if (! $specimen) {
            return $this->respondNotFound('Specimen not found.');
        }

        $document = $this->mediaService->uploadDocument(
            $specimen,
            $request->file('document'),
            $request->validated(),
            $request->user()
        );

        return $this->respondCreated(
            new SpecimenDocumentResource($document->load('uploader')),
            'Document uploaded successfully.'
        );
    }
}
