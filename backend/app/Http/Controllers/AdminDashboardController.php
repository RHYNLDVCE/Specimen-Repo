<?php

namespace App\Http\Controllers;

use App\Repositories\ReportRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected ReportRepository $reportRepository
    ) {}

    /**
     * Display the admin dashboard.
     */
    public function index(Request $request, ?string $tab = 'overview'): Response
    {
        $metrics = $this->reportRepository->getDashboardMetrics();

        return Inertia::render('admin/dashboard', [
            'initialTab' => $tab ?? 'overview',
            'initialMetrics' => $metrics,
            'defaultCredentials' => [
                'email' => env('ADMIN_SEED_EMAIL', 'rhayan@gmail.com'),
                'password' => env('ADMIN_SEED_PASSWORD', 'rhynlodo'),
                'name' => env('ADMIN_SEED_NAME', 'Rhayan Lodovice'),
            ],
        ]);
    }
}
