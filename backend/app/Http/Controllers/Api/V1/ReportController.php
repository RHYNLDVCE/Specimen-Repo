<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Repositories\ReportRepository;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ReportController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ReportRepository $reportRepository
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/reports/dashboard",
     *     summary="Dashboard Summary Metrics",
     *     description="High-level counts and status distributions powered by MongoDB aggregation pipelines.",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard metrics",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dashboard metrics retrieved successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="specimens", type="object",
     *                     @OA\Property(property="total", type="integer", example=150),
     *                     @OA\Property(property="by_status", type="object")
     *                 ),
     *                 @OA\Property(property="loans", type="object",
     *                     @OA\Property(property="total", type="integer", example=25),
     *                     @OA\Property(property="active", type="integer", example=8),
     *                     @OA\Property(property="overdue", type="integer", example=2)
     *                 ),
     *                 @OA\Property(property="totals", type="object",
     *                     @OA\Property(property="taxa", type="integer", example=45),
     *                     @OA\Property(property="collections", type="integer", example=12),
     *                     @OA\Property(property="researchers", type="integer", example=30)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function dashboard(): JsonResponse
    {
        $metrics = $this->reportRepository->getDashboardMetrics();

        return $this->respondSuccess($metrics, 'Dashboard metrics retrieved successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/reports/specimens",
     *     summary="Specimens Analytics Report",
     *     description="Detailed breakdown of specimens by status, kingdom, preservation method, and monthly acquisition trends.",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Specimens report",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function specimens(): JsonResponse
    {
        $report = $this->reportRepository->getSpecimensReport();

        return $this->respondSuccess($report, 'Specimens report retrieved successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/reports/taxonomy",
     *     summary="Taxonomy Analytics Report",
     *     description="Taxonomic aggregation of specimens grouped by class, order, and top families.",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Taxonomy report",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function taxonomy(): JsonResponse
    {
        $report = $this->reportRepository->getTaxonomyReport();

        return $this->respondSuccess($report, 'Taxonomy report retrieved successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/reports/collections",
     *     summary="Collections Analytics Report",
     *     description="Collection statistics including specimen counts per collection and status groupings.",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Collections report",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function collections(): JsonResponse
    {
        $report = $this->reportRepository->getCollectionsReport();

        return $this->respondSuccess($report, 'Collections report retrieved successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/reports/loans",
     *     summary="Loans & Circulation Report",
     *     description="Loan analytics including status breakdown, overdue loans list, and top borrowing researchers.",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Loans report",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function loans(): JsonResponse
    {
        $report = $this->reportRepository->getLoansReport();

        return $this->respondSuccess($report, 'Loans report retrieved successfully.');
    }
}
