<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Collection;
use App\Models\Location;
use App\Models\Project;
use App\Models\Specimen;
use App\Models\SpecimenDocument;
use App\Models\SpecimenImage;
use App\Models\Taxon;
use Illuminate\Database\Seeder;

class SpecimenSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::where('role', 'super_admin')->first();

        // Fetch Taxa
        $taxa = Taxon::all()->keyBy('scientific_name');

        // Fetch Locations
        $locations = Location::all()->keyBy('name');

        // Fetch Collections
        $collections = Collection::all()->keyBy('name');

        // Fetch Projects
        $projects = Project::all()->keyBy('project_code');

        // 1. Foundation Specimens (guaranteed codes for tests & loans)
        $specimens = [
            [
                'specimen_code' => 'SPEC-2026-ORN-001',
                'catalog_number' => 'NM-AVES-2026-081',
                'scientific_name' => 'Pithecophaga jefferyi',
                'common_name' => 'Philippine Eagle',
                'kingdom' => 'Animalia',
                'phylum' => 'Chordata',
                'class' => 'Aves',
                'order' => 'Accipitriformes',
                'family' => 'Accipitridae',
                'genus' => 'Pithecophaga',
                'species' => 'jefferyi',
                'taxon_id' => $taxa['Pithecophaga jefferyi']?->_id,
                'collection_id' => $collections['Philippine Avian Biodiversity Survey']?->_id,
                'project_id' => $projects['PRJ-RAPTOR-2026']?->_id,
                'collector' => 'Dr. Maria Santos',
                'collection_date' => '2024-04-12',
                'location_id' => $locations['Mount Apo Natural Park']?->_id,
                'latitude' => 6.9880,
                'longitude' => 125.2715,
                'habitat' => 'Primary montane mossy canopy',
                'description' => 'Adult male salvage specimen; complete skeletal preparation and chest muscle tissue in cryo.',
                'preservation_method' => 'skeletal',
                'repository_location' => 'Osteology Room, Cabinet 04, Shelf B',
                'status' => 'loaned',
                'created_by' => $admin?->_id,
            ],
            [
                'specimen_code' => 'SPEC-2024-MAM-005',
                'catalog_number' => 'NM-MAM-2024-019',
                'scientific_name' => 'Bubalus mindorensis',
                'common_name' => 'Tamaraw',
                'kingdom' => 'Animalia',
                'phylum' => 'Chordata',
                'class' => 'Mammalia',
                'order' => 'Artiodactyla',
                'family' => 'Bovidae',
                'genus' => 'Bubalus',
                'species' => 'mindorensis',
                'taxon_id' => $taxa['Bubalus mindorensis']?->_id,
                'collection_id' => $collections['Historical Island Mammalia Heritage Collection']?->_id,
                'project_id' => null,
                'collector' => 'Field Ranger Patrol Unit',
                'collection_date' => '2023-09-18',
                'location_id' => $locations['Central Specimen Cryo & Osteology Vault']?->_id,
                'latitude' => 12.8123,
                'longitude' => 121.1456,
                'habitat' => 'Highland bamboo forest and secondary grassland',
                'description' => 'Mature skull with intact v-shaped horns and dental arcade.',
                'preservation_method' => 'dry',
                'repository_location' => 'Mammalogy Wing, Rack 12',
                'status' => 'loaned',
                'created_by' => $admin?->_id,
            ],
            [
                'specimen_code' => 'SPEC-2025-REP-003',
                'catalog_number' => 'NM-HERP-2025-004',
                'scientific_name' => 'Crocodylus mindorensis',
                'common_name' => 'Philippine Freshwater Crocodile',
                'kingdom' => 'Animalia',
                'phylum' => 'Chordata',
                'class' => 'Reptilia',
                'order' => 'Crocodilia',
                'family' => 'Crocodylidae',
                'genus' => 'Crocodylus',
                'species' => 'mindorensis',
                'taxon_id' => $taxa['Crocodylus mindorensis']?->_id,
                'collection_id' => $collections['Historical Island Mammalia Heritage Collection']?->_id,
                'project_id' => null,
                'collector' => 'Conservation Marsh Survey Unit',
                'collection_date' => '2024-01-20',
                'location_id' => $locations['Northern Sierra Madre Natural Park']?->_id,
                'latitude' => 17.0545,
                'longitude' => 122.3810,
                'habitat' => 'Riverine marsh with submerged woody debris',
                'description' => 'Sub-adult specimen fluid preserved in buffered formalin-ethanol solution.',
                'preservation_method' => 'wet',
                'repository_location' => 'Wet Storage Tank WT-02',
                'status' => 'loaned',
                'created_by' => $admin?->_id,
            ],
            [
                'specimen_code' => 'SPEC-2025-MAM-010',
                'catalog_number' => 'NM-MAM-2025-014',
                'scientific_name' => 'Tarsius syrichta',
                'common_name' => 'Philippine Tarsier',
                'kingdom' => 'Animalia',
                'phylum' => 'Chordata',
                'class' => 'Mammalia',
                'order' => 'Primates',
                'family' => 'Tarsiidae',
                'genus' => 'Tarsius',
                'species' => 'syrichta',
                'taxon_id' => $taxa['Tarsius syrichta']?->_id,
                'collection_id' => $collections['Historical Island Mammalia Heritage Collection']?->_id,
                'project_id' => $projects['PRJ-TARSIUS-ACOUSTIC-2025']?->_id,
                'collector' => 'Wildlife Sanctuary Team',
                'collection_date' => '2023-11-05',
                'location_id' => $locations['Rajah Sikatuna Protected Landscape']?->_id,
                'latitude' => 9.7168,
                'longitude' => 124.1170,
                'habitat' => 'Secondary lowland dense brushwood',
                'description' => 'Fluid-preserved whole adult male specimen in 70% ethanol.',
                'preservation_method' => 'wet',
                'repository_location' => 'Wet Jar Rack 02, Jar W-42',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
            [
                'specimen_code' => 'SPEC-2024-BOT-014',
                'catalog_number' => 'UST-HERB-2024-002',
                'scientific_name' => 'Nepenthes rajah',
                'common_name' => 'Kinabalu Pitcher Plant',
                'kingdom' => 'Plantae',
                'phylum' => 'Tracheophyta',
                'class' => 'Magnoliopsida',
                'order' => 'Caryophyllales',
                'family' => 'Nepenthaceae',
                'genus' => 'Nepenthes',
                'species' => 'rajah',
                'taxon_id' => $taxa['Nepenthes rajah']?->_id,
                'collection_id' => $collections['Southeast Asian Montane Flora Archive']?->_id,
                'project_id' => $projects['PRJ-PITCHER-2024']?->_id,
                'collector' => 'Dr. Ramon Bautista',
                'collection_date' => '2024-02-15',
                'location_id' => $locations['Mount Hamiguitan Wildlife Sanctuary']?->_id,
                'latitude' => 6.7210,
                'longitude' => 126.1735,
                'habitat' => 'Peat moss seep on ultramafic ridge',
                'description' => 'Herbarium sheet with pressed pitcher leaf, lid, and inflorescence.',
                'preservation_method' => 'dry',
                'repository_location' => 'Herbarium Case 14, Folder 02',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
            [
                'specimen_code' => 'SPEC-2024-BOT-018',
                'catalog_number' => 'UST-HERB-2024-008',
                'scientific_name' => 'Vanda sanderiana',
                'common_name' => 'Waling-waling',
                'kingdom' => 'Plantae',
                'phylum' => 'Tracheophyta',
                'class' => 'Liliopsida',
                'order' => 'Asparagales',
                'family' => 'Orchidaceae',
                'genus' => 'Vanda',
                'species' => 'sanderiana',
                'taxon_id' => $taxa['Vanda sanderiana']?->_id,
                'collection_id' => $collections['Southeast Asian Montane Flora Archive']?->_id,
                'project_id' => null,
                'collector' => 'Botanical Field Unit',
                'collection_date' => '2024-03-10',
                'location_id' => $locations['Mount Apo Natural Park']?->_id,
                'latitude' => 6.9890,
                'longitude' => 125.2720,
                'habitat' => 'Epiphytic on dipterocarp branch at 12m height',
                'description' => 'Pressed holotype voucher specimen with well-preserved petal pigmentation.',
                'preservation_method' => 'dry',
                'repository_location' => 'Herbarium Case 12, Folder 04',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
            [
                'specimen_code' => 'SPEC-2025-MAR-020',
                'catalog_number' => 'MSI-VERT-2025-045',
                'scientific_name' => 'Chelonia mydas',
                'common_name' => 'Green Sea Turtle',
                'kingdom' => 'Animalia',
                'phylum' => 'Chordata',
                'class' => 'Reptilia',
                'order' => 'Testudines',
                'family' => 'Cheloniidae',
                'genus' => 'Chelonia',
                'species' => 'mydas',
                'taxon_id' => $taxa['Chelonia mydas']?->_id,
                'collection_id' => $collections['Sulu Sea Pelagic & Reef Biota Archive']?->_id,
                'project_id' => $projects['PRJ-PELAGIC-2025']?->_id,
                'collector' => 'Marine Biologist Field Unit',
                'collection_date' => '2025-03-01',
                'location_id' => $locations['Tubbataha Reefs Natural Park']?->_id,
                'latitude' => 8.9515,
                'longitude' => 119.9145,
                'habitat' => 'Seagrass bed bordering barrier coral reef',
                'description' => 'Carapace osteology voucher and dried skull.',
                'preservation_method' => 'dry',
                'repository_location' => 'Marine Osteology Unit, Shelf M3',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
            [
                'specimen_code' => 'SPEC-2025-MAR-022',
                'catalog_number' => 'MSI-VERT-2025-052',
                'scientific_name' => 'Rhincodon typus',
                'common_name' => 'Whale Shark',
                'kingdom' => 'Animalia',
                'phylum' => 'Chordata',
                'class' => 'Chondrichthyes',
                'order' => 'Orectolobiformes',
                'family' => 'Rhincodontidae',
                'genus' => 'Rhincodon',
                'species' => 'typus',
                'taxon_id' => $taxa['Rhincodon typus']?->_id,
                'collection_id' => $collections['Sulu Sea Pelagic & Reef Biota Archive']?->_id,
                'project_id' => $projects['PRJ-PELAGIC-2025']?->_id,
                'collector' => 'Dr. Sofia Lim',
                'collection_date' => '2025-03-04',
                'location_id' => $locations['Tubbataha Reefs Natural Park']?->_id,
                'latitude' => 8.9520,
                'longitude' => 119.9150,
                'habitat' => 'Open oceanic pelagic shelf drop-off',
                'description' => 'Sub-epidermal muscle biopsy in ethanol (genetic voucher).',
                'preservation_method' => 'frozen',
                'repository_location' => 'Cryo-Freezer -80C, Rack 04',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
        ];

        // 2. Programmatic Rich Seed Dataset: Generate 110+ additional diverse specimens
        $taxaList = $taxa->values()->all();
        $locationsList = $locations->values()->all();
        $collectionsList = $collections->values()->all();
        $projectsList = $projects->values()->all();

        $collectors = [
            'Dr. Maria Santos', 'Dr. Sofia Lim', 'Dr. Alejandro Cruz', 'Prof. Carmen Delgado',
            'Dr. Ramon Bautista', 'Dr. Kenneth Alcantara', 'Dr. Beatrice Ocampo',
            'Field Expedition Unit Delta', 'Biodiversity Survey Team North', 'Marine Biology Dive Team Alpha',
            'Ranger Conservation Patrol', 'SEARCA Field Botanists',
        ];

        $preservations = ['wet', 'dry', 'frozen', 'skeletal', 'pinned', 'slide', 'tissue'];
        $statuses = ['active', 'active', 'active', 'active', 'loaned', 'under_review', 'archived'];

        $years = [2021, 2022, 2023, 2024, 2025, 2026];
        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        $specimenIndex = 100;

        foreach ($taxaList as $tx) {
            // Generate 2 to 3 distinct specimens per taxon to ensure wide variety
            for ($rep = 1; $rep <= 2; $rep++) {
                $specimenIndex++;
                $year = $years[($specimenIndex + $rep) % count($years)];
                $month = $months[($specimenIndex * 3) % count($months)];
                $day = str_pad(($specimenIndex % 27) + 1, 2, '0', STR_PAD_LEFT);
                $colDate = "{$year}-{$month}-{$day}";

                $loc = $locationsList[$specimenIndex % count($locationsList)];
                $col = $collectionsList[$specimenIndex % count($collectionsList)];
                $proj = ($specimenIndex % 3 === 0) ? null : $projectsList[$specimenIndex % count($projectsList)];
                $collector = $collectors[$specimenIndex % count($collectors)];

                // Appropriate preservation method based on class / kingdom
                if ($tx->class === 'Insecta') {
                    $pres = ($rep === 1) ? 'pinned' : 'slide';
                    $repLoc = "Entomology Drawer ENT-" . sprintf('%03d', ($specimenIndex % 40) + 1);
                } elseif ($tx->kingdom === 'Plantae') {
                    $pres = ($rep === 1) ? 'dry' : 'tissue';
                    $repLoc = "Herbarium Cabinet H-" . sprintf('%02d', ($specimenIndex % 20) + 1) . ", Folder " . sprintf('%02d', $rep);
                } elseif ($tx->kingdom === 'Fungi') {
                    $pres = ($rep === 1) ? 'dry' : 'slide';
                    $repLoc = "Mycology Cabinet MYC-" . sprintf('%02d', ($specimenIndex % 15) + 1);
                } elseif ($tx->class === 'Aves' || $tx->class === 'Mammalia') {
                    $pres = ($rep === 1) ? 'skeletal' : 'frozen';
                    $repLoc = ($pres === 'skeletal') ? "Osteology Cabinet OST-" . sprintf('%02d', ($specimenIndex % 25) + 1) : "Cryo-Freezer -80C, Box CF-" . sprintf('%02d', ($specimenIndex % 10) + 1);
                } elseif ($tx->class === 'Actinopterygii' || $tx->class === 'Chondrichthyes' || $tx->class === 'Amphibia' || $tx->class === 'Reptilia') {
                    $pres = ($rep === 1) ? 'wet' : 'tissue';
                    $repLoc = ($pres === 'wet') ? "Wet Specimen Jar Rack W-" . sprintf('%02d', ($specimenIndex % 30) + 1) . ", Jar #" . $specimenIndex : "Cryo-Freezer Biobank Rack B-" . sprintf('%02d', ($specimenIndex % 10) + 1);
                } else {
                    $pres = $preservations[$specimenIndex % count($preservations)];
                    $repLoc = "General Collection Shelf G-" . sprintf('%03d', $specimenIndex);
                }

                $prefix = match ($tx->class) {
                    'Aves' => 'ORN',
                    'Mammalia' => 'MAM',
                    'Reptilia', 'Amphibia' => 'HERP',
                    'Insecta' => 'ENT',
                    'Actinopterygii', 'Chondrichthyes' => 'ICH',
                    default => ($tx->kingdom === 'Plantae') ? 'BOT' : (($tx->kingdom === 'Fungi') ? 'MYC' : 'INV'),
                };

                $spCode = "SPEC-{$year}-{$prefix}-" . sprintf('%04d', $specimenIndex);
                $catNum = "NM-{$prefix}-{$year}-" . sprintf('%04d', $specimenIndex);

                $status = $statuses[$specimenIndex % count($statuses)];

                $specimens[] = [
                    'specimen_code' => $spCode,
                    'catalog_number' => $catNum,
                    'scientific_name' => $tx->scientific_name,
                    'common_name' => $tx->common_name,
                    'kingdom' => $tx->kingdom,
                    'phylum' => $tx->phylum,
                    'class' => $tx->class,
                    'order' => $tx->order,
                    'family' => $tx->family,
                    'genus' => $tx->genus,
                    'species' => $tx->species,
                    'taxon_id' => $tx->_id,
                    'collection_id' => $col?->_id,
                    'project_id' => $proj?->_id,
                    'collector' => $collector,
                    'collection_date' => $colDate,
                    'location_id' => $loc?->_id,
                    'latitude' => round($loc->latitude + (mt_rand(-50, 50) / 10000), 5),
                    'longitude' => round($loc->longitude + (mt_rand(-50, 50) / 10000), 5),
                    'habitat' => $loc->habitat,
                    'description' => "Voucher specimen of {$tx->scientific_name} ({$tx->common_name}) gathered during systematic biodiversity inventory at {$loc->name}.",
                    'preservation_method' => $pres,
                    'repository_location' => $repLoc,
                    'status' => $status,
                    'created_by' => $admin?->_id,
                ];
            }
        }

        // Insert / Update all specimens
        foreach ($specimens as $sp) {
            $createdSpecimen = Specimen::updateOrCreate(
                ['specimen_code' => $sp['specimen_code']],
                $sp
            );

            // Add realistic SpecimenImage and SpecimenDocument records to ~30% of specimens
            if (crc32($sp['specimen_code']) % 3 === 0) {
                SpecimenImage::firstOrCreate(
                    [
                        'specimen_id' => $createdSpecimen->_id,
                        'caption' => 'High-resolution dorsal voucher photograph of ' . $sp['scientific_name'],
                    ],
                    [
                        'filename' => strtolower($sp['specimen_code']) . '_dorsal_hd.jpg',
                        'path' => 'specimens/images/' . strtolower($sp['specimen_code']) . '_dorsal_hd.jpg',
                        'url' => '/storage/specimens/images/' . strtolower($sp['specimen_code']) . '_dorsal_hd.jpg',
                        'type' => 'dorsal',
                        'file_size' => mt_rand(1500000, 4500000),
                        'mime_type' => 'image/jpeg',
                        'uploaded_by' => $admin?->_id,
                    ]
                );

                SpecimenDocument::firstOrCreate(
                    [
                        'specimen_id' => $createdSpecimen->_id,
                        'description' => 'Original field collection ledger and voucher catalog notes.',
                    ],
                    [
                        'filename' => strtolower($sp['specimen_code']) . '_field_notes.pdf',
                        'path' => 'specimens/documents/' . strtolower($sp['specimen_code']) . '_field_notes.pdf',
                        'url' => '/storage/specimens/documents/' . strtolower($sp['specimen_code']) . '_field_notes.pdf',
                        'document_type' => 'field_notes',
                        'file_size' => mt_rand(450000, 1800000),
                        'mime_type' => 'application/pdf',
                        'uploaded_by' => $admin?->_id,
                    ]
                );
            }
        }
    }
}
