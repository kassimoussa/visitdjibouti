<?php
require_once 'vendor/autoload.php';

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Vérifier les médias
$totalMedia = App\Models\Media::count();
$imagesType = App\Models\Media::where('type', 'images')->count();
$imageType = App\Models\Media::where('type', 'image')->count();
$distinctTypes = App\Models\Media::distinct('type')->pluck('type')->toArray();

echo "Total media: " . $totalMedia . "\n";
echo "Type 'images' (pluriel): " . $imagesType . "\n"; 
echo "Type 'image' (singulier): " . $imageType . "\n";
echo "Types disponibles: " . implode(', ', $distinctTypes) . "\n";

// Échantillon des médias
$sampleMedia = App\Models\Media::limit(5)->get(['id', 'original_name', 'type', 'created_at']);
echo "\nÉchantillon des médias:\n";
foreach ($sampleMedia as $media) {
    echo "ID: {$media->id}, Nom: {$media->original_name}, Type: {$media->type}, Créé: {$media->created_at}\n";
}