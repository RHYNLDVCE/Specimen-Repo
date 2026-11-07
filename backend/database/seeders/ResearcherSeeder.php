<?php

namespace Database\Seeders;

use App\Models\Researcher;
use Illuminate\Database\Seeder;

class ResearcherSeeder extends Seeder
{
    public function run(): void
    {
        $researchers = [
            [
                'name' => 'Dr. Elena Rostova',
                'email' => 'e.rostova@university.edu',
                'institution' => 'International Institute of Biological Sciences',
                'department' => 'Vertebrate Zoology & Functional Morphology',
                'specialization' => 'Avian Comparative Anatomy and Biomechanics',
                'contact_information' => '+1-555-0199-281',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Marcus Vance',
                'email' => 'm.vance@smithsonian.org',
                'institution' => 'Smithsonian Institution Collaborating Center',
                'department' => 'Department of Paleobiology',
                'specialization' => 'Island Dwarfism and Pleistocene Mammalia',
                'contact_information' => '+1-202-633-1000',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Sofia Lim',
                'email' => 's.lim@ateneo.edu',
                'institution' => 'Ateneo Institute of Marine Environmental Sciences',
                'department' => 'Marine Ecology and Genetics',
                'specialization' => 'Chondrichthyes and Marine Megafauna DNA',
                'contact_information' => '+63-2-8426-6001',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Alejandro Cruz',
                'email' => 'a.cruz@up.edu.ph',
                'institution' => 'University of the Philippines Diliman',
                'department' => 'Institute of Biology',
                'specialization' => 'Phylogenetics of Philippine Herpetofauna',
                'contact_information' => '+63-2-8981-8500',
                'status' => 'active',
            ],
            [
                'name' => 'Prof. Carmen Delgado',
                'email' => 'c.delgado@uplb.edu.ph',
                'institution' => 'Museum of Natural History, UP Los Baños',
                'department' => 'Entomology Division',
                'specialization' => 'Lepidoptera Systematics and Pollinator Conservation',
                'contact_information' => '+63-49-536-2884',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Ramon Bautista',
                'email' => 'r.bautista@nationalmuseum.gov.ph',
                'institution' => 'National Museum of the Philippines',
                'department' => 'Botany and National Herbarium Division',
                'specialization' => 'Carnivorous Flora & Ultramafic Endemics',
                'contact_information' => '+63-2-8527-1209',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Theresa Villanueva',
                'email' => 't.villanueva@dlsu.edu.ph',
                'institution' => 'De La Salle University Manila',
                'department' => 'Biology Department',
                'specialization' => 'Fungal Biodiversity, Bioactive Secondary Metabolites',
                'contact_information' => '+63-2-8524-4611',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Kenneth Alcantara',
                'email' => 'k.alcantara@silliman.edu.ph',
                'institution' => 'Silliman University Angelo King Center',
                'department' => 'Marine Laboratory and Coral Reef Studies',
                'specialization' => 'Scleractinian Coral Taxonomy and Siderastrea Genomics',
                'contact_information' => '+63-35-422-6002',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Hiroshi Tanaka',
                'email' => 'h.tanaka@kyoto-u.ac.jp',
                'institution' => 'Kyoto University Center for Southeast Asian Studies',
                'department' => 'Primatology and Ecological Anthropology',
                'specialization' => 'Primate Vocalizations and Tarsius Social Structure',
                'contact_information' => '+81-75-753-7531',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Danielle Dubois',
                'email' => 'd.dubois@mnhn.fr',
                'institution' => 'Muséum National d\'Histoire Naturelle Paris',
                'department' => 'Systematics and Evolution',
                'specialization' => 'Malacology and Cone Snail Peptidomics',
                'contact_information' => '+33-1-40-79-30-00',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Vicente Santos Jr.',
                'email' => 'v.santos@msi.upd.edu.ph',
                'institution' => 'Marine Science Institute, UP Diliman',
                'department' => 'Fisheries Biology and Larval Ecology',
                'specialization' => 'Sardinella tawilis Conservation Limnology',
                'contact_information' => '+63-2-8922-3921',
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Beatrice Ocampo',
                'email' => 'b.ocampo@ust.edu.ph',
                'institution' => 'University of Santo Tomas',
                'department' => 'Department of Biological Sciences',
                'specialization' => 'Dipterocarp Forest Regeneration and Seed Physiology',
                'contact_information' => '+63-2-8786-1611',
                'status' => 'active',
            ],
        ];

        foreach ($researchers as $res) {
            Researcher::updateOrCreate(
                ['email' => $res['email']],
                $res
            );
        }
    }
}
