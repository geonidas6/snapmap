<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Models\Tracemap;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;


//id 24279432058340920
//access token MLY|24279432058340920|6d58f7b7d8361c4bea4ef89bf5c4fa7c
//client secret MLY|24279432058340920|5cd04cd3804dfbed797ca3c501560f25
//auth url https://www.mapillary.com/connect?client_id=24279432058340920
class FetchTracemapImages extends Command
{


    protected $signature = 'tracemap:fetch';
    protected $description = 'Récupère une image aléatoire depuis Mapillary avec position et enregistre dans la base';

    public function handle()
    {
        $token = env('MAPILLARY_ACCESS_TOKEN');
        if (!$token) {
            $this->error('Le token Mapillary est manquant dans .env');
            return;
        }

        // Coordonnées aléatoires autour de Paris
        $lat = 48.85 + (mt_rand(-1000, 1000) / 10000);
        $lng = 2.35 + (mt_rand(-1000, 1000) / 10000);

        $this->info("Recherche autour de : $lat, $lng");

        $response = Http::get("https://graph.mapillary.com/images", [
            'access_token' => $token,
//            'fields' => 'id,computed_geometry,thumb_1024_url',
//            'limit' => 1,
            //'closeto' => "$lng,$lat"
        ]);

//         dd($response);
        if (!$response->successful() || empty($response['data'])) {
            $this->warn('Aucune image trouvée.');
            return;
        }

        $image = $response['data'][0];
        $coordinates = $image['computed_geometry']['coordinates'];
        $thumb = $image['thumb_1024_url'];

        // Enregistrement dans tracemaps
        $tracemap = Tracemap::create([
            'latitude' => $coordinates[1],
            'longitude' => $coordinates[0],
        ]);

        // Téléchargement du fichier image
        $path = 'medias/' . $image['id'] . '.jpg';
        file_put_contents(public_path($path), file_get_contents($thumb));

        // Enregistrement dans medias
        Media::create([
            'tracemap_id' => $tracemap->id,
            'file_path' => $path,
            'file_type' => 'image/jpeg',
        ]);

        $this->info("Image enregistrée : $path");
    }
}
