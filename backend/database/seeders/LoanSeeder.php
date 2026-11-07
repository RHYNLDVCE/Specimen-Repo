<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Loan;
use App\Models\Researcher;
use App\Models\Specimen;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::where('role', 'super_admin')->first();
        $researchers = Researcher::all();
        $specimens = Specimen::all();

        if ($researchers->isEmpty() || $specimens->isEmpty()) {
            return;
        }

        $resList = $researchers->values()->all();
        $spList = $specimens->values()->all();

        // 1. Foundation Loans (for tests)
        $eagle = Specimen::where('specimen_code', 'SPEC-2026-ORN-001')->first();
        $tamaraw = Specimen::where('specimen_code', 'SPEC-2024-MAM-005')->first();
        $croc = Specimen::where('specimen_code', 'SPEC-2025-REP-003')->first();
        $tarsier = Specimen::where('specimen_code', 'SPEC-2025-MAM-010')->first();
        $elena = Researcher::where('email', 'e.rostova@university.edu')->first();
        $marcus = Researcher::where('email', 'm.vance@smithsonian.org')->first();
        $sofia = Researcher::where('email', 's.lim@ateneo.edu')->first();

        if ($eagle && $elena) {
            Loan::updateOrCreate(
                ['specimen_id' => $eagle->_id, 'borrower_name' => $elena->name],
                [
                    'researcher_id' => $elena->_id,
                    'purpose' => 'Non-destructive micro-CT volumetric scan of skull and sternum.',
                    'loan_date' => '2026-03-01',
                    'expected_return_date' => '2026-06-01',
                    'actual_return_date' => null,
                    'status' => 'approved',
                    'notes' => 'Insured courier transport arranged. Authorized for laboratory radiography only.',
                    'approved_by' => $admin?->_id,
                ]
            );
            $eagle->update(['status' => 'loaned']);
        }

        if ($tamaraw && $marcus) {
            Loan::updateOrCreate(
                ['specimen_id' => $tamaraw->_id, 'borrower_name' => $marcus->name],
                [
                    'researcher_id' => $marcus->_id,
                    'purpose' => 'Cranial suture comparative analysis with fossil dwarf bubalines.',
                    'loan_date' => '2025-08-01',
                    'expected_return_date' => '2025-12-01', // overdue
                    'actual_return_date' => null,
                    'status' => 'approved',
                    'notes' => 'Second reminder notice dispatched to borrowing institution curator.',
                    'approved_by' => $admin?->_id,
                ]
            );
            $tamaraw->update(['status' => 'loaned']);
        }

        if ($croc && $sofia) {
            Loan::updateOrCreate(
                ['specimen_id' => $croc->_id, 'borrower_name' => $sofia->name],
                [
                    'researcher_id' => $sofia->_id,
                    'purpose' => 'Molecular taxonomy, scalation sequencing, and tissue biopsy.',
                    'loan_date' => '2024-05-10',
                    'expected_return_date' => '2024-09-10',
                    'actual_return_date' => '2024-09-02',
                    'status' => 'returned',
                    'notes' => 'Specimen returned intact with accompanying DNA voucher isolate aliquots.',
                    'approved_by' => $admin?->_id,
                ]
            );
        }

        if ($tarsier && $sofia) {
            Loan::updateOrCreate(
                ['specimen_id' => $tarsier->_id, 'borrower_name' => $sofia->name],
                [
                    'researcher_id' => $sofia->_id,
                    'purpose' => 'Comparative non-invasive high-resolution imaging of auditory bullae.',
                    'loan_date' => '2026-05-01',
                    'expected_return_date' => '2026-07-01',
                    'actual_return_date' => null,
                    'status' => 'pending',
                    'notes' => 'Pending departmental committee ethics review.',
                    'approved_by' => null,
                ]
            );
        }

        // 2. Programmatic Additional 30+ Loans across Researchers and Specimens
        $loanPurposes = [
            'High-throughput ancient DNA extraction and mitochondrial control region sequencing.',
            '3D photogrammetric surface modeling for digital open-access morphological repository.',
            'Stable isotope ratio analysis of tooth enamel for paleoclimatic reconstruction.',
            'Comparative skeletal morphometrics for taxonomic revision and synonymy clarification.',
            'X-ray fluorescence spectrometry for heavy metal elemental bioaccumulation profiling.',
            'Histological preparation of dermal scutes and dermal ossicles.',
            'Pollen wash and scanning electron microscopy of floral reproductive structures.',
            'Examination of holotype genitalia structures for description of newly discovered sibling species.',
            'Non-destructive synchrotron radiation micro-tomography of cranial endocasts.',
            'Parasitological screening of gastrointestinal tract and fur ectoparasite collection.',
        ];

        // Pick distinct specimens that are not the 4 foundation specimens
        $candidateSpecimens = $specimens->filter(function ($s) {
            return !in_array($s->specimen_code, [
                'SPEC-2026-ORN-001', 'SPEC-2024-MAM-005', 'SPEC-2025-REP-003', 'SPEC-2025-MAM-010',
            ]);
        })->values();

        $loanConfigs = [
            // Overdue loans (past expected_return_date)
            ['status' => 'approved', 'is_overdue' => true, 'loan_year' => 2025, 'loan_month' => 4, 'duration' => 4],
            ['status' => 'approved', 'is_overdue' => true, 'loan_year' => 2025, 'loan_month' => 7, 'duration' => 3],
            ['status' => 'approved', 'is_overdue' => true, 'loan_year' => 2025, 'loan_month' => 9, 'duration' => 3],
            ['status' => 'approved', 'is_overdue' => true, 'loan_year' => 2026, 'loan_month' => 1, 'duration' => 2],
            // Active approved loans (future expected_return_date)
            ['status' => 'approved', 'is_overdue' => false, 'loan_year' => 2026, 'loan_month' => 6, 'duration' => 6],
            ['status' => 'approved', 'is_overdue' => false, 'loan_year' => 2026, 'loan_month' => 7, 'duration' => 5],
            ['status' => 'approved', 'is_overdue' => false, 'loan_year' => 2026, 'loan_month' => 8, 'duration' => 6],
            ['status' => 'approved', 'is_overdue' => false, 'loan_year' => 2026, 'loan_month' => 8, 'duration' => 4],
            ['status' => 'approved', 'is_overdue' => false, 'loan_year' => 2026, 'loan_month' => 9, 'duration' => 4],
            // Pending loans
            ['status' => 'pending', 'is_overdue' => false, 'loan_year' => 2026, 'loan_month' => 9, 'duration' => 3],
            ['status' => 'pending', 'is_overdue' => false, 'loan_year' => 2026, 'loan_month' => 10, 'duration' => 4],
            ['status' => 'pending', 'is_overdue' => false, 'loan_year' => 2026, 'loan_month' => 10, 'duration' => 6],
            ['status' => 'pending', 'is_overdue' => false, 'loan_year' => 2026, 'loan_month' => 11, 'duration' => 3],
            // Returned loans (historical)
            ['status' => 'returned', 'is_overdue' => false, 'loan_year' => 2023, 'loan_month' => 2, 'duration' => 4],
            ['status' => 'returned', 'is_overdue' => false, 'loan_year' => 2023, 'loan_month' => 6, 'duration' => 5],
            ['status' => 'returned', 'is_overdue' => false, 'loan_year' => 2024, 'loan_month' => 1, 'duration' => 3],
            ['status' => 'returned', 'is_overdue' => false, 'loan_year' => 2024, 'loan_month' => 5, 'duration' => 6],
            ['status' => 'returned', 'is_overdue' => false, 'loan_year' => 2024, 'loan_month' => 9, 'duration' => 4],
            ['status' => 'returned', 'is_overdue' => false, 'loan_year' => 2025, 'loan_month' => 2, 'duration' => 3],
            ['status' => 'returned', 'is_overdue' => false, 'loan_year' => 2025, 'loan_month' => 5, 'duration' => 5],
            ['status' => 'returned', 'is_overdue' => false, 'loan_year' => 2025, 'loan_month' => 8, 'duration' => 4],
            // Cancelled loans
            ['status' => 'cancelled', 'is_overdue' => false, 'loan_year' => 2024, 'loan_month' => 4, 'duration' => 3],
            ['status' => 'cancelled', 'is_overdue' => false, 'loan_year' => 2025, 'loan_month' => 11, 'duration' => 2],
        ];

        foreach ($loanConfigs as $i => $cfg) {
            if (!isset($candidateSpecimens[$i])) {
                break;
            }

            $sp = $candidateSpecimens[$i];
            $res = $resList[$i % count($resList)];
            $purpose = $loanPurposes[$i % count($loanPurposes)];

            $loanMonth = str_pad($cfg['loan_month'], 2, '0', STR_PAD_LEFT);
            $loanDate = "{$cfg['loan_year']}-{$loanMonth}-15";

            $returnMonthNum = $cfg['loan_month'] + $cfg['duration'];
            $returnYear = $cfg['loan_year'];
            if ($returnMonthNum > 12) {
                $returnYear++;
                $returnMonthNum -= 12;
            }
            $returnMonth = str_pad($returnMonthNum, 2, '0', STR_PAD_LEFT);
            $expectedReturn = "{$returnYear}-{$returnMonth}-15";

            $actualReturn = null;
            if ($cfg['status'] === 'returned') {
                $actualReturn = "{$returnYear}-{$returnMonth}-10";
            }

            $approvedBy = in_array($cfg['status'], ['approved', 'returned']) ? $admin?->_id : null;

            Loan::updateOrCreate(
                [
                    'specimen_id' => $sp->_id,
                    'researcher_id' => $res->_id,
                ],
                [
                    'borrower_name' => $res->name,
                    'purpose' => $purpose,
                    'loan_date' => $loanDate,
                    'expected_return_date' => $expectedReturn,
                    'actual_return_date' => $actualReturn,
                    'status' => $cfg['status'],
                    'notes' => "Institutional loan contract #LN-" . sprintf('%04d', $i + 101) . " under reciprocal research agreement.",
                    'approved_by' => $approvedBy,
                ]
            );

            // Update specimen status
            if ($cfg['status'] === 'approved') {
                $sp->update(['status' => 'loaned']);
            } elseif ($sp->status === 'loaned') {
                $sp->update(['status' => 'active']);
            }
        }
    }
}
