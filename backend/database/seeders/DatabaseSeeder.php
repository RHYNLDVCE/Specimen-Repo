<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with complete specimen repository data.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            TaxonSeeder::class,
            LocationSeeder::class,
            CollectionSeeder::class,
            ResearcherSeeder::class,
            ProjectSeeder::class,
            SpecimenSeeder::class,
            LoanSeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}
