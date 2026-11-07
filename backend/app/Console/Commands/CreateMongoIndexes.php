<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Collection;
use App\Models\Loan;
use App\Models\Location;
use App\Models\Project;
use App\Models\Researcher;
use App\Models\Specimen;
use App\Models\Taxon;
use Illuminate\Console\Command;

class CreateMongoIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:mongo-indexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create optimized MongoDB indexes with explanations for Specimen Repository collections';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting MongoDB index creation for Specimen Repository...');

        // 1. Admins Collection
        $this->comment('Indexing [admins] collection:');
        $adminCol = Admin::raw(fn ($c) => $c);
        $adminCol->createIndex(['email' => 1], ['unique' => true, 'name' => 'admins_email_unique']);
        $this->line('  - admins_email_unique: Unique index on email for O(1) authentication lookups.');

        // 2. Specimens Collection
        $this->comment('Indexing [specimens] collection:');
        $specimenCol = Specimen::raw(fn ($c) => $c);
        $specimenCol->createIndex(['specimen_code' => 1], ['unique' => true, 'name' => 'specimens_code_unique']);
        $this->line('  - specimens_code_unique: Enforces system-wide uniqueness of accession codes.');

        $specimenCol->createIndex(['catalog_number' => 1], ['sparse' => true, 'name' => 'specimens_catalog_sparse']);
        $this->line('  - specimens_catalog_sparse: Fast lookup for historical institutional catalog identifiers.');

        $specimenCol->createIndex(['status' => 1, 'created_at' => -1], ['name' => 'specimens_status_created_compound']);
        $this->line('  - specimens_status_created_compound: Optimizes primary dashboard query and status-filtered pagination.');

        $specimenCol->createIndex(['collection_id' => 1], ['name' => 'specimens_collection_fk']);
        $this->line('  - specimens_collection_fk: Speeds up foreign key resolution for collection specimens list.');

        $specimenCol->createIndex(['taxon_id' => 1], ['name' => 'specimens_taxon_fk']);
        $this->line('  - specimens_taxon_fk: Accelerates taxonomic classification filtering and hierarchy queries.');

        $specimenCol->createIndex(['location_id' => 1], ['name' => 'specimens_location_fk']);
        $this->line('  - specimens_location_fk: Optimizes queries by geographical collecting sites.');

        $specimenCol->createIndex(['project_id' => 1], ['sparse' => true, 'name' => 'specimens_project_fk']);
        $this->line('  - specimens_project_fk: Fast retrieval of specimens linked to specific research initiatives.');

        $specimenCol->createIndex([
            'scientific_name' => 'text',
            'common_name' => 'text',
            'genus' => 'text',
            'species' => 'text'
        ], ['name' => 'specimens_taxonomic_text_idx']);
        $this->line('  - specimens_taxonomic_text_idx: High-performance full-text search across binomial and vernacular names.');

        // 3. Taxa Collection
        $this->comment('Indexing [taxa] collection:');
        $taxaCol = Taxon::raw(fn ($c) => $c);
        $taxaCol->createIndex(['scientific_name' => 1], ['unique' => true, 'name' => 'taxa_scientific_unique']);
        $this->line('  - taxa_scientific_unique: Ensures valid biological nomenclature uniqueness.');

        $taxaCol->createIndex(['kingdom' => 1, 'family' => 1], ['name' => 'taxa_hierarchy_compound']);
        $this->line('  - taxa_hierarchy_compound: Optimizes tree traversal and kingdom-wide taxonomic queries.');

        // 4. Collections Collection
        $this->comment('Indexing [collections] collection:');
        $colCol = Collection::raw(fn ($c) => $c);
        $colCol->createIndex(['name' => 1], ['unique' => true, 'name' => 'collections_name_unique']);
        $this->line('  - collections_name_unique: Prevents duplicate collection designations.');

        $colCol->createIndex(['status' => 1], ['name' => 'collections_status_idx']);
        $this->line('  - collections_status_idx: Quick filtering for active vs archived archival collections.');

        // 5. Researchers Collection
        $this->comment('Indexing [researchers] collection:');
        $resCol = Researcher::raw(fn ($c) => $c);
        $resCol->createIndex(['email' => 1], ['unique' => true, 'name' => 'researchers_email_unique']);
        $this->line('  - researchers_email_unique: Unique contact index avoiding duplicate investigator profiles.');

        // 6. Projects Collection
        $this->comment('Indexing [projects] collection:');
        $projCol = Project::raw(fn ($c) => $c);
        $projCol->createIndex(['project_code' => 1], ['unique' => true, 'name' => 'projects_code_unique']);
        $this->line('  - projects_code_unique: Uniqueness for research grant/project identification numbers.');

        // 7. Loans Collection
        $this->comment('Indexing [loans] collection:');
        $loanCol = Loan::raw(fn ($c) => $c);
        $loanCol->createIndex(['specimen_id' => 1, 'status' => 1], ['name' => 'loans_specimen_status_idx']);
        $this->line('  - loans_specimen_status_idx: Real-time verification of active/approved loans to prevent duplicate loaning.');

        $loanCol->createIndex(['expected_return_date' => 1, 'status' => 1], ['name' => 'loans_overdue_tracker_idx']);
        $this->line('  - loans_overdue_tracker_idx: Powers automated overdue loan detection and reminder reports.');

        // 8. Audit Logs Collection
        $this->comment('Indexing [audit_logs] collection:');
        $auditCol = AuditLog::raw(fn ($c) => $c);
        $auditCol->createIndex(['created_at' => -1], ['name' => 'audit_created_desc']);
        $auditCol->createIndex(['action' => 1, 'created_at' => -1], ['name' => 'audit_action_compound']);
        $this->line('  - audit_created_desc & audit_action_compound: Fast chronological timeline and filtered activity auditing.');

        $this->info('All MongoDB indexes successfully applied!');

        return Command::SUCCESS;
    }
}
