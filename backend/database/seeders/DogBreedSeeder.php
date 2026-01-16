<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DogBreedSeeder extends Seeder
{
    public function run(): void
    {
        $dogsCategory = Category::where('slug', 'dogs')->first();

        if (!$dogsCategory) {
            $this->command->error('Dogs category not found. Run CategorySeeder first.');
            return;
        }

        $breeds = [
            // Popular UK breeds
            ['name' => 'Labrador Retriever', 'slug' => 'labrador-retriever'],
            ['name' => 'French Bulldog', 'slug' => 'french-bulldog'],
            ['name' => 'Cocker Spaniel', 'slug' => 'cocker-spaniel'],
            ['name' => 'English Springer Spaniel', 'slug' => 'english-springer-spaniel'],
            ['name' => 'Bulldog', 'slug' => 'bulldog'],
            ['name' => 'Golden Retriever', 'slug' => 'golden-retriever'],
            ['name' => 'German Shepherd', 'slug' => 'german-shepherd'],
            ['name' => 'Pug', 'slug' => 'pug'],
            ['name' => 'Border Terrier', 'slug' => 'border-terrier'],
            ['name' => 'Staffordshire Bull Terrier', 'slug' => 'staffordshire-bull-terrier'],

            // Additional popular breeds
            ['name' => 'Dachshund', 'slug' => 'dachshund'],
            ['name' => 'Cavalier King Charles Spaniel', 'slug' => 'cavalier-king-charles-spaniel'],
            ['name' => 'Miniature Schnauzer', 'slug' => 'miniature-schnauzer'],
            ['name' => 'Shih Tzu', 'slug' => 'shih-tzu'],
            ['name' => 'Boxer', 'slug' => 'boxer'],
            ['name' => 'Rottweiler', 'slug' => 'rottweiler'],
            ['name' => 'Yorkshire Terrier', 'slug' => 'yorkshire-terrier'],
            ['name' => 'Border Collie', 'slug' => 'border-collie'],
            ['name' => 'Chihuahua', 'slug' => 'chihuahua'],
            ['name' => 'Beagle', 'slug' => 'beagle'],

            // Medium to large breeds
            ['name' => 'Siberian Husky', 'slug' => 'siberian-husky'],
            ['name' => 'Doberman Pinscher', 'slug' => 'doberman-pinscher'],
            ['name' => 'Great Dane', 'slug' => 'great-dane'],
            ['name' => 'Bernese Mountain Dog', 'slug' => 'bernese-mountain-dog'],
            ['name' => 'Saint Bernard', 'slug' => 'saint-bernard'],
            ['name' => 'Newfoundland', 'slug' => 'newfoundland'],
            ['name' => 'Akita', 'slug' => 'akita'],
            ['name' => 'Alaskan Malamute', 'slug' => 'alaskan-malamute'],
            ['name' => 'Samoyed', 'slug' => 'samoyed'],
            ['name' => 'Weimaraner', 'slug' => 'weimaraner'],

            // Small breeds
            ['name' => 'Pomeranian', 'slug' => 'pomeranian'],
            ['name' => 'Maltese', 'slug' => 'maltese'],
            ['name' => 'Bichon Frise', 'slug' => 'bichon-frise'],
            ['name' => 'West Highland White Terrier', 'slug' => 'west-highland-white-terrier'],
            ['name' => 'Jack Russell Terrier', 'slug' => 'jack-russell-terrier'],
            ['name' => 'Papillon', 'slug' => 'papillon'],
            ['name' => 'Boston Terrier', 'slug' => 'boston-terrier'],
            ['name' => 'Havanese', 'slug' => 'havanese'],
            ['name' => 'Pekingese', 'slug' => 'pekingese'],
            ['name' => 'Italian Greyhound', 'slug' => 'italian-greyhound'],

            // Working and herding breeds
            ['name' => 'Australian Shepherd', 'slug' => 'australian-shepherd'],
            ['name' => 'Belgian Malinois', 'slug' => 'belgian-malinois'],
            ['name' => 'Shetland Sheepdog', 'slug' => 'shetland-sheepdog'],
            ['name' => 'Old English Sheepdog', 'slug' => 'old-english-sheepdog'],
            ['name' => 'Collie', 'slug' => 'collie'],
            ['name' => 'Cane Corso', 'slug' => 'cane-corso'],
            ['name' => 'Mastiff', 'slug' => 'mastiff'],
            ['name' => 'Bull Mastiff', 'slug' => 'bull-mastiff'],

            // Sporting breeds
            ['name' => 'Pointer', 'slug' => 'pointer'],
            ['name' => 'Vizsla', 'slug' => 'vizsla'],
            ['name' => 'Irish Setter', 'slug' => 'irish-setter'],
            ['name' => 'English Setter', 'slug' => 'english-setter'],
            ['name' => 'Brittany', 'slug' => 'brittany'],
            ['name' => 'Chesapeake Bay Retriever', 'slug' => 'chesapeake-bay-retriever'],
            ['name' => 'Flat-Coated Retriever', 'slug' => 'flat-coated-retriever'],

            // Hound breeds
            ['name' => 'Greyhound', 'slug' => 'greyhound'],
            ['name' => 'Whippet', 'slug' => 'whippet'],
            ['name' => 'Basset Hound', 'slug' => 'basset-hound'],
            ['name' => 'Bloodhound', 'slug' => 'bloodhound'],
            ['name' => 'Afghan Hound', 'slug' => 'afghan-hound'],
            ['name' => 'Saluki', 'slug' => 'saluki'],

            // Additional terriers
            ['name' => 'Airedale Terrier', 'slug' => 'airedale-terrier'],
            ['name' => 'Bull Terrier', 'slug' => 'bull-terrier'],
            ['name' => 'Scottish Terrier', 'slug' => 'scottish-terrier'],
            ['name' => 'Cairn Terrier', 'slug' => 'cairn-terrier'],
            ['name' => 'Fox Terrier', 'slug' => 'fox-terrier'],

            // Other popular breeds
            ['name' => 'Dalmatian', 'slug' => 'dalmatian'],
            ['name' => 'Chow Chow', 'slug' => 'chow-chow'],
            ['name' => 'Shar Pei', 'slug' => 'shar-pei'],
            ['name' => 'Lhasa Apso', 'slug' => 'lhasa-apso'],
            ['name' => 'Mixed Breed', 'slug' => 'mixed-breed'],
        ];

        foreach ($breeds as $breed) {
            Breed::create([
                'category_id' => $dogsCategory->id,
                'name' => $breed['name'],
                'slug' => $breed['slug'],
                'description' => null,
                'characteristics' => null,
                'average_price_range' => null,
                'is_active' => true,
            ]);
        }
    }
}
