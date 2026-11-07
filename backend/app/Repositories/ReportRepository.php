<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\Collection;
use App\Models\Loan;
use App\Models\Project;
use App\Models\Researcher;
use App\Models\Specimen;
use App\Models\Taxon;
use MongoDB\BSON\UTCDateTime;

class ReportRepository
{
    /**
     * Generate dashboard summary metrics using MongoDB aggregation.
     *
     * @return array<string, mixed>
     */
    public function getDashboardMetrics(): array
    {
        // Aggregation for specimen status breakdown
        $specimenStatusAgg = Specimen::raw(function ($collection) {
            return $collection->aggregate([
                ['$group' => ['_id' => '$status', 'count' => ['$sum' => 1]]],
            ]);
        });

        $specimenStatusCounts = [];
        $totalSpecimens = 0;
        foreach ($specimenStatusAgg as $doc) {
            $status = $doc['_id'] ?? 'unknown';
            $count = (int) ($doc['count'] ?? 0);
            $specimenStatusCounts[$status] = $count;
            $totalSpecimens += $count;
        }

        // Aggregation for loan status breakdown
        $loanStatusAgg = Loan::raw(function ($collection) {
            return $collection->aggregate([
                ['$group' => ['_id' => '$status', 'count' => ['$sum' => 1]]],
            ]);
        });

        $loanStatusCounts = [];
        $totalLoans = 0;
        foreach ($loanStatusAgg as $doc) {
            $status = $doc['_id'] ?? 'unknown';
            $count = (int) ($doc['count'] ?? 0);
            $loanStatusCounts[$status] = $count;
            $totalLoans += $count;
        }

        // Overdue loans calculation
        $overdueLoans = Loan::whereIn('status', ['approved', 'active'])
            ->where('expected_return_date', '<', now()->startOfDay())
            ->count();

        return [
            'specimens' => [
                'total' => $totalSpecimens,
                'by_status' => $specimenStatusCounts,
            ],
            'loans' => [
                'total' => $totalLoans,
                'active' => ($loanStatusCounts['active'] ?? 0) + ($loanStatusCounts['approved'] ?? 0),
                'pending' => $loanStatusCounts['pending'] ?? 0,
                'returned' => $loanStatusCounts['returned'] ?? 0,
                'overdue' => $overdueLoans,
                'by_status' => $loanStatusCounts,
            ],
            'totals' => [
                'taxa' => Taxon::count(),
                'collections' => Collection::count(),
                'researchers' => Researcher::count(),
                'projects' => Project::count(),
                'admins' => Admin::count(),
            ],
        ];
    }

    /**
     * Generate detailed specimen analytics using MongoDB aggregation pipelines.
     *
     * @return array<string, mixed>
     */
    public function getSpecimensReport(): array
    {
        $statusAgg = Specimen::raw(function ($col) {
            return $col->aggregate([
                ['$group' => ['_id' => '$status', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
            ]);
        });

        $kingdomAgg = Specimen::raw(function ($col) {
            return $col->aggregate([
                ['$match' => ['kingdom' => ['$ne' => null]]],
                ['$group' => ['_id' => '$kingdom', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
            ]);
        });

        $preservationAgg = Specimen::raw(function ($col) {
            return $col->aggregate([
                ['$match' => ['preservation_method' => ['$ne' => null]]],
                ['$group' => ['_id' => '$preservation_method', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
            ]);
        });

        $monthlyAgg = Specimen::raw(function ($col) {
            return $col->aggregate([
                ['$project' => [
                    'year_month' => [
                        '$dateToString' => ['format' => '%Y-%m', 'date' => '$created_at'],
                    ],
                ]],
                ['$group' => ['_id' => '$year_month', 'count' => ['$sum' => 1]]],
                ['$sort' => ['_id' => -1]],
                ['$limit' => 12],
            ]);
        });

        return [
            'by_status' => iterator_to_array($statusAgg),
            'by_kingdom' => iterator_to_array($kingdomAgg),
            'by_preservation_method' => iterator_to_array($preservationAgg),
            'monthly_acquisitions' => iterator_to_array($monthlyAgg),
        ];
    }

    /**
     * Generate taxonomy aggregation report.
     *
     * @return array<string, mixed>
     */
    public function getTaxonomyReport(): array
    {
        $familyAgg = Specimen::raw(function ($col) {
            return $col->aggregate([
                ['$match' => ['family' => ['$ne' => null]]],
                ['$group' => ['_id' => '$family', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
                ['$limit' => 20],
            ]);
        });

        $orderAgg = Specimen::raw(function ($col) {
            return $col->aggregate([
                ['$match' => ['order' => ['$ne' => null]]],
                ['$group' => ['_id' => '$order', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
                ['$limit' => 20],
            ]);
        });

        $classAgg = Specimen::raw(function ($col) {
            return $col->aggregate([
                ['$match' => ['class' => ['$ne' => null]]],
                ['$group' => ['_id' => '$class', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
            ]);
        });

        return [
            'top_families' => iterator_to_array($familyAgg),
            'top_orders' => iterator_to_array($orderAgg),
            'by_class' => iterator_to_array($classAgg),
            'total_taxa' => Taxon::count(),
        ];
    }

    /**
     * Generate collections aggregation report.
     *
     * @return array<string, mixed>
     */
    public function getCollectionsReport(): array
    {
        $collections = Collection::withCount('specimens')->get();

        $collectionStatusAgg = Collection::raw(function ($col) {
            return $col->aggregate([
                ['$group' => ['_id' => '$status', 'count' => ['$sum' => 1]]],
            ]);
        });

        $collectionTypeAgg = Collection::raw(function ($col) {
            return $col->aggregate([
                ['$group' => ['_id' => '$collection_type', 'count' => ['$sum' => 1]]],
            ]);
        });

        return [
            'collections' => $collections->map(fn ($c) => [
                'id' => (string) $c->_id,
                'name' => $c->name,
                'collection_type' => $c->collection_type,
                'status' => $c->status,
                'specimens_count' => $c->specimens_count ?? 0,
            ]),
            'by_status' => iterator_to_array($collectionStatusAgg),
            'by_type' => iterator_to_array($collectionTypeAgg),
            'total_collections' => $collections->count(),
        ];
    }

    /**
     * Generate loans report with overdue breakdown and top borrowers.
     *
     * @return array<string, mixed>
     */
    public function getLoansReport(): array
    {
        $statusAgg = Loan::raw(function ($col) {
            return $col->aggregate([
                ['$group' => ['_id' => '$status', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
            ]);
        });

        $topBorrowersAgg = Loan::raw(function ($col) {
            return $col->aggregate([
                ['$group' => ['_id' => '$borrower_name', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
                ['$limit' => 10],
            ]);
        });

        $overdueLoans = Loan::with(['specimen', 'researcher'])
            ->whereIn('status', ['approved', 'active'])
            ->where('expected_return_date', '<', now()->startOfDay())
            ->get();

        return [
            'by_status' => iterator_to_array($statusAgg),
            'top_borrowers' => iterator_to_array($topBorrowersAgg),
            'overdue_count' => $overdueLoans->count(),
            'overdue_loans' => $overdueLoans->map(fn ($loan) => [
                'id' => (string) $loan->_id,
                'borrower_name' => $loan->borrower_name,
                'specimen_code' => $loan->specimen?->specimen_code,
                'loan_date' => $loan->loan_date?->format('Y-m-d'),
                'expected_return_date' => $loan->expected_return_date?->format('Y-m-d'),
                'days_overdue' => $loan->expected_return_date ? (int) abs(now()->diffInDays($loan->expected_return_date)) : 0,
            ]),
        ];
    }
}
