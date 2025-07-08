<?php

namespace App\Http\Controllers;

use App\Models\Tracemap;
use App\Models\Media;
use App\Events\NewTracemapEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;

class TracemapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupère uniquement les tracemaps de moins de 24 heures avec leurs médias associés
        $tracemaps = Tracemap::with('media')
            ->where('created_at', '>=', now()->subHours(24))
            ->get();
        return view('tracemap.index', compact('tracemaps'));
    }

    // La méthode create a été supprimée car nous utilisons maintenant un modal dans la vue index

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valide les données du formulaire
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'content' => 'required|array',
            'content.*' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:10240',
        ]);

        // Crée un nouveau tracemap avec les données validées
        $tracemap = Tracemap::create([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        // Stocke chaque fichier téléversé et crée un enregistrement Media
        foreach ($request->file('content') as $file) {
            // Stocke le fichier dans le disque 'public' au lieu de 'public/tracemaps'
            $path = $file->store('tracemaps', 'public');
            $fileType = $file->getClientMimeType();

            // Crée un nouvel enregistrement Media associé au Tracemap
            Media::create([
                'tracemap_id' => $tracemap->id,
                'file_path' => Storage::url($path),
                'file_type' => $fileType,
            ]);
        }

        // Redirige vers la page d'accueil avec un message de succès
        return redirect()->route('tracemap.index')->with('success', 'Tracemap créé avec succès!');
    }

    /**
     * Store a newly created resource in storage via AJAX.
     * Cette méthode est similaire à store() mais retourne une réponse JSON pour les requêtes AJAX.
     */
    public function storeAjax(Request $request)
    {
        try {
            // Valide les données du formulaire
            $validated = $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'content' => 'required|array',
                'content.*' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:10240',
            ]);

            // Crée un nouveau tracemap avec les données validées
            $tracemap = Tracemap::create([
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);

            $mediaItems = [];

            // Stocke chaque fichier téléversé et crée un enregistrement Media
            foreach ($request->file('content') as $file) {
                // Stocke le fichier dans le disque 'public'
                $path = $file->store('tracemaps', 'public');
                $fileType = $file->getClientMimeType();

                // Crée un nouvel enregistrement Media associé au Tracemap
                $media = Media::create([
                    'tracemap_id' => $tracemap->id,
                    'file_path' => 'storage/' . $path,
                    'file_type' => $fileType,
                ]);

                $mediaItems[] = [
                    'id' => $media->id,
                    'file_path' => $media->file_path,
                    'file_type' => $media->file_type,
                    'is_video' => str_starts_with($fileType, 'video/') ||
                        in_array(pathinfo($path, PATHINFO_EXTENSION), ['mp4', 'mov', 'avi'])
                ];
            }

            // Préparer les données du tracemap pour la réponse et la notification
            $tracemapData = [
                'id' => $tracemap->id,
                'latitude' => $tracemap->latitude,
                'longitude' => $tracemap->longitude,
                'media' => $mediaItems
            ];


            // Envoyer une notification via Pusher
            $this->sendTracemapNotification($tracemapData);

            // Retourne une réponse JSON avec les informations du tracemap créé
            return response()->json([
                'success' => true,
                'message' => 'Tracemap créé avec succès!',
                'tracemap' => $tracemapData
            ]);

        } catch (\Exception $e) {
            // En cas d'erreur, retourne une réponse JSON avec le message d'erreur
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du tracemap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Envoie une notification en temps réel pour informer les autres utilisateurs d'un nouveau tracemap
     * Utilise le système de broadcasting de Laravel pour envoyer l'événement
     *
     * @param array $tracemapData Les données du tracemap à envoyer
     * @return void
     */
    private function sendTracemapNotification(array $tracemapData): void
    {
        try {
            Log::info('Début de l\'envoi de notification en temps réel pour un nouveau tracemap');

            // Préparer les données à envoyer
            $eventData = [
                'message' => 'Un nouveau tracemap a été créé!',
                'tracemap' => $tracemapData
            ];

            Log::info('Données de l\'événement préparées', [
                'event_name' => 'new-tracemap',
                'channel' => 'tracemap-updates',
                'message' => $eventData['message'],
                'tracemap_id' => $tracemapData['id'] ?? 'non défini',
                'media_count' => count($tracemapData['media'] ?? []),
            ]);

            // Utiliser l'API de broadcasting de Laravel pour envoyer l'événement
            broadcast(new \App\Events\NewTracemapEvent($eventData))->toOthers();

            Log::info('Événement de nouveau tracemap diffusé avec succès via Laravel Broadcasting');
        } catch (\Exception $e) {
            // Enregistrer l'erreur mais ne pas interrompre le flux de l'application
            Log::error('Erreur lors de l\'envoi de la notification en temps réel', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
