<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Researcher;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $elena = Researcher::where('email', 'e.rostova@university.edu')->first();
        $sofia = Researcher::where('email', 's.lim@ateneo.edu')->first();
        $bautista = Researcher::where('email', 'r.bautista@nationalmuseum.gov.ph')->first();
        $cruz = Researcher::where('email', 'a.cruz@up.edu.ph')->first();
        $delgado = Researcher::where('email', 'c.delgado@uplb.edu.ph')->first();
        $alcantara = Researcher::where('email', 'k.alcantara@silliman.edu.ph')->first();
        $tanaka = Researcher::where('email', 'h.tanaka@kyoto-u.ac.jp')->first();
        $ocampo = Researcher::where('email', 'b.ocampo@ust.edu.ph')->first();

        $projects = [
            [
                'project_code' => 'PRJ-RAPTOR-2026',
                'title' => 'Philippine Forest Raptor Genetic Connectivity and Radiographic Biomechanics',
                'description' => 'Genome-wide sequencing and high-resolution computed tomography to assess population fragmentation in apex forest accipitrids.',
                'principal_researcher_id' => $elena?->_id,
                'start_date' => '2024-01-01',
                'end_date' => '2026-12-31',
                'status' => 'active',
            ],
            [
                'project_code' => 'PRJ-PELAGIC-2025',
                'title' => 'Sulu-Sulawesi Marine Megafauna Population Genomic Connectivity',
                'description' => 'Mitochondrial and microsatellite genotyping of migratory elasmobranchs and sea turtles passing through protected coral atolls.',
                'principal_researcher_id' => $sofia?->_id,
                'start_date' => '2025-03-01',
                'end_date' => '2027-02-28',
                'status' => 'active',
            ],
            [
                'project_code' => 'PRJ-PITCHER-2024',
                'title' => 'Ultramafic Flora Adaptation and Digestive Proteomics in Endemic Nepenthes',
                'description' => 'Biochemical profiling of pitcher fluid proteolytic enzymes and heavy metal hyperaccumulation in montane serpentine species.',
                'principal_researcher_id' => $bautista?->_id,
                'start_date' => '2023-08-01',
                'end_date' => '2025-07-31',
                'status' => 'completed',
            ],
            [
                'project_code' => 'PRJ-HERP-KARST-2025',
                'title' => 'Cryptic Speciation and Micro-endemism in Palawan Karst Amphibians',
                'description' => 'Acoustic monitoring and multilocus phylogeography of lungless flat-headed frogs and karst-associated geckos.',
                'principal_researcher_id' => $cruz?->_id,
                'start_date' => '2024-06-01',
                'end_date' => '2027-05-31',
                'status' => 'active',
            ],
            [
                'project_code' => 'PRJ-POLLINATOR-2026',
                'title' => 'Archipelagic Island Biogeography of Papilionid Birdwing Butterflies',
                'description' => 'Structural optical iridescence and flight muscle morphometrics of Troides birdwings across elevational gradients.',
                'principal_researcher_id' => $delgado?->_id,
                'start_date' => '2023-01-15',
                'end_date' => '2026-06-30',
                'status' => 'active',
            ],
            [
                'project_code' => 'PRJ-CORAL-RESILIENCE-2027',
                'title' => 'Thermal Bleaching Resilience and Genomic Signatures of Acropora Table Corals',
                'description' => 'Comparative transcriptomics of zooxanthellae clade shifts in pristine Sulu Sea atolls versus degraded inshore reefs.',
                'principal_researcher_id' => $alcantara?->_id,
                'start_date' => '2025-01-01',
                'end_date' => '2027-12-31',
                'status' => 'active',
            ],
            [
                'project_code' => 'PRJ-TARSIUS-ACOUSTIC-2025',
                'title' => 'Ultrasonic Communication and Olfactory Scent Marking in Philippine Tarsiers',
                'description' => 'Field acoustic bio-logging and non-invasive glandular swab metabolomics in Bohol and Samar populations.',
                'principal_researcher_id' => $tanaka?->_id,
                'start_date' => '2024-02-01',
                'end_date' => '2026-01-31',
                'status' => 'active',
            ],
            [
                'project_code' => 'PRJ-DIPTEROCARP-GENOME-2028',
                'title' => 'Mast Fruiting Phenology and Seed Longevity in Critically Endangered Dipterocarps',
                'description' => 'Cryopreservation protocols for recalcitrant Shorea seeds and genomic mapping of drought resistance alleles.',
                'principal_researcher_id' => $ocampo?->_id,
                'start_date' => '2024-09-01',
                'end_date' => '2028-08-31',
                'status' => 'active',
            ],
        ];

        foreach ($projects as $proj) {
            Project::updateOrCreate(
                ['project_code' => $proj['project_code']],
                $proj
            );
        }
    }
}
