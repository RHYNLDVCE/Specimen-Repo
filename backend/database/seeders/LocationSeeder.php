<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Mount Apo Natural Park',
                'description' => 'Highest peak in the Philippines (2,954m) with rich mossy montane forests and geothermal sulfur vents.',
                'province' => 'Davao del Sur',
                'municipality' => 'Bansalan',
                'barangay' => 'Managa',
                'latitude' => 6.9875,
                'longitude' => 125.2711,
                'habitat' => 'Montane mossy tropical cloud forest',
            ],
            [
                'name' => 'Northern Sierra Madre Natural Park',
                'description' => 'Largest contiguous protected lowland rainforest block in the Philippines, bordering the Pacific coast.',
                'province' => 'Isabela',
                'municipality' => 'Palanan',
                'barangay' => 'Didian',
                'latitude' => 17.0543,
                'longitude' => 122.3812,
                'habitat' => 'Primary lowland dipterocarp rainforest',
            ],
            [
                'name' => 'Mount Hamiguitan Wildlife Sanctuary',
                'description' => 'UNESCO World Heritage Site recognized for its unique 225-hectare bonsai pygmy ultramafic forest.',
                'province' => 'Davao Oriental',
                'municipality' => 'San Isidro',
                'barangay' => 'La Union',
                'latitude' => 6.7214,
                'longitude' => 126.1738,
                'habitat' => 'Ultramafic pygmy bonsai forest',
            ],
            [
                'name' => 'Tubbataha Reefs Natural Park',
                'description' => 'Pristine offshore coral atoll marine sanctuary located at the center of the Sulu Sea.',
                'province' => 'Palawan',
                'municipality' => 'Cagayancillo',
                'barangay' => 'Atoll North',
                'latitude' => 8.9510,
                'longitude' => 119.9140,
                'habitat' => 'Coral reef crest, drop-off walls, and pelagic waters',
            ],
            [
                'name' => 'Central Specimen Cryo & Osteology Vault',
                'description' => 'University central natural history research building with climate-controlled specimen freezers and vaults.',
                'province' => 'Metro Manila',
                'municipality' => 'Quezon City',
                'barangay' => 'UP Campus',
                'latitude' => 14.6538,
                'longitude' => 121.0685,
                'habitat' => 'Indoor university laboratory & repository facility',
            ],
            [
                'name' => 'Puerto Princesa Subterranean River National Park',
                'description' => 'World Heritage subterranean river flowing through an expansive karst mountain landscape into St. Paul Bay.',
                'province' => 'Palawan',
                'municipality' => 'Puerto Princesa',
                'barangay' => 'Cabayugan',
                'latitude' => 10.1667,
                'longitude' => 118.9167,
                'habitat' => 'Limestone karst forest, caverns, and tidal river',
            ],
            [
                'name' => 'Mount Pulag National Park',
                'description' => 'Third-highest peak in the Philippines (2,928m) renowned for dwarf bamboo grasslands and sea of clouds.',
                'province' => 'Benguet',
                'municipality' => 'Kabayan',
                'barangay' => 'Babadac',
                'latitude' => 16.5979,
                'longitude' => 120.8992,
                'habitat' => 'Alpine dwarf bamboo grassland and mossy pine forest',
            ],
            [
                'name' => 'Mount Makiling Forest Reserve',
                'description' => 'Dormant stratovolcano and long-standing biodiversity research sanctuary administered by UPLB.',
                'province' => 'Laguna',
                'municipality' => 'Los Baños',
                'barangay' => 'Batong Malake',
                'latitude' => 14.1333,
                'longitude' => 121.2000,
                'habitat' => 'Secondary to primary volcanic mid-montane rainforest',
            ],
            [
                'name' => 'Apo Reef Natural Park',
                'description' => 'The second largest contiguous coral reef system in the world, covering 34 square kilometers.',
                'province' => 'Occidental Mindoro',
                'municipality' => 'Sablayan',
                'barangay' => 'Apo Island',
                'latitude' => 12.6667,
                'longitude' => 120.4667,
                'habitat' => 'Submerged oceanic coral reef platform and mangrove lagoon',
            ],
            [
                'name' => 'Batanes Protected Landscapes and Seascapes',
                'description' => 'Windswept northernmost volcanic archipelago featuring rolling pasture knolls and littoral cliffs.',
                'province' => 'Batanes',
                'municipality' => 'Basco',
                'barangay' => 'Ivana Coast',
                'latitude' => 20.4485,
                'longitude' => 121.9702,
                'habitat' => 'Subtropical littoral sea cliffs and windswept grasslands',
            ],
            [
                'name' => 'Rajah Sikatuna Protected Landscape',
                'description' => 'Largest remaining tract of natural lowland forest in Bohol, situated over karst limestone terrain.',
                'province' => 'Bohol',
                'municipality' => 'Bilar',
                'barangay' => 'Riverside',
                'latitude' => 9.7167,
                'longitude' => 124.1167,
                'habitat' => 'Semi-evergreen limestone karst forest',
            ],
            [
                'name' => 'Mount Kitanglad Range Natural Park',
                'description' => 'Ancestral domain range in northern Mindanao hosting nesting territories for Philippine Eagles.',
                'province' => 'Bukidnon',
                'municipality' => 'Impasugong',
                'barangay' => 'La Fortuna',
                'latitude' => 8.1333,
                'longitude' => 124.9167,
                'habitat' => 'High-altitude subalpine mossy forest',
            ],
            [
                'name' => 'Lake Taal Aquatic Reserve',
                'description' => 'Freshwater caldera lake with an active volcanic island containing sulfur-rich unique limnological habitats.',
                'province' => 'Batangas',
                'municipality' => 'Talisay',
                'barangay' => 'Poblacion Shore',
                'latitude' => 14.0000,
                'longitude' => 120.9833,
                'habitat' => 'Deep freshwater volcanic caldera lake',
            ],
            [
                'name' => 'Sibuyan Island Forest Reserve',
                'description' => 'The "Galápagos of Asia", an isolated island with extraordinary botanical and vertebrate endemism.',
                'province' => 'Romblon',
                'municipality' => 'Magdiwang',
                'barangay' => 'Jao-asan',
                'latitude' => 12.4167,
                'longitude' => 122.5667,
                'habitat' => 'Intact sea-level to summit ultramafic rainforest',
            ],
            [
                'name' => 'Mount Victoria Ultramafic Complex',
                'description' => 'Rugged serpentine-peridotite mountain range in central Palawan hosting hyperendemic carnivorous flora.',
                'province' => 'Palawan',
                'municipality' => 'Narra',
                'barangay' => 'Princess Urduja',
                'latitude' => 9.3833,
                'longitude' => 118.3167,
                'habitat' => 'Serpentine scrub and high-altitude montane heath',
            ],
        ];

        foreach ($locations as $loc) {
            Location::updateOrCreate(
                ['name' => $loc['name']],
                $loc
            );
        }
    }
}
