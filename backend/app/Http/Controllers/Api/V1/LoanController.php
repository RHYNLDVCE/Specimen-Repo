<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\StoreLoanRequest;
use App\Http\Resources\LoanResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Loan;
use App\Services\LoanService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class LoanController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected LoanService $loanService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/loans",
     *     summary="List Specimen Loans",
     *     description="Retrieve paginated list of specimen loan transactions.",
     *     tags={"Loans"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="status", in="query", description="Filter by loan status (pending, approved, active, returned, overdue, cancelled)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="specimen_id", in="query", description="Filter by specimen ID", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="researcher_id", in="query", description="Filter by researcher ID", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer", default=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of loans",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Loan"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Loan::query()->with(['specimen', 'researcher', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('specimen_id')) {
            $query->where('specimen_id', $request->input('specimen_id'));
        }

        if ($request->filled('researcher_id')) {
            $query->where('researcher_id', $request->input('researcher_id'));
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->respondPaginated(
            $paginated,
            LoanResource::collection($paginated->items()),
            'Loans retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/loans",
     *     summary="Create Specimen Loan",
     *     description="Create a new specimen loan transaction. Specimen must be in active status.",
     *     tags={"Loans"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"specimen_id","researcher_id","borrower_name","purpose","loan_date","expected_return_date"},
     *             @OA\Property(property="specimen_id", type="string", example="67d280e5b7a5a8f4c201a011"),
     *             @OA\Property(property="researcher_id", type="string", example="67d280e5b7a5a8f4c201a050"),
     *             @OA\Property(property="borrower_name", type="string", example="Dr. Elena Rostova"),
     *             @OA\Property(property="purpose", type="string", example="Cranial CT-scan morphometric study"),
     *             @OA\Property(property="loan_date", type="string", format="date", example="2026-04-01"),
     *             @OA\Property(property="expected_return_date", type="string", format="date", example="2026-06-01"),
     *             @OA\Property(property="notes", type="string", example="Specimen requires climate-controlled shipping")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Loan created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Loan request created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Loan")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Specimen already loaned or unavailable"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(StoreLoanRequest $request): JsonResponse
    {
        try {
            $loan = $this->loanService->createLoan($request->validated(), $request->user());

            return $this->respondCreated(
                new LoanResource($loan),
                'Loan request created successfully.'
            );
        } catch (DomainException $e) {
            return $this->respondError($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/loans/{id}",
     *     summary="Get Loan Details",
     *     description="Retrieve details of a single loan transaction by ID.",
     *     tags={"Loans"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Loan ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Loan details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Loan")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Loan not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $loan = Loan::with(['specimen', 'researcher', 'approver'])->find($id);

        if (! $loan) {
            return $this->respondNotFound('Loan record not found.');
        }

        return $this->respondSuccess(
            new LoanResource($loan),
            'Loan details retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/loans/{id}/approve",
     *     summary="Approve Specimen Loan",
     *     description="Approve a pending loan transaction. Automatically changes the specimen status to loaned.",
     *     tags={"Loans"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Loan ID", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Loan approved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Loan approved successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Loan")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid status transition or specimen unavailable"),
     *     @OA\Response(response=404, description="Loan not found")
     * )
     */
    public function approve(Request $request, string $id): JsonResponse
    {
        $loan = Loan::with('specimen')->find($id);

        if (! $loan) {
            return $this->respondNotFound('Loan record not found.');
        }

        try {
            $approved = $this->loanService->approveLoan($loan, $request->user());

            return $this->respondSuccess(
                new LoanResource($approved),
                'Loan approved successfully.'
            );
        } catch (DomainException $e) {
            return $this->respondError($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/loans/{id}/return",
     *     summary="Return Specimen Loan",
     *     description="Record the return of a loaned specimen. Automatically changes the specimen status back to active.",
     *     tags={"Loans"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Loan ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="actual_return_date", type="string", format="date", example="2026-05-20"),
     *             @OA\Property(property="notes", type="string", example="Returned in pristine condition without damages")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Specimen returned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Specimen returned and status restored to active."),
     *             @OA\Property(property="data", ref="#/components/schemas/Loan")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid status transition"),
     *     @OA\Response(response=404, description="Loan not found")
     * )
     */
    public function returnLoan(Request $request, string $id): JsonResponse
    {
        $loan = Loan::with('specimen')->find($id);

        if (! $loan) {
            return $this->respondNotFound('Loan record not found.');
        }

        try {
            $returned = $this->loanService->returnLoan(
                $loan,
                $request->user(),
                $request->input('actual_return_date'),
                $request->input('notes')
            );

            return $this->respondSuccess(
                new LoanResource($returned),
                'Specimen returned and status restored to active.'
            );
        } catch (DomainException $e) {
            return $this->respondError($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/loans/{id}/cancel",
     *     summary="Cancel Specimen Loan",
     *     description="Cancel a loan transaction. Reverts specimen status to active if previously approved.",
     *     tags={"Loans"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Loan ID", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="reason", type="string", example="Borrower cancelled request due to grant timing")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Loan cancelled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Loan cancelled successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Loan")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid status transition"),
     *     @OA\Response(response=404, description="Loan not found")
     * )
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        $loan = Loan::with('specimen')->find($id);

        if (! $loan) {
            return $this->respondNotFound('Loan record not found.');
        }

        try {
            $cancelled = $this->loanService->cancelLoan(
                $loan,
                $request->user(),
                $request->input('reason')
            );

            return $this->respondSuccess(
                new LoanResource($cancelled),
                'Loan cancelled successfully.'
            );
        } catch (DomainException $e) {
            return $this->respondError($e->getMessage(), 400);
        }
    }
}
