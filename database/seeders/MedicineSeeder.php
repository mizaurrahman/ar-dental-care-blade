<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['brand_name' => 'Napa', 'generic_name' => 'Paracetamol', 'strength' => '500 mg', 'form' => 'Tablet', 'company' => 'Beximco Pharmaceuticals'],
            ['brand_name' => 'Napa Extra', 'generic_name' => 'Paracetamol + Caffeine', 'strength' => '500 mg + 65 mg', 'form' => 'Tablet', 'company' => 'Beximco Pharmaceuticals'],
            ['brand_name' => 'Ace', 'generic_name' => 'Paracetamol', 'strength' => '500 mg', 'form' => 'Tablet', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Seclo', 'generic_name' => 'Omeprazole', 'strength' => '20 mg', 'form' => 'Capsule', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Maxpro', 'generic_name' => 'Esomeprazole', 'strength' => '20 mg', 'form' => 'Capsule', 'company' => 'Renata Limited'],
            ['brand_name' => 'Amoxicillin', 'generic_name' => 'Amoxicillin', 'strength' => '500 mg', 'form' => 'Capsule', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Moxacil', 'generic_name' => 'Amoxicillin', 'strength' => '500 mg', 'form' => 'Capsule', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Ciprocin', 'generic_name' => 'Ciprofloxacin', 'strength' => '500 mg', 'form' => 'Tablet', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Azithrocin', 'generic_name' => 'Azithromycin', 'strength' => '500 mg', 'form' => 'Tablet', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Metryl', 'generic_name' => 'Metronidazole', 'strength' => '400 mg', 'form' => 'Tablet', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Flagyl', 'generic_name' => 'Metronidazole', 'strength' => '400 mg', 'form' => 'Tablet', 'company' => 'Sanofi Bangladesh'],
            ['brand_name' => 'Ketorolac', 'generic_name' => 'Ketorolac Tromethamine', 'strength' => '10 mg', 'form' => 'Tablet', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Toradol', 'generic_name' => 'Ketorolac', 'strength' => '10 mg', 'form' => 'Tablet', 'company' => 'Popular Pharmaceuticals'],
            ['brand_name' => 'Ibuprofen', 'generic_name' => 'Ibuprofen', 'strength' => '400 mg', 'form' => 'Tablet', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Flexi', 'generic_name' => 'Aceclofenac', 'strength' => '100 mg', 'form' => 'Tablet', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Napa Extend', 'generic_name' => 'Paracetamol', 'strength' => '665 mg', 'form' => 'Tablet', 'company' => 'Beximco Pharmaceuticals'],
            ['brand_name' => 'Fexo', 'generic_name' => 'Fexofenadine', 'strength' => '120 mg', 'form' => 'Tablet', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Allegra', 'generic_name' => 'Fexofenadine', 'strength' => '120 mg', 'form' => 'Tablet', 'company' => 'Sanofi'],
            ['brand_name' => 'Calbo-D', 'generic_name' => 'Calcium + Vitamin D3', 'strength' => '500 mg + 200 IU', 'form' => 'Tablet', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Oradex', 'generic_name' => 'Chlorhexidine Gluconate', 'strength' => '0.2%', 'form' => 'Mouthwash', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Hexisol', 'generic_name' => 'Chlorhexidine', 'strength' => '0.2%', 'form' => 'Mouthwash', 'company' => 'ACI Limited'],
            ['brand_name' => 'Dentalgel', 'generic_name' => 'Choline Salicylate', 'strength' => '8.7%', 'form' => 'Oral gel', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Betnovate-N', 'generic_name' => 'Betamethasone + Neomycin', 'strength' => '0.1% + 0.5%', 'form' => 'Cream', 'company' => 'GlaxoSmithKline'],
            ['brand_name' => 'Pantonix', 'generic_name' => 'Pantoprazole', 'strength' => '40 mg', 'form' => 'Tablet', 'company' => 'Incepta Pharmaceuticals'],
            ['brand_name' => 'Losectil', 'generic_name' => 'Omeprazole', 'strength' => '20 mg', 'form' => 'Capsule', 'company' => 'Healthcare Pharmaceuticals'],
            ['brand_name' => 'Rivotril', 'generic_name' => 'Clonazepam', 'strength' => '0.5 mg', 'form' => 'Tablet', 'company' => 'Roche'],
            ['brand_name' => 'Sergel', 'generic_name' => 'Esomeprazole', 'strength' => '20 mg', 'form' => 'Capsule', 'company' => 'Healthcare Pharmaceuticals'],
            ['brand_name' => 'Neotack', 'generic_name' => 'Ranitidine', 'strength' => '150 mg', 'form' => 'Tablet', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Monas', 'generic_name' => 'Montelukast', 'strength' => '10 mg', 'form' => 'Tablet', 'company' => 'Square Pharmaceuticals'],
            ['brand_name' => 'Napa Suspension', 'generic_name' => 'Paracetamol', 'strength' => '120 mg/5 ml', 'form' => 'Suspension', 'company' => 'Beximco Pharmaceuticals'],
        ];

        foreach ($items as $item) {
            Medicine::updateOrCreate(
                [
                    'brand_name' => $item['brand_name'],
                    'strength' => $item['strength'],
                    'form' => $item['form'],
                ],
                [...$item, 'source' => 'seed', 'last_synced_at' => now()]
            );
        }
    }
}
