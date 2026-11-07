<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\Specimen;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::where('role', 'super_admin')->first();
        $curator = Admin::where('role', 'admin')->first();

        if (!$admin) {
            return;
        }

        $specimens = Specimen::take(20)->get();
        $loans = Loan::take(15)->get();

        $actions = [
            [
                'action' => 'LOGIN',
                'resource_type' => 'Admin',
                'resource_id' => $admin->_id,
                'description' => "Admin {$admin->email} logged in successfully.",
                'days_ago' => 45,
            ],
            [
                'action' => 'LOGIN',
                'resource_type' => 'Admin',
                'resource_id' => $curator?->_id ?? $admin->_id,
                'description' => "Admin " . ($curator ? $curator->email : $admin->email) . " logged in successfully.",
                'days_ago' => 30,
            ],
        ];

        foreach ($specimens as $idx => $sp) {
            $actions[] = [
                'action' => 'CREATE_SPECIMEN',
                'resource_type' => 'Specimen',
                'resource_id' => $sp->_id,
                'description' => "Created specimen {$sp->specimen_code} ({$sp->scientific_name}).",
                'days_ago' => 60 - ($idx * 2),
            ];

            if ($idx % 3 === 0) {
                $actions[] = [
                    'action' => 'UPDATE_SPECIMEN',
                    'resource_type' => 'Specimen',
                    'resource_id' => $sp->_id,
                    'description' => "Updated storage location and metadata for specimen {$sp->specimen_code}.",
                    'days_ago' => 20 - ($idx % 10),
                ];
            }
        }

        foreach ($loans as $lidx => $ln) {
            $actions[] = [
                'action' => 'CREATE_LOAN',
                'resource_type' => 'Loan',
                'resource_id' => $ln->_id,
                'description' => "Submitted loan request for borrower {$ln->borrower_name}.",
                'days_ago' => 35 - $lidx,
            ];

            if ($ln->status === 'approved' || $ln->status === 'returned') {
                $actions[] = [
                    'action' => 'APPROVE_LOAN',
                    'resource_type' => 'Loan',
                    'resource_id' => $ln->_id,
                    'description' => "Approved loan for borrower {$ln->borrower_name}.",
                    'days_ago' => 30 - $lidx,
                ];
            }

            if ($ln->status === 'returned') {
                $actions[] = [
                    'action' => 'RETURN_LOAN',
                    'resource_type' => 'Loan',
                    'resource_id' => $ln->_id,
                    'description' => "Recorded return of specimen from loan #{$ln->_id}.",
                    'days_ago' => 10 - ($lidx % 5),
                ];
            }
        }

        foreach ($actions as $act) {
            $logTime = Carbon::now()->subDays(max(0, $act['days_ago']))->subMinutes(mt_rand(10, 500));
            AuditLog::firstOrCreate(
                [
                    'action' => $act['action'],
                    'resource_id' => $act['resource_id'],
                    'description' => $act['description'],
                ],
                [
                    'admin_id' => $admin->_id,
                    'admin_email' => $admin->email,
                    'resource_type' => $act['resource_type'],
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/130.0.0.0 Safari/537.36',
                    'created_at' => $logTime,
                ]
            );
        }
    }
}
