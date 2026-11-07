<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Collection;
use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::where('role', 'super_admin')->first();

        $collections = [
            [
                'name' => 'Philippine Avian Biodiversity Survey',
                'description' => 'Systematic ornithological study voucher collection across major Philippine mountain ranges.',
                'collection_type' => 'Ornithology',
                'collector' => 'National Museum Ornithology Department',
                'date_started' => '2024-01-15',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
            [
                'name' => 'Southeast Asian Montane Flora Archive',
                'description' => 'Herbarium vouchers, seed tissues, and pressed holotypes from high-altitude ultramafic peaks.',
                'collection_type' => 'Botany / Herbarium',
                'collector' => 'National Herbarium Botanical Staff',
                'date_started' => '2023-06-10',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
            [
                'name' => 'Sulu Sea Pelagic & Reef Biota Archive',
                'description' => 'Ichthyological, elasmobranch, and marine invertebrate specimens collected during reef conservation surveys.',
                'collection_type' => 'Ichthyology & Marine Biology',
                'collector' => 'Marine Science Institute',
                'date_started' => '2025-02-01',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
            [
                'name' => 'Historical Island Mammalia Heritage Collection',
                'description' => 'Archival skull and osteological collection of historical endemic mammals collected across biogeographic sub-regions.',
                'collection_type' => 'Mammalogy & Osteology',
                'collector' => 'Heritage Survey Expedition',
                'date_started' => '2020-03-01',
                'status' => 'archived',
                'created_by' => $admin?->_id,
            ],
            [
                'name' => 'National Entomological & Arthropod Biorepository',
                'description' => 'Pinned insects, slide mounts of micro-hymenoptera, and voucher specimens of endemic pollinators.',
                'collection_type' => 'Entomology',
                'collector' => 'Museum of Natural History Entomology Division',
                'date_started' => '2022-08-15',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
            [
                'name' => 'Palawan Karst Herpetofauna Research Collection',
                'description' => 'Fluid-preserved amphibians and squamates gathered from limestone sinkholes, caves, and riverine corridors.',
                'collection_type' => 'Herpetology',
                'collector' => 'Palawan Biodiversity Expedition Group',
                'date_started' => '2023-11-20',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
            [
                'name' => 'Archipelagic Freshwater Ichthyology Biorepository',
                'description' => 'Systematic surveys of endemic freshwater lacustrine and riverine fishes across Luzon and Mindanao basins.',
                'collection_type' => 'Ichthyology',
                'collector' => 'Freshwater Fisheries Research Division',
                'date_started' => '2021-04-12',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
            [
                'name' => 'Tropical Cryptogamic & Mycology Herbarium',
                'description' => 'Macrofungi basidiocarps, lichens, and bryophyte cushions from montane tropical cloud forests.',
                'collection_type' => 'Mycology & Cryptogams',
                'collector' => 'Fungal Biodiversity Working Group',
                'date_started' => '2022-01-08',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
            [
                'name' => 'Deep Sea Benthic & Invertebrate Biorepository',
                'description' => 'Scleractinian corals, deep-sea mollusks, and benthic fauna retrieved during oceanographic research cruises.',
                'collection_type' => 'Invertebrate Zoology',
                'collector' => 'Oceanographic Research Vessel Explorer',
                'date_started' => '2024-05-18',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
            [
                'name' => 'Vertebrate Paleontology & Osteology Vault',
                'description' => 'Fossil specimens, Pleistocene mammal teeth, subfossil remains, and articulated vertebrate comparative skeletons.',
                'collection_type' => 'Paleontology & Comparative Osteology',
                'collector' => 'Archaeology and Paleontology Taskforce',
                'date_started' => '2019-09-01',
                'status' => 'active',
                'created_by' => $admin?->_id,
            ],
        ];

        foreach ($collections as $col) {
            Collection::updateOrCreate(
                ['name' => $col['name']],
                $col
            );
        }
    }
}
