<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Specimen Repository REST API",
 *     description="Production-grade REST API backend for managing biological specimens, taxonomy, collections, researchers, locations, media files, specimen loans, and administrative audit logging. Built with Laravel 13 and MongoDB.",
 *     @OA\Contact(
 *         name="API Support",
 *         email="admin@specimenrepo.local"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/",
 *     description="Current Host Environment"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your Bearer token obtained from POST /api/v1/auth/login"
 * )
 *
 * @OA\Tag(name="Authentication", description="Admin login, logout, and profile operations")
 * @OA\Tag(name="Specimens", description="Biological specimen management, filtering, searching, images, and documents")
 * @OA\Tag(name="Taxonomy", description="Taxonomic ranks and biological classification")
 * @OA\Tag(name="Collections", description="Specimen collection groupings")
 * @OA\Tag(name="Researchers", description="Researchers, scientists, and borrowers")
 * @OA\Tag(name="Projects", description="Research projects and specimen affiliations")
 * @OA\Tag(name="Locations", description="Geographic collection sites and repository storage locations")
 * @OA\Tag(name="Loans", description="Specimen loan transactions and lifecycle approval/return workflow")
 * @OA\Tag(name="Reports", description="Analytics and statistical reports powered by MongoDB aggregation pipelines")
 * @OA\Tag(name="Audit Logs", description="Audit logs tracking administrative state-changing operations")
 */
class OpenApiInfo
{
}
