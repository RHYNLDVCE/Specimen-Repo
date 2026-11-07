<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Specimen",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="67d280e5b7a5a8f4c201a011"),
 *     @OA\Property(property="specimen_code", type="string", example="SPEC-2026-001"),
 *     @OA\Property(property="catalog_number", type="string", example="CAT-00123"),
 *     @OA\Property(property="scientific_name", type="string", example="Pithecophaga jefferyi"),
 *     @OA\Property(property="common_name", type="string", example="Philippine Eagle"),
 *     @OA\Property(property="kingdom", type="string", example="Animalia"),
 *     @OA\Property(property="phylum", type="string", example="Chordata"),
 *     @OA\Property(property="class", type="string", example="Aves"),
 *     @OA\Property(property="order", type="string", example="Accipitriformes"),
 *     @OA\Property(property="family", type="string", example="Accipitridae"),
 *     @OA\Property(property="genus", type="string", example="Pithecophaga"),
 *     @OA\Property(property="species", type="string", example="jefferyi"),
 *     @OA\Property(property="taxon_id", type="string", example="67d280e5b7a5a8f4c201a020"),
 *     @OA\Property(property="collection_id", type="string", example="67d280e5b7a5a8f4c201a030"),
 *     @OA\Property(property="project_id", type="string", example="67d280e5b7a5a8f4c201a035"),
 *     @OA\Property(property="collector", type="string", example="Dr. Maria Santos"),
 *     @OA\Property(property="collection_date", type="string", format="date", example="2026-03-15"),
 *     @OA\Property(property="location_id", type="string", example="67d280e5b7a5a8f4c201a040"),
 *     @OA\Property(property="latitude", type="number", format="float", example=7.0094),
 *     @OA\Property(property="longitude", type="number", format="float", example=125.2789),
 *     @OA\Property(property="habitat", type="string", example="Primary lowland tropical rainforest"),
 *     @OA\Property(property="description", type="string", example="Well-preserved skeletal specimen"),
 *     @OA\Property(property="preservation_method", type="string", example="skeletal"),
 *     @OA\Property(property="repository_location", type="string", example="Cabinet B, Shelf 4"),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-15T08:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-03-15T08:30:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Taxon",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="67d280e5b7a5a8f4c201a020"),
 *     @OA\Property(property="kingdom", type="string", example="Animalia"),
 *     @OA\Property(property="phylum", type="string", example="Chordata"),
 *     @OA\Property(property="class", type="string", example="Aves"),
 *     @OA\Property(property="order", type="string", example="Accipitriformes"),
 *     @OA\Property(property="family", type="string", example="Accipitridae"),
 *     @OA\Property(property="genus", type="string", example="Pithecophaga"),
 *     @OA\Property(property="species", type="string", example="jefferyi"),
 *     @OA\Property(property="scientific_name", type="string", example="Pithecophaga jefferyi"),
 *     @OA\Property(property="common_name", type="string", example="Philippine Eagle"),
 *     @OA\Property(property="authority", type="string", example="Ogilvie-Grant, 1896"),
 *     @OA\Property(property="description", type="string", example="Apex avian predator endemic to the Philippines")
 * )
 *
 * @OA\Schema(
 *     schema="Collection",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="67d280e5b7a5a8f4c201a030"),
 *     @OA\Property(property="name", type="string", example="Philippine Avian Biodiversity Survey"),
 *     @OA\Property(property="description", type="string", example="Field collection of endemic avian species"),
 *     @OA\Property(property="collection_type", type="string", example="Ornithology"),
 *     @OA\Property(property="collector", type="string", example="National Museum Research Team"),
 *     @OA\Property(property="date_started", type="string", format="date", example="2025-01-10"),
 *     @OA\Property(property="status", type="string", example="active")
 * )
 *
 * @OA\Schema(
 *     schema="Researcher",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="67d280e5b7a5a8f4c201a050"),
 *     @OA\Property(property="name", type="string", example="Dr. Elena Rostova"),
 *     @OA\Property(property="email", type="string", example="e.rostova@university.edu"),
 *     @OA\Property(property="institution", type="string", example="State University Department of Biology"),
 *     @OA\Property(property="department", type="string", example="Zoology Division"),
 *     @OA\Property(property="specialization", type="string", example="Raptor Osteology"),
 *     @OA\Property(property="status", type="string", example="active")
 * )
 *
 * @OA\Schema(
 *     schema="Project",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="67d280e5b7a5a8f4c201a060"),
 *     @OA\Property(property="project_code", type="string", example="PRJ-RAPTOR-2026"),
 *     @OA\Property(property="title", type="string", example="Southeast Asian Raptor Evolutionary Morphology"),
 *     @OA\Property(property="description", type="string", example="Comparative morphology across island archipelagos"),
 *     @OA\Property(property="principal_researcher_id", type="string", example="67d280e5b7a5a8f4c201a050"),
 *     @OA\Property(property="start_date", type="string", format="date", example="2026-01-01"),
 *     @OA\Property(property="status", type="string", example="active")
 * )
 *
 * @OA\Schema(
 *     schema="Location",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="67d280e5b7a5a8f4c201a040"),
 *     @OA\Property(property="name", type="string", example="Mount Apo Natural Park"),
 *     @OA\Property(property="province", type="string", example="Davao del Sur"),
 *     @OA\Property(property="municipality", type="string", example="Bansalan"),
 *     @OA\Property(property="barangay", type="string", example="Managa"),
 *     @OA\Property(property="latitude", type="number", format="float", example=6.9875),
 *     @OA\Property(property="longitude", type="number", format="float", example=125.2711),
 *     @OA\Property(property="habitat", type="string", example="Montane mossy forest")
 * )
 *
 * @OA\Schema(
 *     schema="Loan",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="67d280e5b7a5a8f4c201a070"),
 *     @OA\Property(property="specimen_id", type="string", example="67d280e5b7a5a8f4c201a011"),
 *     @OA\Property(property="researcher_id", type="string", example="67d280e5b7a5a8f4c201a050"),
 *     @OA\Property(property="borrower_name", type="string", example="Dr. Elena Rostova"),
 *     @OA\Property(property="purpose", type="string", example="Cranial CT-scan morphometric study"),
 *     @OA\Property(property="loan_date", type="string", format="date", example="2026-04-01"),
 *     @OA\Property(property="expected_return_date", type="string", format="date", example="2026-06-01"),
 *     @OA\Property(property="actual_return_date", type="string", format="date", example=null),
 *     @OA\Property(property="status", type="string", example="approved")
 * )
 */
class Schemas
{
}
